<?php

namespace Tests\Unit;

use App\Support\PageHtml;
use Tests\TestCase;

class PageHtmlSanitizerTest extends TestCase
{
    public function test_script_tags_are_removed(): void
    {
        $html = PageHtml::sanitize('<script>alert(1)</script><p>Merhaba</p>');
        $this->assertStringNotContainsString('<script', strtolower($html));
        $this->assertStringNotContainsString('alert(1)', $html);
        $this->assertStringContainsString('Merhaba', $html);
    }

    public function test_onerror_attributes_are_removed(): void
    {
        $html = PageHtml::sanitize('<p><img src=x onerror=alert(1)></p>');
        $this->assertStringNotContainsString('onerror', strtolower($html));
        $this->assertStringNotContainsString('<img', strtolower($html));
    }

    public function test_javascript_href_is_removed(): void
    {
        $html = PageHtml::sanitize('<p><a href="javascript:alert(1)">x</a></p>');
        $this->assertStringNotContainsString('javascript:', strtolower($html));
    }

    public function test_safe_markup_is_kept(): void
    {
        $html = PageHtml::sanitize('<p><strong>Merhaba</strong></p>');
        $this->assertStringContainsString('<strong>Merhaba</strong>', $html);
        $this->assertStringContainsString('<p>', $html);
    }

    public function test_iframe_and_form_are_removed(): void
    {
        $html = PageHtml::sanitize('<iframe src="https://evil.example"></iframe><form action="/"><input></form><p>ok</p>');
        $this->assertStringNotContainsString('<iframe', strtolower($html));
        $this->assertStringNotContainsString('<form', strtolower($html));
        $this->assertStringContainsString('ok', $html);
    }

    public function test_blank_target_gets_noopener_noreferrer(): void
    {
        $html = PageHtml::sanitize('<p><a href="https://example.com" target="_blank">x</a></p>');
        $this->assertStringContainsString('noopener', strtolower($html));
        $this->assertStringContainsString('noreferrer', strtolower($html));
    }

    public function test_sanitize_is_idempotent_for_supported_quill_html(): void
    {
        $html = <<<'HTML'
<p class="ql-align-center">Merhaba <strong>kalın</strong> ve <em>italik</em></p>
<ul>
<li>Bir</li>
<li>İki</li>
</ul>
<p><a href="https://example.com" target="_blank">Link</a></p>
<table>
<thead><tr><th>Başlık</th></tr></thead>
<tbody><tr><td>Hücre</td></tr></tbody>
</table>
HTML;

        $once = PageHtml::sanitize($html);
        $twice = PageHtml::sanitize($once);

        $this->assertSame($once, $twice);
        $this->assertStringContainsString('<strong>kalın</strong>', $once);
        $this->assertStringContainsString('<em>italik</em>', $once);
        $this->assertStringContainsString('<ul>', $once);
        $this->assertStringContainsString('ql-align-center', $once);
        $this->assertStringContainsString('<table>', $once);
        $this->assertStringContainsString('https://example.com', $once);
    }
}
