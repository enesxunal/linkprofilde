<?php

namespace Tests\Feature;

use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\CustomPage;
use App\Support\PageHtml;
use Tests\TestCase;

class CustomPageLegacyRenderTest extends TestCase
{
    public function test_legacy_unsafe_content_is_sanitized_on_public_render_without_touching_db(): void
    {
        $legacy = <<<'HTML'
<p>Normal</p>
<script>alert(1)</script>
<img src=x onerror=alert(1)>
<a href="javascript:alert(1)">x</a>
HTML;

        $currentPage = new CustomPage();
        $currentPage->name = 'Legacy';
        $currentPage->route = 'legacy-xss';
        $currentPage->content = $legacy;

        $safeContent = PageHtml::sanitize((string) $currentPage->content);

        $html = view('custom-page', [
            'app' => $this->fakeApp(),
            'customPages' => collect([$currentPage]),
            'currentPage' => $currentPage,
            'appSections' => collect([
                $this->fakeSection('Follow On', 'Takip'),
                $this->fakeSection('Address', 'Adres'),
            ]),
            'safeContent' => $safeContent,
        ])->render();

        $this->assertSame($legacy, $currentPage->content);
        $this->assertStringContainsString('Normal', $html);

        preg_match('/<div class="ql-editor">(.*?)<\/div>/s', $html, $match);
        $this->assertNotEmpty($match[1] ?? null);
        $rendered = strtolower($match[1]);

        $this->assertStringContainsString('<p>normal</p>', $rendered);
        $this->assertStringNotContainsString('<script', $rendered);
        $this->assertStringNotContainsString('alert(1)', $rendered);
        $this->assertStringNotContainsString('onerror', $rendered);
        $this->assertStringNotContainsString('javascript:', $rendered);
    }

    private function fakeApp(): AppSetting
    {
        $app = new AppSetting();
        $app->name = 'Test App';
        $app->title = 'Test App';
        $app->logo = 'assets/logo.png';
        $app->description = 'Desc';
        $app->copyright = 'Copyright';

        return $app;
    }

    private function fakeSection(string $name, string $title): AppSection
    {
        $section = new AppSection();
        $section->name = $name;
        $section->title = $title;
        $section->section_list = '[]';

        return $section;
    }
}
