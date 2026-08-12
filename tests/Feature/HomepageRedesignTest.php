<?php

namespace Tests\Feature;

use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\CustomPage;
use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HomepageRedesignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_homepage_view_contains_new_hero_and_excludes_legacy_claims(): void
    {
        $html = $this->renderHomepage();

        $this->assertStringContainsString('Tek link. Tüm dijital dünyan.', $html);
        $this->assertStringNotContainsString('LinkBurada', $html);
        $this->assertStringNotContainsString('Update Header Section', $html);
        $this->assertStringNotContainsString('Save Changes', $html);
        $this->assertStringNotContainsString('Section Tiltle', $html);
        $this->assertStringNotContainsString('Dinamik QR', $html);
        $this->assertStringNotContainsString('Dynamic QR', $html);
        $this->assertStringNotContainsString('QR hedefini sonradan değiştir', $html);
        $this->assertStringContainsString('id="features"', $html);
        $this->assertStringContainsString('id="bio-link"', $html);
        $this->assertStringContainsString('id="short-links"', $html);
        $this->assertStringContainsString('id="qr"', $html);
        $this->assertStringContainsString('id="analytics"', $html);
        $this->assertStringContainsString('id="faq"', $html);
        $this->assertStringContainsString('id="pricing"', $html);
    }

    public function test_logged_in_user_sees_dashboard_link_not_register_panel_link(): void
    {
        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $html = $this->actingAs($user)->renderHomepage();

        $this->assertStringContainsString('href="/dashboard"', $html);
        $this->assertStringContainsString('Yönetim Paneli', $html);
        $this->assertStringNotContainsString('Yönetici Paneli', $html);
    }

    public function test_customize_modals_are_not_in_public_dom(): void
    {
        $html = $this->renderHomepage(customize: false);

        $this->assertStringNotContainsString('Bölümü Düzenle', $html);
        $this->assertStringNotContainsString('Update Header Section', $html);
        $this->assertStringNotContainsString('Save Changes', $html);
    }

    private function renderHomepage(bool $customize = false): string
    {
        return view('pages.home', [
            'app' => $this->fakeApp(),
            'appSections' => $this->fakeSections(),
            'customPages' => collect(),
            'testimonials' => collect(),
            'plans' => collect([$this->fakePlan()]),
            'customize' => $customize,
            'SA' => false,
        ])->render();
    }

    private function fakeApp(): AppSetting
    {
        $app = new AppSetting();
        $app->title = 'LinkProfilde';
        $app->logo = 'assets/icons/link-drop.png';
        $app->description = 'Test description';
        $app->copyright = '3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi. Tüm hakları saklıdır.';

        return $app;
    }

    private function fakePlan(): PricingPlan
    {
        $plan = new PricingPlan();
        $plan->id = 1;
        $plan->name = 'BASIC';
        $plan->description = 'Ücretsiz plan';
        $plan->monthly_price = 0;
        $plan->yearly_price = 0;
        $plan->currency = 'TRY';
        $plan->status = 'active';
        $plan->biolinks = '5';
        $plan->biolink_blocks = 4;
        $plan->shortlinks = '10';
        $plan->projects = '10';
        $plan->qrcodes = '10';
        $plan->themes = 'Free';
        $plan->custom_theme = false;
        $plan->support = 72;

        return $plan;
    }

    private function fakeSections(): Collection
    {
        $definitions = [
            ['Header', 'Tek link. Tüm dijital dünyan.', 'Profilini oluştur, linklerini yönet, QR kodlarını paylaş ve ziyaretçilerini tek panelden analiz et.', '[{"content":"Bio Link","icon":"link","url":null}]'],
            ['Features', 'Features', null, '[{"content":"Bio Link","icon":"link","url":null},{"content":"Kısa Link","icon":"link-slash","url":null},{"content":"QR Kod","icon":"qrcode","url":null},{"content":"Analytics","icon":"chart-line-up","url":null}]'],
            ['Create Link', 'Kendine ait dijital profilini oluştur.', 'Desc', '[{"content":"~40 hazır tema","icon":"double-check","url":null}]'],
            ['Add Blocks', 'Linklerini daha sade, daha yönetilebilir hale getir.', 'Desc', '[{"content":"Kısa link oluşturma","icon":"double-check","url":null}]'],
            ['QR Codes', 'Markana uygun QR kodlar oluştur', 'Desc', '[{"content":"QR oluşturma ve indirme","icon":"double-check","url":null}]', 'assets/qr-code.svg'],
            ['Analytics', 'Ne kadar paylaştığını değil, ne kadar etki yarattığını gör.', 'Desc', '[{"content":"Aylık ziyaretçi grafiği","icon":"double-check","url":null}]'],
            ['UseCases', 'Kimler için?', 'Desc', '[{"content":"İçerik Üreticileri|||Desc","icon":null,"url":null}]'],
            ['Follow On', 'Takip:', null, '[{"content":null,"icon":"twitter","url":"https://twitter.com"}]'],
            ['Support', 'Sık sorulan sorular', 'Desc', '[{"content":"LinkProfilde nedir?|||Platform açıklaması","icon":null,"url":null}]'],
            ['FinalCTA', 'Dijital dünyanı tek bağlantıda birleştir.', 'Desc', null],
            ['Address', 'Adres', null, '[{"content":"Ankara","icon":null,"url":null}]'],
        ];

        return collect($definitions)->map(function (array $def, int $index) {
            $section = new AppSection();
            $section->id = $index + 1;
            $section->name = $def[0];
            $section->title = $def[1];
            $section->description = $def[2];
            $section->section_list = $def[3];
            $section->thumbnail = $def[4] ?? null;

            return $section;
        });
    }
}
