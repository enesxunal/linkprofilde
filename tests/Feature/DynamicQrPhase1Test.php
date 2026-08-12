<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\EnrichAnalyticsEventLocationJob;
use App\Models\AnalyticsEvent;
use App\Models\Link;
use App\Models\QRCode;
use App\Models\ShetabitVisit;
use App\Models\User;
use App\Services\QRCode\QrDestinationResolver;
use App\Services\QRCode\QrPublicCodeGenerator;
use App\Services\QRCode\QrScanRecorder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use LogicException;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DynamicQrPhase1Test extends TestCase
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

    private function makeLink(User $user, string $type = 'biolink', ?string $urlName = null): Link
    {
        $link = new Link();
        $link->user_id = $user->id;
        $link->link_name = $type === 'shortlink' ? 'Short Demo' : 'Bio Demo';
        $link->link_type = $type;
        $link->url_name = $urlName ?? ($type.'-'.uniqid());
        $link->external_url = $type === 'shortlink' ? 'https://example.com/target' : null;
        $link->save();

        return $link->fresh();
    }

    private function makeDynamicQr(User $user, array $overrides = []): QRCode
    {
        $generator = app(QrPublicCodeGenerator::class);
        $code = $overrides['public_code'] ?? $generator->generateUnique();

        $qr = new QRCode();
        $qr->user_id = $user->id;
        $qr->qr_type = 'project_qr';
        $qr->content = url('/q/'.$code);
        $qr->img_data = 'data:image/png;base64,AAA';
        $qr->public_code = $code;
        $qr->is_dynamic = true;
        $qr->is_active = true;
        $qr->destination_type = QRCode::DESTINATION_EXTERNAL;
        $qr->destination_url = 'https://example.com/landing';
        $qr->destination_link_id = null;

        foreach ($overrides as $key => $value) {
            $qr->{$key} = $value;
        }

        $qr->save();

        return $qr->fresh();
    }

    public function test_migration_preserves_existing_legacy_qr_data(): void
    {
        $this->assertTrue(Schema::hasColumns('qrcodes', [
            'public_code',
            'is_dynamic',
            'is_active',
            'destination_type',
            'destination_url',
            'destination_link_id',
            'deleted_at',
        ]));
        $this->assertTrue(Schema::hasTable('analytics_events'));

        $user = $this->userWithRole();
        $id = DB::table('qrcodes')->insertGetId([
            'user_id' => $user->id,
            'link_id' => null,
            'project_id' => null,
            'name' => 'Legacy Menu',
            'qr_type' => 'project_qr',
            'content' => 'https://example.com/legacy',
            'img_data' => 'data:image/png;base64,LEGACY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $row = DB::table('qrcodes')->where('id', $id)->first();

        $this->assertSame('https://example.com/legacy', $row->content);
        $this->assertSame('data:image/png;base64,LEGACY', $row->img_data);
        $this->assertNull($row->public_code);
        $this->assertSame(0, (int) $row->is_dynamic);
        $this->assertSame(1, (int) $row->is_active);
        $this->assertNull($row->deleted_at);
    }

    public function test_public_code_is_twelve_char_unique_base62_not_numeric_id(): void
    {
        $generator = app(QrPublicCodeGenerator::class);
        $codes = [];

        for ($i = 0; $i < 5; $i++) {
            $code = $generator->generateUnique();
            $this->assertSame(12, strlen($code));
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{12}$/', $code);
            $this->assertFalse(ctype_digit($code), 'public_code must not be a pure numeric id');
            $codes[] = $code;
        }

        $this->assertSame(count($codes), count(array_unique($codes)));
    }

    public function test_unauthenticated_scan_redirects_and_writes_qr_scan_without_ip(): void
    {
        Bus::fake([EnrichAnalyticsEventLocationJob::class]);

        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, [
            'destination_type' => QRCode::DESTINATION_EXTERNAL,
            'destination_url' => 'https://example.com/menu',
        ]);

        $response = $this->get('/q/'.$qr->public_code);

        $response->assertRedirect('https://example.com/menu');
        $response->assertStatus(302);

        $event = AnalyticsEvent::query()->where('event_type', AnalyticsEvent::TYPE_QR_SCAN)->first();
        $this->assertNotNull($event);
        $this->assertSame($owner->id, (int) $event->owner_id);
        $this->assertSame(AnalyticsEvent::SUBJECT_QR_CODE, $event->subject_type);
        $this->assertSame($qr->id, (int) $event->subject_id);
        $this->assertSame(AnalyticsEvent::SOURCE_QR, $event->source_type);
        $this->assertSame($qr->id, (int) $event->source_id);
        $this->assertSame(QRCode::DESTINATION_EXTERNAL, $event->metadata['destination_type'] ?? null);
        $this->assertArrayNotHasKey('destination_url', $event->metadata ?? []);
        $this->assertArrayNotHasKey('ip', $event->getAttributes());
        $this->assertFalse(Schema::hasColumn('analytics_events', 'ip'));

        Bus::assertDispatched(EnrichAnalyticsEventLocationJob::class, function (EnrichAnalyticsEventLocationJob $job) use ($event) {
            return $job->eventId === $event->id && $job->ip !== '';
        });
    }

    public function test_inactive_qr_returns_410(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, ['is_active' => false]);

        $this->get('/q/'.$qr->public_code)
            ->assertStatus(410)
            ->assertSee('Bu QR kod artık aktif değil.', false);

        $this->assertSame(0, AnalyticsEvent::query()->count());
    }

    public function test_soft_deleted_qr_returns_410(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);
        $code = $qr->public_code;
        $qr->delete();

        $this->assertSoftDeleted('qrcodes', ['id' => $qr->id]);

        $this->get('/q/'.$code)
            ->assertStatus(410)
            ->assertSee('Bu QR kod artık aktif değil.', false);

        $this->assertSame(0, AnalyticsEvent::query()->count());
    }

    public function test_unknown_code_returns_404(): void
    {
        $this->get('/q/ABCDEF12abcd')
            ->assertStatus(404)
            ->assertSee('Bağlantı kullanılamıyor.', false);
    }

    public function test_legacy_static_qr_is_not_resolved_by_public_endpoint(): void
    {
        $owner = $this->userWithRole();
        $generator = app(QrPublicCodeGenerator::class);
        $code = $generator->generateUnique();

        $qr = new QRCode();
        $qr->user_id = $owner->id;
        $qr->qr_type = 'project_qr';
        $qr->content = 'https://example.com/static';
        $qr->img_data = 'data:image/png;base64,STATIC';
        $qr->public_code = $code;
        $qr->is_dynamic = false;
        $qr->is_active = true;
        $qr->save();

        $this->get('/q/'.$code)
            ->assertStatus(404);

        $this->assertSame(0, AnalyticsEvent::query()->count());
    }

    public function test_unsafe_external_destination_is_rejected_on_assign_and_unavailable_on_resolve(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);
        $resolver = app(QrDestinationResolver::class);

        try {
            $resolver->assertAssignable($qr, QRCode::DESTINATION_EXTERNAL, 'javascript:alert(1)');
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('destination_url', $e->errors());
        }

        try {
            $resolver->assertAssignable($qr, QRCode::DESTINATION_EXTERNAL, 'data:text/html,hi');
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('destination_url', $e->errors());
        }

        $qr->destination_url = 'javascript:alert(1)';
        $qr->save();

        $result = $resolver->resolve($qr->fresh());
        $this->assertFalse($result->ok);
    }

    public function test_cross_user_biolink_destination_rejected(): void
    {
        $owner = $this->userWithRole();
        $other = $this->userWithRole();
        $foreign = $this->makeLink($other, 'biolink');
        $qr = $this->makeDynamicQr($owner);
        $resolver = app(QrDestinationResolver::class);

        $this->expectException(ValidationException::class);
        $resolver->assertAssignable($qr, QRCode::DESTINATION_BIOLINK, null, $foreign->id);
    }

    public function test_biolink_type_mismatch_rejected(): void
    {
        $owner = $this->userWithRole();
        $short = $this->makeLink($owner, 'shortlink');
        $qr = $this->makeDynamicQr($owner);
        $resolver = app(QrDestinationResolver::class);

        $this->expectException(ValidationException::class);
        $resolver->assertAssignable($qr, QRCode::DESTINATION_BIOLINK, null, $short->id);
    }

    public function test_shortlink_type_mismatch_rejected(): void
    {
        $owner = $this->userWithRole();
        $bio = $this->makeLink($owner, 'biolink');
        $qr = $this->makeDynamicQr($owner);
        $resolver = app(QrDestinationResolver::class);

        $this->expectException(ValidationException::class);
        $resolver->assertAssignable($qr, QRCode::DESTINATION_SHORTLINK, null, $bio->id);
    }

    public function test_self_and_other_dynamic_qr_redirects_rejected(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);
        $other = $this->makeDynamicQr($owner);
        $resolver = app(QrDestinationResolver::class);

        $selfUrl = rtrim((string) config('app.url'), '/').'/q/'.$qr->public_code;
        $otherUrl = rtrim((string) config('app.url'), '/').'/q/'.$other->public_code;

        try {
            $resolver->assertAssignable($qr, QRCode::DESTINATION_EXTERNAL, $selfUrl);
            $this->fail('Expected self redirect rejection');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('destination_url', $e->errors());
        }

        try {
            $resolver->assertAssignable($qr, QRCode::DESTINATION_EXTERNAL, $otherUrl);
            $this->fail('Expected other /q redirect rejection');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('destination_url', $e->errors());
        }

        $qr->destination_url = $otherUrl;
        $qr->save();
        $this->assertFalse($resolver->resolve($qr->fresh())->ok);
    }

    public function test_public_code_is_immutable_after_assignment(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);
        $original = $qr->public_code;

        try {
            $qr->public_code = 'ZZZZZZZZZZZZ';
            $qr->save();
            $this->fail('Expected LogicException for public_code mutation');
        } catch (LogicException $e) {
            $this->assertStringContainsString('immutable', $e->getMessage());
        }

        $this->assertSame($original, $qr->fresh()->public_code);
    }

    public function test_assign_public_code_does_not_overwrite_existing(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);
        $original = $qr->public_code;

        $returned = app(QrPublicCodeGenerator::class)->assign($qr->fresh());

        $this->assertSame($original, $returned);
        $this->assertSame($original, $qr->fresh()->public_code);
    }

    public function test_biolink_destination_scan_then_shetabit_still_tracks(): void
    {
        Bus::fake([EnrichAnalyticsEventLocationJob::class]);

        $owner = $this->userWithRole();
        $link = $this->makeLink($owner, 'biolink', 'bio-qr-'.uniqid());
        $qr = $this->makeDynamicQr($owner, [
            'destination_type' => QRCode::DESTINATION_BIOLINK,
            'destination_url' => null,
            'destination_link_id' => $link->id,
        ]);

        $scan = $this->get('/q/'.$qr->public_code);
        $scan->assertRedirect(url('/'.$link->url_name));

        $this->assertSame(1, AnalyticsEvent::query()->where('event_type', AnalyticsEvent::TYPE_QR_SCAN)->count());

        $land = $this->get('/'.$link->url_name);
        $land->assertOk();

        $this->assertGreaterThanOrEqual(
            1,
            ShetabitVisit::query()->where('link_id', $link->id)->count()
        );
    }

    public function test_missing_destination_returns_safe_unavailable(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, [
            'destination_type' => null,
            'destination_url' => null,
            'destination_link_id' => null,
        ]);

        $this->get('/q/'.$qr->public_code)
            ->assertStatus(410)
            ->assertSee('Bağlantı kullanılamıyor.', false);

        $this->assertSame(0, AnalyticsEvent::query()->count());
    }

    public function test_soft_delete_via_controller_nulls_link_qrcode_id(): void
    {
        $owner = $this->userWithRole();
        $link = $this->makeLink($owner, 'biolink');
        $qr = $this->makeDynamicQr($owner, [
            'link_id' => $link->id,
            'qr_type' => 'link_qr',
            'destination_type' => QRCode::DESTINATION_BIOLINK,
            'destination_link_id' => $link->id,
            'destination_url' => null,
        ]);
        $link->qrcode_id = $qr->id;
        $link->save();

        $this->actingAs($owner)
            ->from('/qrcodes')
            ->delete('/qrcodes/delete/'.$qr->id)
            ->assertRedirect();

        $this->assertSoftDeleted('qrcodes', ['id' => $qr->id]);
        $this->assertNull($link->fresh()->qrcode_id);
    }

    public function test_numeric_id_path_is_not_a_qr_redirect(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);

        // /q/{numeric id} must not match the 12-char publicCode route constraint.
        $this->get('/q/'.$qr->id)->assertStatus(404);
    }

    public function test_recorder_exception_still_redirects_to_destination(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, [
            'destination_url' => 'https://example.com/still-works',
        ]);

        $this->mock(QrScanRecorder::class, function ($mock) {
            $mock->shouldReceive('record')
                ->once()
                ->andThrow(new RuntimeException('analytics unavailable'));
        });

        $this->get('/q/'.$qr->public_code)
            ->assertStatus(302)
            ->assertRedirect('https://example.com/still-works');

        $this->assertSame(0, AnalyticsEvent::query()->count());
    }

    public function test_redirect_route_has_no_throttle_and_repeated_scans_are_not_429(): void
    {
        $route = app('router')->getRoutes()->getByName('qr.redirect');
        $this->assertNotNull($route);
        $middleware = $route->gatherMiddleware();
        foreach ($middleware as $item) {
            $this->assertStringNotContainsString(
                'throttle',
                is_string($item) ? $item : '',
                'QR redirect must not use route throttle middleware'
            );
        }

        Bus::fake([EnrichAnalyticsEventLocationJob::class]);

        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, [
            'destination_url' => 'https://example.com/burst',
        ]);

        for ($i = 0; $i < 25; $i++) {
            $this->get('/q/'.$qr->public_code)
                ->assertStatus(302)
                ->assertRedirect('https://example.com/burst');
        }
    }

    public function test_successful_redirect_sends_no_store_cache_headers(): void
    {
        Bus::fake([EnrichAnalyticsEventLocationJob::class]);

        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, [
            'destination_url' => 'https://example.com/cached',
        ]);

        $response = $this->get('/q/'.$qr->public_code);

        $response->assertStatus(302);
        $response->assertRedirect('https://example.com/cached');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertSame('no-cache', $response->headers->get('Pragma'));
    }

    public function test_assign_retries_on_public_code_unique_collision(): void
    {
        $owner = $this->userWithRole();
        $taken = 'RaceTaken001';
        $free = 'RaceFree0002';
        $this->makeDynamicQr($owner, ['public_code' => $taken]);

        $generator = $this->getMockBuilder(QrPublicCodeGenerator::class)
            ->onlyMethods(['generate'])
            ->getMock();
        $generator->expects($this->exactly(2))
            ->method('generate')
            ->willReturnOnConsecutiveCalls($taken, $free);

        $qr = new QRCode();
        $qr->user_id = $owner->id;
        $qr->qr_type = 'project_qr';
        $qr->content = 'https://example.com/pending';
        $qr->img_data = 'data:image/png;base64,AAA';
        $qr->is_dynamic = true;
        $qr->is_active = true;
        $qr->destination_type = QRCode::DESTINATION_EXTERNAL;
        $qr->destination_url = 'https://example.com/x';

        $assigned = $generator->assign($qr);

        $this->assertSame($free, $assigned);
        $this->assertSame($free, $qr->fresh()->public_code);
    }

    public function test_assign_does_not_swallow_unrelated_query_exception(): void
    {
        $owner = $this->userWithRole();

        $generator = $this->getMockBuilder(QrPublicCodeGenerator::class)
            ->onlyMethods(['generate'])
            ->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->willReturn('Unrelated9901');

        // Missing NOT NULL columns (content/img_data/qr_type) → unrelated QueryException.
        $qr = new QRCode();
        $qr->user_id = $owner->id;
        $qr->is_dynamic = true;
        $qr->is_active = true;

        $this->expectException(QueryException::class);
        $generator->assign($qr);
    }

    public function test_uppercase_q_path_loop_is_rejected(): void
    {
        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner);
        $other = $this->makeDynamicQr($owner);
        $resolver = app(QrDestinationResolver::class);

        $upperUrl = rtrim((string) config('app.url'), '/').'/Q/'.$other->public_code;

        try {
            $resolver->assertAssignable($qr, QRCode::DESTINATION_EXTERNAL, $upperUrl);
            $this->fail('Expected uppercase /Q loop rejection');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('destination_url', $e->errors());
        }

        $this->assertTrue($resolver->pointsToLocalQrRedirect($upperUrl));
    }

    public function test_single_request_writes_at_most_one_qr_scan_event(): void
    {
        Bus::fake([EnrichAnalyticsEventLocationJob::class]);

        $owner = $this->userWithRole();
        $qr = $this->makeDynamicQr($owner, [
            'destination_url' => 'https://example.com/once',
        ]);

        $this->get('/q/'.$qr->public_code)->assertRedirect('https://example.com/once');

        $this->assertSame(
            1,
            AnalyticsEvent::query()
                ->where('event_type', AnalyticsEvent::TYPE_QR_SCAN)
                ->where('subject_id', $qr->id)
                ->count()
        );
    }
}
