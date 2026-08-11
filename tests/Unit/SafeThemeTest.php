<?php

namespace Tests\Unit;

use App\Support\SafeTheme;
use Tests\TestCase;

class SafeThemeTest extends TestCase
{
    public function test_valid_hex_passes(): void
    {
        $this->assertSame('#30425A', SafeTheme::hex('#30425A'));
        $this->assertSame('#fff', SafeTheme::hex('#fff'));
        $this->assertSame('#FFFFFF', SafeTheme::hex('#FFFFFF'));
    }

    public function test_raw_css_is_rejected(): void
    {
        $this->assertNull(SafeTheme::hex('position:fixed;z-index:99999'));
        $this->assertNull(SafeTheme::hex('#fff; position:fixed'));
        $this->assertNull(SafeTheme::hex("url(//evil.example/x.png)"));
        $this->assertNull(SafeTheme::hex('red'));
        $this->assertNull(SafeTheme::hex('javascript:alert(1)'));
    }

    public function test_font_allowlist(): void
    {
        $this->assertSame('Inter, sans-serif', SafeTheme::font('Inter, sans-serif'));
        $this->assertNull(SafeTheme::font('Inter; position:fixed'));
        $this->assertNull(SafeTheme::font('url(//evil.example)'));
    }

    public function test_color_background_is_single_declaration(): void
    {
        $this->assertSame('background-color: #30425A', SafeTheme::colorBackground('#30425A'));
        $this->assertStringNotContainsString('position', SafeTheme::colorBackground('#30425A'));
    }
}
