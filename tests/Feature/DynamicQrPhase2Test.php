<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Link;
use App\Models\Project;
use App\Models\QRCode;
use App\Models\User;
use App\Services\QRCode\QrDynamicCreator;
use App\Services\QRCode\QrImageData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DynamicQrPhase2Test extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function userWithRole(string $role = 'PREMIUM'): User
    {
        Role::findOrCreate($role);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makeProject(User $user, string $name = 'Demo Project'): Project
    {
        $project = new Project;
        $project->user_id = $user->id;
        $project->project_name = $name;
        $project->save();

        return $project->fresh();
    }

    private function makeLink(User $user, string $type = 'biolink', ?string $urlName = null): Link
    {
        $link = new Link;
        $link->user_id = $user->id;
        $link->link_name = $type === 'shortlink' ? 'Short Demo' : 'Bio Demo';
        $link->link_type = $type;
        $link->url_name = $urlName ?? ($type.'-'.uniqid());
        $link->external_url = $type === 'shortlink' ? 'https://example.com/target' : null;
        $link->save();

        return $link->fresh();
    }

    private function makeLegacyQr(User $user, array $overrides = []): QRCode
    {
        $qr = new QRCode;
        $qr->user_id = $user->id;
        $qr->qr_type = 'project_qr';
        $qr->content = 'https://example.com/legacy-static';
        $qr->img_data = 'data:image/png;base64,LEGACY';
        $qr->is_dynamic = false;
        $qr->is_active = true;
        $qr->name = 'Legacy QR';

        foreach ($overrides as $key => $value) {
            $qr->{$key} = $value;
        }

        $qr->save();

        return $qr->fresh();
    }

    private function expectedPublicUrl(string $code): string
    {
        return rtrim((string) config('app.url'), '/').'/q/'.$code;
    }

    private function validPngDataUri(): string
    {
        return QrDynamicCreator::PLACEHOLDER_IMG;
    }

    private function validJpegDataUri(): string
    {
        return 'data:image/jpeg;base64,'.base64_encode(QrImageData::JPEG_SIGNATURE.'testdata');
    }

    public function test_standalone_external_dynamic_create(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $response = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/landing',
                'name' => 'Menu QR',
            ]);

        $response->assertOk();
        $payload = $response->json();

        $qr = QRCode::query()->findOrFail($payload['id']);

        $this->assertTrue($qr->is_dynamic);
        $this->assertTrue($qr->is_active);
        $this->assertSame(12, strlen((string) $qr->public_code));
        $this->assertSame($this->expectedPublicUrl($qr->public_code), $qr->content);
        $this->assertSame($this->expectedPublicUrl($qr->public_code), $payload['public_url']);
        $this->assertSame($this->expectedPublicUrl($qr->public_code), $payload['content']);
        $this->assertSame(QRCode::DESTINATION_EXTERNAL, $qr->destination_type);
        $this->assertSame('https://example.com/landing', $qr->destination_url);
        $this->assertNull($qr->destination_link_id);
        $this->assertStringNotContainsString('[object Object]', $qr->content);
    }

    public function test_external_destination_stored_canonical(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'HTTPS://Example.COM/Path',
            ])
            ->assertOk();

        $qr = QRCode::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
        $this->assertSame('https://example.com/Path', $qr->destination_url);
    }

    public function test_own_biolink_destination_create(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $link = $this->makeLink($user, 'biolink');

        $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_BIOLINK,
                'destination_link_id' => $link->id,
            ])
            ->assertOk();

        $qr = QRCode::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
        $this->assertSame(QRCode::DESTINATION_BIOLINK, $qr->destination_type);
        $this->assertSame($link->id, $qr->destination_link_id);
        $this->assertNull($qr->destination_url);
    }

    public function test_other_user_biolink_rejected(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $project = $this->makeProject($owner);
        $foreign = $this->makeLink($other, 'biolink');

        $this->actingAs($owner)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_BIOLINK,
                'destination_link_id' => $foreign->id,
            ])
            ->assertStatus(422);
    }

    public function test_own_shortlink_destination_create(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $link = $this->makeLink($user, 'shortlink');

        $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_SHORTLINK,
                'destination_link_id' => $link->id,
            ])
            ->assertOk();

        $qr = QRCode::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
        $this->assertSame(QRCode::DESTINATION_SHORTLINK, $qr->destination_type);
        $this->assertSame($link->id, $qr->destination_link_id);
    }

    public function test_link_type_mismatch_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $bio = $this->makeLink($user, 'biolink');

        $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_SHORTLINK,
                'destination_link_id' => $bio->id,
            ])
            ->assertStatus(422);
    }

    public function test_bio_qr_create_dynamic_and_binds_link(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'biolink');

        $response = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', [
                'link_id' => $link->id,
                'qr_type' => 'link_qr',
            ]);

        $response->assertOk();
        $qr = QRCode::query()->findOrFail($response->json('id'));

        $this->assertTrue($qr->is_dynamic);
        $this->assertSame(QRCode::DESTINATION_BIOLINK, $qr->destination_type);
        $this->assertSame($link->id, $qr->destination_link_id);
        $this->assertSame($this->expectedPublicUrl($qr->public_code), $qr->content);
        $this->assertSame($qr->id, $link->fresh()->qrcode_id);

        $png = $this->validPngDataUri();
        $this->actingAs($user)
            ->postJson('/qrcodes/'.$qr->id.'/finalize', [
                'qr_code' => $png,
            ])
            ->assertOk();

        $this->assertSame($png, $qr->fresh()->img_data);
    }

    public function test_short_qr_create_dynamic_encoded_value_is_string_url(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'shortlink');

        $response = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', [
                'link_id' => $link->id,
                'qr_type' => 'link_qr',
            ]);

        $response->assertOk();
        $publicUrl = $response->json('public_url');
        $content = $response->json('content');

        $this->assertIsString($publicUrl);
        $this->assertIsString($content);
        $this->assertSame($publicUrl, $content);
        $this->assertStringNotContainsString('[object Object]', $publicUrl);
        $this->assertMatchesRegularExpression('#/q/[A-Za-z0-9]{12}$#', $publicUrl);

        $qr = QRCode::query()->findOrFail($response->json('id'));
        $this->assertTrue($qr->is_dynamic);
        $this->assertSame(QRCode::DESTINATION_SHORTLINK, $qr->destination_type);
        $this->assertSame($link->id, $qr->destination_link_id);
        $this->assertSame($qr->id, $link->fresh()->qrcode_id);
    }

    public function test_create_transaction_rolls_back_on_failure(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'biolink');
        $before = QRCode::query()->count();

        Link::saving(function () {
            throw new RuntimeException('forced link save failure');
        });

        try {
            $this->actingAs($user)
                ->postJson('/qrcodes/prepare/link-qr', [
                    'link_id' => $link->id,
                    'qr_type' => 'link_qr',
                ])
                ->assertStatus(422);
        } finally {
            Link::flushEventListeners();
            // Re-boot model so later tests still work.
            Link::clearBootedModels();
        }

        $this->assertSame($before, QRCode::query()->count());
        $this->assertNull($link->fresh()->qrcode_id);
    }

    public function test_edit_destination_owner_works_and_keeps_immutable_fields(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $prepare = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/old',
            ])
            ->assertOk()
            ->json();

        $qr = QRCode::query()->findOrFail($prepare['id']);
        $code = $qr->public_code;
        $content = $qr->content;
        $img = $qr->img_data;

        $this->actingAs($user)
            ->from('/qrcodes/'.$qr->id.'/destination')
            ->patch('/qrcodes/'.$qr->id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/new-target',
                'is_active' => true,
            ])
            ->assertRedirect('/qrcodes');

        $fresh = $qr->fresh();
        $this->assertSame($code, $fresh->public_code);
        $this->assertSame($content, $fresh->content);
        $this->assertSame($img, $fresh->img_data);
        $this->assertSame('https://example.com/new-target', $fresh->destination_url);

        $this->get('/q/'.$code)
            ->assertStatus(302)
            ->assertRedirect('https://example.com/new-target');
    }

    public function test_edit_destination_other_user_denied(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $project = $this->makeProject($owner);

        $id = $this->actingAs($owner)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/private',
            ])
            ->json('id');

        $this->actingAs($other)
            ->patch('/qrcodes/'.$id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/hacked',
            ])
            ->assertNotFound();

        $this->actingAs($other)
            ->get('/qrcodes/'.$id.'/destination')
            ->assertNotFound();
    }

    public function test_legacy_edit_denied(): void
    {
        $user = $this->userWithRole();
        $legacy = $this->makeLegacyQr($user);

        $this->actingAs($user)
            ->get('/qrcodes/'.$legacy->id.'/destination')
            ->assertNotFound();

        $this->actingAs($user)
            ->patch('/qrcodes/'.$legacy->id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/nope',
            ])
            ->assertNotFound();
    }

    public function test_inactive_toggle_returns_410(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/active',
            ])
            ->json('id');

        $qr = QRCode::query()->findOrFail($id);

        $this->actingAs($user)
            ->patch('/qrcodes/'.$qr->id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/active',
                'is_active' => false,
            ])
            ->assertRedirect('/qrcodes');

        $this->assertFalse($qr->fresh()->is_active);
        $this->get('/q/'.$qr->public_code)->assertStatus(410);
    }

    public function test_create_page_props_exclude_other_user_links(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $this->makeProject($owner);
        $ownBio = $this->makeLink($owner, 'biolink', 'own-bio-'.uniqid());
        $ownShort = $this->makeLink($owner, 'shortlink', 'own-short-'.uniqid());
        $foreignBio = $this->makeLink($other, 'biolink', 'foreign-bio-'.uniqid());
        $foreignShort = $this->makeLink($other, 'shortlink', 'foreign-short-'.uniqid());

        $response = $this->actingAs($owner)->get('/qrcodes/create');
        $response->assertOk();

        $page = $response->original->getData()['page']['props'] ?? null;
        if ($page === null && method_exists($response->original, 'toArray')) {
            $page = $response->inertiaProps ?? null;
        }

        // Inertia response props
        $props = $response->viewData('page')['props'] ?? [];
        if ($props === [] && isset($response->original->getData()['page'])) {
            $props = $response->original->getData()['page']['props'];
        }

        $response->assertInertia(fn ($assert) => $assert
            ->component('QRCodes/Create')
            ->has('biolinks')
            ->has('shortlinks')
            ->where('biolinks', function ($links) use ($ownBio, $foreignBio) {
                $ids = collect($links)->pluck('id')->all();

                return in_array($ownBio->id, $ids, true)
                    && ! in_array($foreignBio->id, $ids, true);
            })
            ->where('shortlinks', function ($links) use ($ownShort, $foreignShort) {
                $ids = collect($links)->pluck('id')->all();

                return in_array($ownShort->id, $ids, true)
                    && ! in_array($foreignShort->id, $ids, true);
            })
        );
    }

    public function test_existing_legacy_qr_untouched_and_listed(): void
    {
        $user = $this->userWithRole();
        $legacy = $this->makeLegacyQr($user, [
            'content' => 'https://example.com/keep-me',
            'img_data' => 'data:image/png;base64,KEEP',
        ]);

        $this->actingAs($user)
            ->get('/qrcodes')
            ->assertOk()
            ->assertInertia(fn ($assert) => $assert
                ->component('QRCodes/Show')
                ->has('qrcodes.data', 1)
            );

        $fresh = $legacy->fresh();
        $this->assertFalse($fresh->is_dynamic);
        $this->assertNull($fresh->public_code);
        $this->assertSame('https://example.com/keep-me', $fresh->content);
        $this->assertSame('data:image/png;base64,KEEP', $fresh->img_data);
    }

    public function test_dynamic_delete_nulls_link_qrcode_id(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'biolink');

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', [
                'link_id' => $link->id,
            ])
            ->json('id');

        $this->assertSame($id, $link->fresh()->qrcode_id);

        $this->actingAs($user)
            ->from('/qrcodes')
            ->delete('/qrcodes/delete/'.$id)
            ->assertRedirect();

        $this->assertSoftDeleted('qrcodes', ['id' => $id]);
        $this->assertNull($link->fresh()->qrcode_id);
    }

    public function test_unsafe_external_edit_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/ok',
            ])
            ->json('id');

        $this->actingAs($user)
            ->from('/qrcodes/'.$id.'/destination')
            ->patch('/qrcodes/'.$id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors('destination_url');
    }

    public function test_qr_loop_target_edit_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/ok',
            ])
            ->json('id');

        $qr = QRCode::query()->findOrFail($id);
        $loopUrl = $this->expectedPublicUrl($qr->public_code);

        $this->actingAs($user)
            ->from('/qrcodes/'.$id.'/destination')
            ->patch('/qrcodes/'.$id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => $loopUrl,
            ])
            ->assertSessionHasErrors('destination_url');
    }

    public function test_public_code_and_content_rejected_in_edit_payload(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/ok',
            ])
            ->json('id');

        $this->actingAs($user)
            ->from('/qrcodes/'.$id.'/destination')
            ->patch('/qrcodes/'.$id.'/destination', [
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/ok',
                'public_code' => 'HACKEDHACKED',
                'content' => 'https://evil.example/q/HACKEDHACKED',
            ])
            ->assertSessionHasErrors('public_code');
    }

    public function test_client_content_ignored_on_prepare(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $response = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/real',
                'content' => 'https://evil.example/spoof',
            ])
            ->assertOk();

        $qr = QRCode::query()->findOrFail($response->json('id'));
        $this->assertSame($this->expectedPublicUrl($qr->public_code), $qr->content);
        $this->assertNotSame('https://evil.example/spoof', $qr->content);
    }

    public function test_finalize_updates_img_only(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $prepare = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/img',
            ])
            ->json();

        $qr = QRCode::query()->findOrFail($prepare['id']);
        $code = $qr->public_code;
        $content = $qr->content;

        $png = $this->validPngDataUri();
        $this->actingAs($user)
            ->postJson('/qrcodes/'.$qr->id.'/finalize', [
                'qr_code' => $png,
            ])
            ->assertOk();

        $fresh = $qr->fresh();
        $this->assertSame($code, $fresh->public_code);
        $this->assertSame($content, $fresh->content);
        $this->assertSame($png, $fresh->img_data);
    }

    public function test_valid_png_finalize(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/png',
            ])
            ->json('id');

        $png = $this->validPngDataUri();
        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', ['qr_code' => $png])
            ->assertOk();

        $this->assertSame($png, QRCode::query()->findOrFail($id)->img_data);
    }

    public function test_valid_jpeg_finalize(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/jpeg',
            ])
            ->json('id');

        $jpeg = $this->validJpegDataUri();
        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', ['qr_code' => $jpeg])
            ->assertOk();

        $this->assertSame($jpeg, QRCode::query()->findOrFail($id)->img_data);
    }

    public function test_svg_finalize_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/svg',
            ])
            ->json('id');

        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', [
                'qr_code' => 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('qr_code');
    }

    public function test_html_finalize_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/html',
            ])
            ->json('id');

        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', [
                'qr_code' => 'data:text/html;base64,'.base64_encode('<html><script>alert(1)</script></html>'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('qr_code');
    }

    public function test_malformed_base64_finalize_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/bad64',
            ])
            ->json('id');

        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', [
                'qr_code' => 'data:image/png;base64,@@@@not-base64',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('qr_code');
    }

    public function test_oversized_finalize_rejected(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/huge',
            ])
            ->json('id');

        $huge = 'data:image/png;base64,'.str_repeat('A', QrImageData::MAX_LENGTH);

        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', [
                'qr_code' => $huge,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('qr_code');
    }

    public function test_finalize_other_user_denied(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $project = $this->makeProject($owner);
        $id = $this->actingAs($owner)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/idor',
            ])
            ->json('id');

        $this->actingAs($other)
            ->postJson('/qrcodes/'.$id.'/finalize', [
                'qr_code' => $this->validPngDataUri(),
            ])
            ->assertNotFound();
    }

    public function test_finalize_legacy_denied(): void
    {
        $user = $this->userWithRole();
        $legacy = $this->makeLegacyQr($user);

        $this->actingAs($user)
            ->postJson('/qrcodes/'.$legacy->id.'/finalize', [
                'qr_code' => $this->validPngDataUri(),
            ])
            ->assertNotFound();
    }

    public function test_finalize_soft_deleted_denied(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);
        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/gone',
            ])
            ->json('id');

        QRCode::query()->findOrFail($id)->delete();

        $this->actingAs($user)
            ->postJson('/qrcodes/'.$id.'/finalize', [
                'qr_code' => $this->validPngDataUri(),
            ])
            ->assertNotFound();
    }

    public function test_duplicate_bio_prepare_rejected_without_new_row(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'biolink');

        $first = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', ['link_id' => $link->id])
            ->assertOk()
            ->json();

        $count = QRCode::query()->where('user_id', $user->id)->count();
        $bound = $link->fresh()->qrcode_id;

        $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', ['link_id' => $link->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('link_id');

        $this->assertSame($count, QRCode::query()->where('user_id', $user->id)->count());
        $this->assertSame($bound, $link->fresh()->qrcode_id);
        $this->assertSame($first['id'], $bound);
    }

    public function test_duplicate_short_prepare_rejected_without_new_row(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'shortlink');

        $first = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', ['link_id' => $link->id])
            ->assertOk()
            ->json();

        $count = QRCode::query()->where('user_id', $user->id)->count();
        $bound = $link->fresh()->qrcode_id;

        $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', ['link_id' => $link->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('link_id');

        $this->assertSame($count, QRCode::query()->where('user_id', $user->id)->count());
        $this->assertSame($bound, $link->fresh()->qrcode_id);
        $this->assertSame($first['id'], $bound);
    }

    public function test_prepare_only_public_redirect_works(): void
    {
        $user = $this->userWithRole();
        $project = $this->makeProject($user);

        $payload = $this->actingAs($user)
            ->postJson('/qrcodes/prepare', [
                'project_id' => $project->id,
                'destination_type' => QRCode::DESTINATION_EXTERNAL,
                'destination_url' => 'https://example.com/prepare-only',
            ])
            ->assertOk()
            ->json();

        $qr = QRCode::query()->findOrFail($payload['id']);
        $this->assertSame(QrDynamicCreator::PLACEHOLDER_IMG, $qr->img_data);

        $this->get('/q/'.$qr->public_code)
            ->assertStatus(302)
            ->assertRedirect('https://example.com/prepare-only');
    }

    public function test_bio_slug_change_keeps_same_qr_redirect(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'biolink', 'old-bio-slug');

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', ['link_id' => $link->id])
            ->json('id');

        $qr = QRCode::query()->findOrFail($id);
        $code = $qr->public_code;
        $content = $qr->content;

        $link->url_name = 'new-bio-slug';
        $link->save();

        $fresh = $qr->fresh();
        $this->assertSame($code, $fresh->public_code);
        $this->assertSame($content, $fresh->content);
        $this->assertSame($link->id, $fresh->destination_link_id);

        $this->get('/q/'.$code)
            ->assertStatus(302)
            ->assertRedirect(url('/new-bio-slug'));
    }

    public function test_short_slug_change_keeps_same_qr_redirect(): void
    {
        $user = $this->userWithRole();
        $link = $this->makeLink($user, 'shortlink', 'old-short-slug');

        $id = $this->actingAs($user)
            ->postJson('/qrcodes/prepare/link-qr', ['link_id' => $link->id])
            ->json('id');

        $qr = QRCode::query()->findOrFail($id);
        $code = $qr->public_code;
        $content = $qr->content;

        $link->url_name = 'new-short-slug';
        $link->save();

        $fresh = $qr->fresh();
        $this->assertSame($code, $fresh->public_code);
        $this->assertSame($content, $fresh->content);
        $this->assertSame($link->id, $fresh->destination_link_id);

        $this->get('/q/'.$code)
            ->assertStatus(302)
            ->assertRedirect(url('/new-short-slug'));
    }
}
