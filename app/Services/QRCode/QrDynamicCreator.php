<?php

namespace App\Services\QRCode;

use App\Models\Link;
use App\Models\QRCode;
use App\Support\SafeUrl;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class QrDynamicCreator
{
    /** Minimal 1x1 PNG — presentation placeholder until client finalize. */
    public const PLACEHOLDER_IMG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    public function __construct(
        private QrPublicCodeGenerator $codes,
        private QrDestinationResolver $destinations,
    ) {}

    public static function publicRedirectUrl(string $publicCode): string
    {
        return rtrim((string) config('app.url'), '/').'/q/'.$publicCode;
    }

    /**
     * Create a standalone (project) dynamic QR. Client content is ignored.
     *
     * @param  array{destination_type: string, destination_url?: ?string, destination_link_id?: ?int, project_id: int, user_id: int, name?: ?string, qr_type?: ?string, img_data?: ?string}  $data
     */
    public function createStandalone(array $data): QRCode
    {
        return DB::transaction(function () use ($data) {
            $qr = new QRCode;
            $qr->user_id = (int) $data['user_id'];
            $qr->project_id = (int) $data['project_id'];
            $qr->link_id = null;
            $qr->name = isset($data['name']) && is_string($data['name']) && trim($data['name']) !== ''
                ? trim($data['name'])
                : null;
            $qr->qr_type = $data['qr_type'] ?? 'project_qr';
            $qr->is_dynamic = true;
            $qr->is_active = true;

            $type = strtolower(trim((string) $data['destination_type']));
            $destinationUrl = $data['destination_url'] ?? null;
            $destinationLinkId = isset($data['destination_link_id']) ? (int) $data['destination_link_id'] : null;

            $this->destinations->assertAssignable($qr, $type, $destinationUrl, $destinationLinkId);
            $this->applyDestination($qr, $type, $destinationUrl, $destinationLinkId);

            $imgData = $data['img_data'] ?? null;
            $qr->img_data = is_string($imgData) && $imgData !== '' ? $imgData : self::PLACEHOLDER_IMG;

            $this->assignCodeAndContent($qr);

            return $qr->fresh(['destinationLink', 'project', 'link']);
        });
    }

    /**
     * Create a Bio/Short link dynamic QR and bind links.qrcode_id.
     *
     * @param  array{link: Link, user_id: int, name?: ?string, qr_type?: ?string, img_data?: ?string}  $data
     */
    public function createForLink(array $data): QRCode
    {
        /** @var Link $incoming */
        $incoming = $data['link'];

        return DB::transaction(function () use ($data, $incoming) {
            $link = Link::query()
                ->where('id', $incoming->id)
                ->lockForUpdate()
                ->first();

            if (! $link) {
                throw ValidationException::withMessages([
                    'link_id' => 'Hedef link bulunamadı.',
                ]);
            }

            if ($link->qrcode_id) {
                throw ValidationException::withMessages([
                    'link_id' => 'Bu link için QR kod zaten var.',
                ]);
            }

            $type = $link->link_type === 'shortlink'
                ? QRCode::DESTINATION_SHORTLINK
                : QRCode::DESTINATION_BIOLINK;

            $qr = new QRCode;
            $qr->user_id = (int) $link->user_id;
            $qr->link_id = $link->id;
            $qr->project_id = null;
            $qr->name = isset($data['name']) && is_string($data['name']) && trim($data['name']) !== ''
                ? trim($data['name'])
                : null;
            $qr->qr_type = $data['qr_type'] ?? 'link_qr';
            $qr->is_dynamic = true;
            $qr->is_active = true;

            $this->destinations->assertAssignable($qr, $type, null, (int) $link->id);
            $this->applyDestination($qr, $type, null, (int) $link->id);

            $imgData = $data['img_data'] ?? null;
            $qr->img_data = is_string($imgData) && $imgData !== '' ? $imgData : self::PLACEHOLDER_IMG;

            $this->assignCodeAndContent($qr);

            $link->qrcode_id = $qr->id;
            $link->save();

            return $qr->fresh(['destinationLink', 'link']);
        });
    }

    /**
     * Attach client-rendered QR image that must encode the dynamic redirect URL.
     * Server cannot verify encoded QR payload; img_data is a validated presentation cache.
     */
    public function finalizeImage(QRCode $qrCode, string $imgData): QRCode
    {
        if (! $qrCode->is_dynamic) {
            throw new RuntimeException('Only dynamic QR images can be finalized.');
        }

        $qrCode->img_data = QrImageData::assertValid($imgData);
        $qrCode->save();

        return $qrCode->fresh();
    }

    /**
     * Update destination (and optional is_active) without touching public_code, content, or img_data.
     *
     * @param  array{destination_type: string, destination_url?: ?string, destination_link_id?: ?int, is_active?: bool}  $data
     */
    public function updateDestination(QRCode $qrCode, array $data): QRCode
    {
        if (! $qrCode->is_dynamic) {
            throw new RuntimeException('Legacy QR destination cannot be edited.');
        }

        return DB::transaction(function () use ($qrCode, $data) {
            $type = strtolower(trim((string) $data['destination_type']));
            $destinationUrl = $data['destination_url'] ?? null;
            $destinationLinkId = array_key_exists('destination_link_id', $data) && $data['destination_link_id'] !== null
                ? (int) $data['destination_link_id']
                : null;

            $this->destinations->assertAssignable($qrCode, $type, $destinationUrl, $destinationLinkId);

            $this->applyDestination($qrCode, $type, $destinationUrl, $destinationLinkId);

            if (array_key_exists('is_active', $data) && $data['is_active'] !== null) {
                $qrCode->is_active = (bool) $data['is_active'];
            }

            // Do not touch public_code, content, or img_data.
            $qrCode->save();

            return $qrCode->fresh(['destinationLink', 'project', 'link']);
        });
    }

    private function applyDestination(
        QRCode $qr,
        string $type,
        ?string $destinationUrl,
        ?int $destinationLinkId
    ): void {
        $qr->destination_type = $type;

        if ($type === QRCode::DESTINATION_EXTERNAL) {
            $canonical = SafeUrl::canonicalize($destinationUrl);
            $qr->destination_url = $canonical;
            $qr->destination_link_id = null;

            return;
        }

        $qr->destination_url = null;
        $qr->destination_link_id = $destinationLinkId;
    }

    private function assignCodeAndContent(QRCode $qr): void
    {
        for ($attempt = 0; $attempt < 16; $attempt++) {
            $code = $this->codes->generate();
            $qr->public_code = $code;
            $qr->content = self::publicRedirectUrl($code);

            try {
                $qr->save();

                return;
            } catch (QueryException $e) {
                $qr->public_code = null;
                $qr->content = '';

                if (! $this->codes->isPublicCodeUniqueViolation($e)) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Unable to assign a unique QR public_code.');
    }
}
