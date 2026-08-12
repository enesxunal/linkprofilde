<?php

namespace Tests\Feature;

use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\PricingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageMigrationSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // Isolate from shared docker MySQL used by the app container.
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();
        $this->withoutVite();
    }

    public function test_a_full_legacy_add_blocks_row_is_updated_to_defaults(): void
    {
        $this->seedAppSetting();
        $section = $this->createSection(
            'Add Blocks',
            'Block Ekle',
            'Linkinizi özelleştirilebilir şekilde ayarlamak için sürükle ve bırak özelliğini kullanabilirsiniz.',
            $this->legacyDuplicateList()
        );

        $this->runHomepageMigration();

        $section->refresh();
        $this->assertSame('Linklerini daha sade, daha yönetilebilir hale getir.', $section->title);
        $this->assertSame(
            'Kısa link oluştur, düzenle, ara ve yönlendir. Ziyaret performansını analytics ile takip et.',
            $section->description
        );
        $contents = collect(json_decode($section->section_list, true))->pluck('content')->all();
        $this->assertContains('Kısa link oluşturma', $contents);
        $this->assertNotContains('Özel renkler', $contents);
    }

    public function test_b_custom_title_with_legacy_list_keeps_title_updates_list(): void
    {
        $this->seedAppSetting();
        $section = $this->createSection(
            'Add Blocks',
            'Benim özel başlığım',
            'Benim özel açıklamam',
            $this->legacyDuplicateList()
        );

        $this->runHomepageMigration();

        $section->refresh();
        $this->assertSame('Benim özel başlığım', $section->title);
        $this->assertSame('Benim özel açıklamam', $section->description);
        $contents = collect(json_decode($section->section_list, true))->pluck('content')->all();
        $this->assertContains('Kısa link oluşturma', $contents);
        $this->assertNotContains('19+ Tema', $contents);
    }

    public function test_c_legacy_title_with_custom_list_keeps_custom_list(): void
    {
        $this->seedAppSetting();
        $customList = json_encode([
            ['content' => 'Custom bullet A', 'icon' => 'double-check', 'url' => null],
            ['content' => 'Custom bullet B', 'icon' => 'double-check', 'url' => null],
        ]);
        $section = $this->createSection(
            'QR Codes',
            'QR Kod Oluştur',
            'Kendi benzersiz QR kodunuzu oluşturun ve içeriğini istediğiniz gibi düzenleyin.',
            $customList
        );

        $this->runHomepageMigration();

        $section->refresh();
        $this->assertSame('Markana uygun QR kodlar oluştur', $section->title);
        $contents = collect(json_decode($section->section_list, true))->pluck('content')->all();
        $this->assertSame(['Custom bullet A', 'Custom bullet B'], $contents);
    }

    public function test_d_custom_description_is_preserved_when_title_is_legacy(): void
    {
        $this->seedAppSetting();
        $section = $this->createSection(
            'Add Blocks',
            'Block Ekle',
            'Admin tarafından yazılmış özel açıklama',
            $this->legacyDuplicateList()
        );

        $this->runHomepageMigration();

        $section->refresh();
        $this->assertSame('Linklerini daha sade, daha yönetilebilir hale getir.', $section->title);
        $this->assertSame('Admin tarafından yazılmış özel açıklama', $section->description);
    }

    public function test_e_support_destek_title_with_custom_list_keeps_custom_list(): void
    {
        $this->seedAppSetting();
        $customList = json_encode([
            ['content' => 'Özel SSS 1', 'icon' => null, 'url' => null],
            ['content' => 'Özel SSS 2', 'icon' => null, 'url' => null],
        ]);
        $section = $this->createSection('Support', 'Destek', null, $customList);

        $this->runHomepageMigration();

        $section->refresh();
        $this->assertSame('Sık sorulan sorular', $section->title);
        $contents = collect(json_decode($section->section_list, true))->pluck('content')->all();
        $this->assertSame(['Özel SSS 1', 'Özel SSS 2'], $contents);
    }

    public function test_f_custom_copyright_with_2023_is_not_changed(): void
    {
        $app = $this->seedAppSetting('© 2023 Benim Markam');

        $this->runHomepageMigration();

        $app->refresh();
        $this->assertSame('© 2023 Benim Markam', $app->copyright);
    }

    public function test_g_linkburada_copyright_is_updated(): void
    {
        $app = $this->seedAppSetting("3 Kare Yazılım\nTelif hakları © 2023 LinkBurada. Tüm hakları saklıdır.");

        $this->runHomepageMigration();

        $app->refresh();
        $this->assertSame(
            '3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi. Tüm hakları saklıdır.',
            $app->copyright
        );
        $this->assertStringNotContainsString('LinkBurada', $app->copyright);
    }

    public function test_h_homepage_renders_without_header_section(): void
    {
        $this->seedAppSetting();
        $this->seedMinimalPricingPlan();

        // Intentionally no Header app_section row.
        $this->createSection('Features', 'Features', null, json_encode([
            ['content' => 'Bio Link', 'icon' => 'link', 'url' => null],
        ]));
        $this->createSection('Follow On', 'Takip:', null, json_encode([
            ['content' => null, 'icon' => 'twitter', 'url' => 'https://twitter.com'],
        ]));
        $this->createSection('Address', 'Adres', null, json_encode([
            ['content' => 'Ankara', 'icon' => null, 'url' => null],
        ]));
        $this->createSection('Support', 'Sık sorulan sorular', 'Desc', json_encode([
            ['content' => 'LinkProfilde nedir?|||Cevap', 'icon' => null, 'url' => null],
        ]));

        if (!is_dir(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0777, true);
        }
        file_put_contents(storage_path('app/public/installed'), '1');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Tek link. Tüm dijital dünyan.', false);
    }

    public function test_i_running_migration_logic_twice_does_not_change_custom_or_updated_data(): void
    {
        $this->seedAppSetting();
        $section = $this->createSection(
            'Add Blocks',
            'Block Ekle',
            'Linkinizi özelleştirilebilir şekilde ayarlamak için sürükle ve bırak özelliğini kullanabilirsiniz.',
            $this->legacyDuplicateList()
        );

        $this->runHomepageMigration();
        $section->refresh();

        $afterFirst = [
            'title' => $section->title,
            'description' => $section->description,
            'section_list' => $section->section_list,
        ];

        // Customize after first pass.
        $section->title = 'Sonradan customize başlık';
        $section->description = 'Sonradan customize açıklama';
        $section->section_list = json_encode([
            ['content' => 'Customize bullet', 'icon' => 'double-check', 'url' => null],
        ]);
        $section->save();

        $this->runHomepageMigration();
        $section->refresh();

        $this->assertSame('Sonradan customize başlık', $section->title);
        $this->assertSame('Sonradan customize açıklama', $section->description);
        $this->assertSame(['Customize bullet'], collect(json_decode($section->section_list, true))->pluck('content')->all());

        // Also: second pass on already-updated defaults must be stable.
        $fresh = $this->createSection(
            'QR Codes',
            'Markana uygun QR kodlar oluştur',
            'Renk, boyut, köşe stili ve logo ile QR kodunu özelleştir; indir ve projelerin altında düzenle.',
            json_encode([
                ['content' => 'QR oluşturma ve indirme', 'icon' => 'double-check', 'url' => null],
            ])
        );
        $before = $fresh->only(['title', 'description', 'section_list']);
        $this->runHomepageMigration();
        $fresh->refresh();
        $this->assertSame($before['title'], $fresh->title);
        $this->assertSame($before['description'], $fresh->description);
        $this->assertSame($before['section_list'], $fresh->section_list);

        $this->assertNotSame('Block Ekle', $afterFirst['title']);
    }

    private function runHomepageMigration(): void
    {
        $migration = require database_path('migrations/2026_08_12_000001_refresh_homepage_marketing_content.php');
        $migration->up();
    }

    private function seedAppSetting(string $copyright = '3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi. Tüm hakları saklıdır.'): AppSetting
    {
        $app = new AppSetting();
        $app->forceFill([
            'title' => 'LinkProfilde',
            'logo' => 'assets/icons/link-drop.png',
            'description' => 'Test description',
            'copyright' => $copyright,
        ])->save();

        return $app->fresh();
    }

    private function createSection(string $name, string $title, ?string $description, ?string $sectionList): AppSection
    {
        $section = new AppSection();
        $section->forceFill([
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'thumbnail' => null,
            'section_list' => $sectionList,
        ])->save();

        return $section->fresh();
    }

    private function legacyDuplicateList(): string
    {
        return json_encode([
            ['content' => 'Özel renkler', 'icon' => 'double-check', 'url' => null],
            ['content' => 'Ayarları özelleştirin veya sürükleyip bırakın', 'icon' => 'double-check', 'url' => null],
            ['content' => '19+ Tema', 'icon' => 'double-check', 'url' => null],
            ['content' => 'Ayarları özelleştirin veya sürükleyip bırakın', 'icon' => 'double-check', 'url' => null],
        ]);
    }

    private function seedMinimalPricingPlan(): void
    {
        PricingPlan::create([
            'name' => 'BASIC',
            'description' => 'Ücretsiz plan',
            'monthly_price' => 0,
            'yearly_price' => 0,
            'currency' => 'TRY',
            'status' => 'active',
            'biolinks' => '5',
            'biolink_blocks' => 4,
            'shortlinks' => '10',
            'projects' => '10',
            'qrcodes' => '10',
            'themes' => 'Free',
            'custom_theme' => false,
            'support' => 72,
        ]);
    }
}
