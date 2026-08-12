<?php

namespace Tests\Unit;

use App\Rules\SafeUrl as SafeUrlRule;
use App\Support\SafeUrl;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SafeUrlTest extends TestCase
{
    public function test_https_and_http_pass(): void
    {
        $this->assertSame('https://example.com', SafeUrl::canonicalize('https://example.com'));
        $this->assertSame('http://example.com', SafeUrl::canonicalize('http://example.com'));
        $this->assertSame(
            'https://example.com/path?q=1',
            SafeUrl::canonicalize('https://example.com/path?q=1')
        );
    }

    public function test_javascript_schemes_fail(): void
    {
        $this->assertNull(SafeUrl::canonicalize('javascript:alert(1)'));
        $this->assertNull(SafeUrl::canonicalize('JaVaScRiPt:alert(1)'));
        $this->assertNull(SafeUrl::canonicalize('  javascript:alert(1)  '));
    }

    public function test_data_blob_and_vbscript_fail(): void
    {
        $this->assertNull(SafeUrl::canonicalize('data:text/html,<script>alert(1)</script>'));
        $this->assertNull(SafeUrl::canonicalize('blob:https://example.com/uuid'));
        $this->assertNull(SafeUrl::canonicalize('vbscript:msgbox(1)'));
    }

    public function test_protocol_relative_fails(): void
    {
        $this->assertNull(SafeUrl::canonicalize('//evil.example'));
        $this->assertNull(SafeUrl::canonicalize('//evil.example/phish'));
    }

    public function test_malformed_and_control_chars_fail(): void
    {
        $this->assertNull(SafeUrl::canonicalize("https://example.com/\nalert"));
        $this->assertNull(SafeUrl::canonicalize("https://example.com/\x00path"));
        $this->assertNull(SafeUrl::canonicalize('not a url'));
        $this->assertNull(SafeUrl::canonicalize('https:example.com'));
        $this->assertNull(SafeUrl::canonicalize('https://user:pass@example.com'));
    }

    public function test_validation_rule(): void
    {
        $this->assertFalse(Validator::make(['url' => 'https://example.com'], ['url' => [new SafeUrlRule]])->fails());
        $this->assertTrue(Validator::make(['url' => 'javascript:alert(1)'], ['url' => [new SafeUrlRule]])->fails());
        $this->assertTrue(Validator::make(['url' => '//evil.example'], ['url' => [new SafeUrlRule]])->fails());
    }

    public function test_href_allows_fragments_and_rejects_javascript(): void
    {
        $this->assertSame('#', SafeUrl::href('#'));
        $this->assertSame('#help', SafeUrl::href('#help'));
        $this->assertSame('', SafeUrl::href(null));
        $this->assertNull(SafeUrl::href('javascript:alert(1)'));
        $this->assertSame('https://twitter.com', SafeUrl::href('https://twitter.com'));
    }
}
