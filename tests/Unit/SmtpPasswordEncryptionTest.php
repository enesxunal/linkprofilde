<?php

namespace Tests\Unit;

use App\Helpers\AppHelper;
use App\Models\SmtpSetting;
use App\Support\SmtpPasswordBackfill;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SmtpPasswordEncryptionTest extends TestCase
{
    use DatabaseTransactions;

    private function insertSmtp(array $overrides = []): SmtpSetting
    {
        $smtp = new SmtpSetting();
        $smtp->host = $overrides['host'] ?? 'smtp.example.com';
        $smtp->port = $overrides['port'] ?? '587';
        $smtp->username = $overrides['username'] ?? 'smtp-user';
        $smtp->password = $overrides['password'] ?? 'plain-secret';
        $smtp->encryption = $overrides['encryption'] ?? 'tls';
        $smtp->sender_email = $overrides['sender_email'] ?? 'noreply@example.com';
        $smtp->sender_name = $overrides['sender_name'] ?? 'Example';
        $smtp->save();

        return $smtp->fresh();
    }

    private function rawPassword(int $id): ?string
    {
        return DB::table('smtp_settings')->where('id', $id)->value('password');
    }

    public function test_new_password_is_not_stored_as_plaintext(): void
    {
        $plain = 'plain-secret-a';
        $smtp = $this->insertSmtp(['password' => $plain]);

        $raw = $this->rawPassword($smtp->id);

        $this->assertNotSame($plain, $raw);
        $this->assertNotEmpty($raw);
        $this->assertStringNotContainsString($plain, (string) $raw);
    }

    public function test_eloquent_read_returns_decrypted_plaintext(): void
    {
        $plain = 'plain-secret-b';
        $smtp = $this->insertSmtp(['password' => $plain]);

        $this->assertSame($plain, $smtp->password);
        $this->assertSame($plain, SmtpSetting::find($smtp->id)->password);
    }

    public function test_serialization_hides_password(): void
    {
        $smtp = $this->insertSmtp(['password' => 'plain-secret-c']);

        $array = $smtp->toArray();
        $json = $smtp->toJson();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertStringNotContainsString('plain-secret-c', $json);
        $this->assertStringNotContainsString('"password"', $json);
    }

    public function test_blank_password_update_preserves_encrypted_value(): void
    {
        $smtp = $this->insertSmtp(['password' => 'keep-me']);
        $rawBefore = $this->rawPassword($smtp->id);

        $smtp->host = 'smtp.updated.example';
        $smtp->save();

        $fresh = $smtp->fresh();
        $this->assertSame($rawBefore, $this->rawPassword($fresh->id));
        $this->assertSame('keep-me', $fresh->password);
        $this->assertSame('smtp.updated.example', $fresh->host);
    }

    public function test_new_password_update_encrypts_and_decrypts(): void
    {
        $smtp = $this->insertSmtp(['password' => 'old-secret']);
        $rawBefore = $this->rawPassword($smtp->id);

        $smtp->password = 'new-secret';
        $smtp->save();

        $fresh = $smtp->fresh();
        $rawAfter = $this->rawPassword($fresh->id);

        $this->assertSame('new-secret', $fresh->password);
        $this->assertNotSame($rawBefore, $rawAfter);
        $this->assertNotSame('new-secret', $rawAfter);
        $this->assertStringNotContainsString('new-secret', (string) $rawAfter);
    }

    public function test_backfill_encrypts_legacy_plaintext(): void
    {
        $id = DB::table('smtp_settings')->insertGetId([
            'host' => 'smtp.legacy.example',
            'port' => '587',
            'username' => 'legacy-user',
            'password' => 'legacy-plain',
            'encryption' => 'tls',
            'sender_email' => 'legacy@example.com',
            'sender_name' => 'Legacy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $updated = SmtpPasswordBackfill::encryptExistingRows();
        $raw = $this->rawPassword($id);

        $this->assertGreaterThanOrEqual(1, $updated);
        $this->assertNotSame('legacy-plain', $raw);
        $this->assertSame('legacy-plain', Crypt::decryptString($raw));
        $this->assertSame('legacy-plain', SmtpSetting::find($id)->password);
    }

    public function test_backfill_is_idempotent_and_does_not_double_encrypt(): void
    {
        $id = DB::table('smtp_settings')->insertGetId([
            'host' => 'smtp.once.example',
            'port' => '587',
            'username' => 'once-user',
            'password' => 'once-plain',
            'encryption' => 'tls',
            'sender_email' => 'once@example.com',
            'sender_name' => 'Once',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SmtpPasswordBackfill::encryptExistingRows();
        $rawAfterFirst = $this->rawPassword($id);

        $updatedSecond = SmtpPasswordBackfill::encryptExistingRows();
        $rawAfterSecond = $this->rawPassword($id);

        $this->assertSame(0, $updatedSecond);
        $this->assertSame($rawAfterFirst, $rawAfterSecond);
        $this->assertSame('once-plain', Crypt::decryptString($rawAfterSecond));
        $this->assertSame('once-plain', SmtpSetting::find($id)->password);
    }

    public function test_app_helper_smtp_uses_decrypted_password(): void
    {
        DB::table('smtp_settings')->delete();
        $this->insertSmtp([
            'host' => 'smtp.helper.example',
            'username' => 'helper-user',
            'password' => 'helper-secret',
        ]);

        $smtp = AppHelper::smtp();

        $this->assertSame('helper-secret', $smtp->password);
        $this->assertSame('helper-secret', config('mail.mailers.smtp.password'));
        $this->assertSame('smtp.helper.example', config('mail.mailers.smtp.host'));
    }
}
