<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Link;
use App\Models\Project;
use App\Models\QRCode;
use App\Rules\XSSPurifier;
use App\Services\QRCode\QrDynamicCreator;
use App\Services\QRCode\QrImageData;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use RuntimeException;
use Throwable;

class QRCodeController extends Controller
{
    public function __construct(
        private QrDynamicCreator $creator,
    ) {}

    //---------------------------------------------------
    // Getting all the qr-code of user or admin
    public function index(Request $req)
    {
        try {
            $user = auth()->user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $page = $req->per_page ? intval($req->per_page) : 10;

            $qrcodes = QRCode::when(! $SA, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
                ->orderBy('created_at', 'desc')
                ->with(['link', 'project', 'destinationLink'])
                ->paginate($page);

            $limit = AppHelper::limit_checker('qrcodes', $qrcodes->count());

            return Inertia::render('QRCodes/Show', compact('qrcodes', 'limit'));
        } catch (Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    //---------------------------------------------------

    //---------------------------------------------------
    // Accessing qr-code editor page
    public function create()
    {
        $user = auth()->user();
        $current = QRCode::where('user_id', $user->id)->count();
        $limit = AppHelper::limit_checker('qrcodes', $current);
        if ($limit) {
            return back()->with('error', $limit);
        }

        try {
            $projects = Project::where('user_id', $user->id)->get();
            $biolinks = Link::query()
                ->where('user_id', $user->id)
                ->where('link_type', 'biolink')
                ->orderBy('link_name')
                ->get(['id', 'link_name', 'url_name', 'link_type']);
            $shortlinks = Link::query()
                ->where('user_id', $user->id)
                ->where('link_type', 'shortlink')
                ->orderBy('link_name')
                ->get(['id', 'link_name', 'url_name', 'link_type']);

            return Inertia::render('QRCodes/Create', compact('projects', 'biolinks', 'shortlinks'));
        } catch (Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    //---------------------------------------------------

    /**
     * Step 1: create dynamic QR row + public_code (JSON).
     * Client then renders QR for returned public_url and calls finalize.
     */
    public function prepare(Request $req)
    {
        $req->validate([
            'project_id' => ['required'],
            'destination_type' => ['required', 'string', Rule::in([
                QRCode::DESTINATION_EXTERNAL,
                QRCode::DESTINATION_BIOLINK,
                QRCode::DESTINATION_SHORTLINK,
            ])],
            'destination_url' => ['nullable', 'string', 'max:2000'],
            'destination_link_id' => ['nullable', 'integer'],
            'name' => ['nullable', 'string', 'max:100', new XSSPurifier],
            'qr_type' => ['nullable', 'string', 'max:50'],
        ]);

        $user = auth()->user();
        $current = QRCode::where('user_id', $user->id)->count();
        $limit = AppHelper::limit_checker('qrcodes', $current);
        if ($limit) {
            throw ValidationException::withMessages(['project_id' => $limit]);
        }

        $projectQuery = Project::where('id', $req->project_id);
        if (! $user->hasRole('SUPER-ADMIN')) {
            $projectQuery->where('user_id', $user->id);
        }
        $project = $projectQuery->firstOrFail();

        try {
            $qr = $this->creator->createStandalone([
                'user_id' => (int) $project->user_id,
                'project_id' => (int) $project->id,
                'destination_type' => $req->destination_type,
                'destination_url' => $req->destination_url,
                'destination_link_id' => $req->destination_link_id,
                'name' => $req->name,
                'qr_type' => $req->qr_type ?: 'project_qr',
            ]);

            return response()->json([
                'id' => $qr->id,
                'public_code' => $qr->public_code,
                'content' => $qr->content,
                'public_url' => $qr->content,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $th) {
            report($th);

            throw ValidationException::withMessages([
                'destination_type' => 'QR kod oluşturulamadı. Lütfen tekrar deneyin.',
            ]);
        }
    }

    /**
     * Step 2: attach client-rendered image encoding the dynamic redirect URL.
     */
    public function finalize(Request $req, $id)
    {
        $req->validate([
            'qr_code' => ['required', 'string', 'max:'.QrImageData::MAX_LENGTH],
        ]);

        $qr = $this->ownedQrOrFail($id);

        if (! $qr->is_dynamic) {
            abort(404);
        }

        try {
            $this->creator->finalizeImage($qr, $req->qr_code);

            if ($req->expectsJson() || $req->wantsJson() || $req->ajax()) {
                return response()->json([
                    'ok' => true,
                    'id' => $qr->id,
                    'redirect' => '/qrcodes',
                ]);
            }

            return redirect()->to('/qrcodes')->with('success', 'QR kod başarıyla oluşturuldu.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $th) {
            report($th);

            if ($req->expectsJson() || $req->wantsJson() || $req->ajax()) {
                return response()->json(['message' => 'QR görseli kaydedilemedi.'], 422);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    //---------------------------------------------------
    // Legacy-compatible alias: prepare + expects client to finalize separately.
    // Kept for older clients; new Create UI uses /prepare then /finalize.
    public function save_qr(Request $req)
    {
        // If finalize payload (qr_id + qr_code), attach image.
        if ($req->filled('qr_id') && $req->filled('qr_code')) {
            return $this->finalize($req, $req->qr_id);
        }

        return $this->prepare($req);
    }
    //---------------------------------------------------

    /**
     * Bio/Short: prepare dynamic QR bound to link (JSON).
     */
    public function prepare_link_qr(Request $req)
    {
        $req->validate([
            'link_id' => ['required', 'exists:links,id'],
            'qr_type' => ['nullable', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:100', new XSSPurifier],
        ]);

        $user = auth()->user();
        $current = QRCode::where('user_id', $user->id)->count();
        $limit = AppHelper::limit_checker('qrcodes', $current);
        if ($limit) {
            throw ValidationException::withMessages(['link_id' => $limit]);
        }

        $linkQuery = Link::where('id', $req->link_id);
        if (! $user->hasRole('SUPER-ADMIN')) {
            $linkQuery->where('user_id', $user->id);
        }
        $link = $linkQuery->firstOrFail();

        if (! in_array($link->link_type, ['biolink', 'shortlink'], true)) {
            throw ValidationException::withMessages([
                'link_id' => 'Bu link tipi için QR oluşturulamaz.',
            ]);
        }

        try {
            $qr = $this->creator->createForLink([
                'link' => $link,
                'user_id' => (int) $link->user_id,
                'name' => $req->name,
                'qr_type' => $req->qr_type ?: 'link_qr',
            ]);

            return response()->json([
                'id' => $qr->id,
                'public_code' => $qr->public_code,
                'content' => $qr->content,
                'public_url' => $qr->content,
                'link_id' => $link->id,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $th) {
            report($th);

            throw ValidationException::withMessages([
                'link_id' => 'QR kod oluşturulamadı. Lütfen tekrar deneyin.',
            ]);
        }
    }

    //--------------------------------------------------
    // Legacy-compatible link QR entry: prepare (JSON) or finalize when qr_id set.
    public function save_link_qr(Request $req)
    {
        if ($req->filled('qr_id') && $req->filled('qr_code')) {
            $req->validate([
                'qr_code' => ['required', 'string', 'max:'.QrImageData::MAX_LENGTH],
                'qr_id' => ['required'],
            ]);

            $qr = $this->ownedQrOrFail($req->qr_id);
            if (! $qr->is_dynamic) {
                abort(404);
            }

            try {
                $this->creator->finalizeImage($qr, $req->qr_code);

                if ($req->expectsJson() || $req->wantsJson() || $req->ajax()) {
                    return response()->json(['ok' => true, 'id' => $qr->id]);
                }

                return back()->with('success', 'QR kod başarıyla oluşturuldu.');
            } catch (ValidationException $e) {
                throw $e;
            } catch (Throwable $th) {
                report($th);

                return back()->with('error', $th->getMessage());
            }
        }

        return $this->prepare_link_qr($req);
    }
    //--------------------------------------------------

    public function editDestination($id)
    {
        $qr = $this->ownedQrOrFail($id);

        if (! $qr->is_dynamic) {
            abort(404);
        }

        $qr->load(['destinationLink', 'project', 'link']);

        $ownerId = (int) $qr->user_id;
        $biolinks = Link::query()
            ->where('user_id', $ownerId)
            ->where('link_type', 'biolink')
            ->orderBy('link_name')
            ->get(['id', 'link_name', 'url_name', 'link_type']);
        $shortlinks = Link::query()
            ->where('user_id', $ownerId)
            ->where('link_type', 'shortlink')
            ->orderBy('link_name')
            ->get(['id', 'link_name', 'url_name', 'link_type']);

        // Only the QR owner's links — never leak other users' links into props.
        return Inertia::render('QRCodes/EditDestination', [
            'qrcode' => $qr,
            'biolinks' => $biolinks,
            'shortlinks' => $shortlinks,
        ]);
    }

    public function updateDestination(Request $req, $id)
    {
        $qr = $this->ownedQrOrFail($id);

        if (! $qr->is_dynamic) {
            abort(404);
        }

        $req->validate([
            'destination_type' => ['required', 'string', Rule::in([
                QRCode::DESTINATION_EXTERNAL,
                QRCode::DESTINATION_BIOLINK,
                QRCode::DESTINATION_SHORTLINK,
            ])],
            'destination_url' => ['nullable', 'string', 'max:2000'],
            'destination_link_id' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Reject attempts to mutate immutable fields via mass assignment style payloads.
        if ($req->exists('public_code') || $req->exists('content') || $req->exists('img_data')) {
            throw ValidationException::withMessages([
                'public_code' => 'QR kod adresi değiştirilemez.',
            ]);
        }

        $originalCode = $qr->public_code;
        $originalContent = $qr->content;

        try {
            $updated = $this->creator->updateDestination($qr, [
                'destination_type' => $req->destination_type,
                'destination_url' => $req->destination_url,
                'destination_link_id' => $req->destination_link_id,
                'is_active' => $req->exists('is_active') ? $req->boolean('is_active') : null,
            ]);

            // Defense in depth — immutable invariants.
            if ($updated->public_code !== $originalCode || $updated->content !== $originalContent) {
                throw new RuntimeException('Immutable QR fields changed unexpectedly.');
            }

            return redirect()->to('/qrcodes')->with('success', 'QR hedefi güncellendi.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $th) {
            report($th);

            return back()->with('error', $th->getMessage());
        }
    }
    //---------------------------------------------------

    //---------------------------------------------------
    // Delete qr code from bio-link or project
    public function delete($id)
    {
        $qrCode = $this->ownedQrOrFail($id);

        try {
            if ($qrCode->link_id) {
                $linkQuery = Link::where('id', $qrCode->link_id);

                if (! auth()->user()->hasRole('SUPER-ADMIN')) {
                    $linkQuery->where('user_id', auth()->id());
                }

                $linkQuery
                    ->where('qrcode_id', $qrCode->id)
                    ->update(['qrcode_id' => null]);
            }

            $qrCode->delete();

            return back()->with('success', 'QR kod başarıyla silindi.');
        } catch (Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    //---------------------------------------------------

    private function ownedQrOrFail($id): QRCode
    {
        $query = QRCode::where('id', $id);
        if (! auth()->user()->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', auth()->id());
        }

        return $query->firstOrFail();
    }
}
