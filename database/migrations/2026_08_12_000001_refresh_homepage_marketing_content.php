<?php

use App\Models\AppSection;
use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Idempotent homepage content refresh for production.
     * Field-level updates only for known legacy defaults; does not overwrite custom admin content.
     */
    public function up(): void
    {
        $this->updateCopyrightIfLegacy();
        $this->updateHeaderIfLegacy();
        $this->updateFeaturesIfLegacy();
        $this->updateCreateLinkIfLegacy();
        $this->updateAddBlocksIfLegacy();
        $this->updateQrIfLegacy();
        $this->updateSupportIfLegacy();
        $this->ensureSection('Analytics', [
            'title' => 'Ne kadar paylaştığını değil, ne kadar etki yarattığını gör.',
            'description' => 'Dashboard özetleri ve kısa link analytics ile ziyaretçilerini net şekilde takip et.',
            'thumbnail' => null,
            'section_list' => json_encode([
                ['content' => 'Toplam link, page view, proje ve QR özetleri', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Aylık ziyaretçi grafiği', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Haftalık page view', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Kısa link: ülke, cihaz, işletim sistemi, tarayıcı, dil, referrer', 'icon' => 'double-check', 'url' => null],
            ]),
        ]);
        $this->ensureSection('UseCases', [
            'title' => 'Kimler için?',
            'description' => 'Kişisel markadan işletmeye, etkinlikten NFC karta kadar aynı altyapı.',
            'thumbnail' => null,
            'section_list' => json_encode([
                ['content' => 'İçerik Üreticileri|||Tüm sosyal hesaplarını, videolarını ve iş birliği linklerini tek profilde topla.', 'icon' => null, 'url' => null],
                ['content' => 'Freelancerlar|||Portfolyo, iletişim ve proje linklerini müşterilerine tek bağlantıyla sun.', 'icon' => null, 'url' => null],
                ['content' => 'İşletmeler|||Marka profili, kampanya linkleri ve QR ile fiziksel-dijital trafiği birleştir.', 'icon' => null, 'url' => null],
                ['content' => 'Sosyal Medya|||Instagram, TikTok ve diğer platformlardan tek sabit linkle yönlendir.', 'icon' => null, 'url' => null],
                ['content' => 'Etkinlik & Fuar|||Kayıt, harita ve sponsor linklerini QR ile hızlıca paylaş.', 'icon' => null, 'url' => null],
                ['content' => 'NFC Kart Kullanıcıları|||NFC kartını LinkProfilde profil adresine yönlendir.', 'icon' => null, 'url' => null],
            ]),
        ]);
        $this->ensureSection('FinalCTA', [
            'title' => 'Dijital dünyanı tek bağlantıda birleştir.',
            'description' => 'LinkProfilde profilini dakikalar içinde oluştur ve paylaşmaya başla.',
            'thumbnail' => null,
            'section_list' => null,
        ]);
    }

    public function down(): void
    {
        // Non-destructive content migration; no rollback of marketing copy.
    }

    private function updateCopyrightIfLegacy(): void
    {
        $app = AppSetting::query()->first();
        if (!$app || !is_string($app->copyright)) {
            return;
        }

        // Only touch known LinkBurada legacy copyright; do not match bare "© 2023".
        if (str_contains($app->copyright, 'LinkBurada')) {
            $app->copyright = '3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi. Tüm hakları saklıdır.';
            $app->save();
        }
    }

    private function updateHeaderIfLegacy(): void
    {
        $section = AppSection::query()->where('name', 'Header')->first();
        if (!$section) {
            return;
        }

        $legacyTitles = [
            'Tüm Linkler Profildeki Link’te!',
            'Tüm Linkler Profildeki Link\'te!',
            'All your links in one place',
        ];
        $dirty = false;
        $wasLegacyTitle = in_array($section->title, $legacyTitles, true);

        if ($wasLegacyTitle) {
            $section->title = 'Tek link. Tüm dijital dünyan.';
            $dirty = true;

            // Fill empty description only when title itself was legacy.
            if ($section->description === null || trim((string) $section->description) === '') {
                $section->description = 'Profilini oluştur, linklerini yönet, QR kodlarını paylaş ve ziyaretçilerini tek panelden analiz et.';
            }
        }

        $legacyList = $this->normalizeList($section->section_list);
        $legacyMarkers = ['Ücretsiz Link', 'Premium Link', 'QR Kod Oluşturucu', 'Link Kısaltma'];
        if ($this->listContainsAllContents($legacyList, $legacyMarkers)) {
            $section->section_list = json_encode([
                ['content' => 'Bio Link', 'icon' => 'link', 'url' => null],
                ['content' => 'Kısa Link', 'icon' => 'link-slash', 'url' => null],
                ['content' => 'QR Kod', 'icon' => 'qrcode', 'url' => null],
                ['content' => 'Analytics', 'icon' => 'chart-line-up', 'url' => null],
            ]);
            $dirty = true;
        }

        if ($dirty) {
            $section->save();
        }
    }

    private function updateFeaturesIfLegacy(): void
    {
        $section = AppSection::query()->where('name', 'Features')->first();
        if (!$section) {
            return;
        }

        $list = $this->normalizeList($section->section_list);
        $legacyMarkers = ['Link Kısaltma', '19+ Tema', 'Ziyaretçi Takibi', 'Tam Özelleştirme'];
        if ($this->listContainsAllContents($list, $legacyMarkers)) {
            $section->section_list = json_encode([
                ['content' => 'Bio Link', 'icon' => 'link', 'url' => null],
                ['content' => 'Kısa Link', 'icon' => 'link-slash', 'url' => null],
                ['content' => 'QR Kod', 'icon' => 'qrcode', 'url' => null],
                ['content' => 'Analytics', 'icon' => 'chart-line-up', 'url' => null],
            ]);
            $section->save();
        }
    }

    private function updateCreateLinkIfLegacy(): void
    {
        $section = AppSection::query()->where('name', 'Create Link')->first();
        if (!$section) {
            return;
        }

        $legacyTitles = ['Basit bir formla link oluşturun'];
        $legacyDescriptions = [
            'Benzersiz linkinizi oluşturun ve takipçilerinizin tüm önemli içeriklerinizi takip etmeleri için
                   linklerinizi ekleyin.',
        ];
        $list = $this->normalizeList($section->section_list);
        $legacyMarkers = ['Özel renkler', '19+ Tema'];
        $dirty = false;

        if (in_array($section->title, $legacyTitles, true)) {
            $section->title = 'Kendine ait dijital profilini oluştur.';
            $dirty = true;

            $currentDescription = is_string($section->description) ? trim(preg_replace('/\s+/', ' ', $section->description) ?? '') : '';
            $legacyDescriptionNormalized = array_map(
                fn (string $text) => trim(preg_replace('/\s+/', ' ', $text) ?? ''),
                $legacyDescriptions
            );
            if ($currentDescription === '' || in_array($currentDescription, $legacyDescriptionNormalized, true)) {
                $section->description = 'Temalar, sosyal bağlantılar ve içerik bloklarıyla profesyonel bir bio link sayfası hazırla.';
            }
        }

        if ($this->listContainsAllContents($list, $legacyMarkers)) {
            $section->section_list = json_encode([
                ['content' => '~40 hazır tema', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Sosyal medya ve iletişim bağlantıları', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Link, Heading, Paragraph, Image blokları', 'icon' => 'double-check', 'url' => null],
                ['content' => 'YouTube, Spotify, Vimeo, TikTok, SoundCloud', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Sürükle-bırak sıralama', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Özel tema (plana göre)', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Telefon varsa rehbere ekleme / vCard', 'icon' => 'double-check', 'url' => null],
                ['content' => 'NFC kartını LinkProfilde profilinle kullan', 'icon' => 'double-check', 'url' => null],
            ]);
            $dirty = true;
        }

        if ($dirty) {
            $section->save();
        }
    }

    private function updateAddBlocksIfLegacy(): void
    {
        $section = AppSection::query()->where('name', 'Add Blocks')->first();
        if (!$section) {
            return;
        }

        $legacyTitles = ['Block Ekle', 'Blok Ekle'];
        $legacyDescriptions = [
            'Linkinizi özelleştirilebilir şekilde ayarlamak için sürükle ve bırak özelliğini kullanabilirsiniz.',
        ];
        $list = $this->normalizeList($section->section_list);
        $legacyMarkers = ['Özel renkler', '19+ Tema'];
        $dirty = false;

        if (in_array($section->title, $legacyTitles, true)) {
            $section->title = 'Linklerini daha sade, daha yönetilebilir hale getir.';
            $dirty = true;

            if ($this->isBlankOrLegacyDescription($section->description, $legacyDescriptions)) {
                $section->description = 'Kısa link oluştur, düzenle, ara ve yönlendir. Ziyaret performansını analytics ile takip et.';
            }
        }

        if ($this->listContainsAllContents($list, $legacyMarkers)) {
            $section->section_list = json_encode([
                ['content' => 'Kısa link oluşturma', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Düzenleme ve arama', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Harici URL yönlendirme', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Ziyaret analytics (ülke, cihaz, tarayıcı, referrer)', 'icon' => 'double-check', 'url' => null],
            ]);
            $dirty = true;
        }

        if ($dirty) {
            $section->save();
        }
    }

    private function updateQrIfLegacy(): void
    {
        $section = AppSection::query()->where('name', 'QR Codes')->first();
        if (!$section) {
            return;
        }

        $legacyTitles = ['QR Kod Oluştur'];
        $legacyDescriptions = [
            'Kendi benzersiz QR kodunuzu oluşturun ve içeriğini istediğiniz gibi düzenleyin.',
        ];
        $list = $this->normalizeList($section->section_list);
        $legacyMarkers = ['Özel renkler', '19+ Tema'];
        $dirty = false;

        if (in_array($section->title, $legacyTitles, true)) {
            $section->title = 'Markana uygun QR kodlar oluştur';
            $dirty = true;

            if ($this->isBlankOrLegacyDescription($section->description, $legacyDescriptions)) {
                $section->description = 'Renk, boyut, köşe stili ve logo ile QR kodunu özelleştir; indir ve projelerin altında düzenle.';
            }
        }

        if ($this->listContainsAllContents($list, $legacyMarkers)) {
            $section->section_list = json_encode([
                ['content' => 'QR oluşturma ve indirme', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Renk, boyut ve köşe/stil özelleştirme', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Logo ekleme', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Proje bazlı organizasyon', 'icon' => 'double-check', 'url' => null],
                ['content' => 'Bio link ve kısa link için QR', 'icon' => 'double-check', 'url' => null],
            ]);
            $dirty = true;
        }

        if ($dirty) {
            $section->save();
        }
    }

    private function updateSupportIfLegacy(): void
    {
        $section = AppSection::query()->where('name', 'Support')->first();
        if (!$section) {
            return;
        }

        $list = $this->normalizeList($section->section_list);
        $legacyMarkers = ['Help', 'Getting Started', 'FAQs'];
        $dirty = false;

        if ($section->title === 'Destek') {
            $section->title = 'Sık sorulan sorular';
            $dirty = true;

            if ($section->description === null || trim((string) $section->description) === '') {
                $section->description = 'LinkProfilde hakkında en çok merak edilenler.';
            }
        }

        if ($this->listContainsAllContents($list, $legacyMarkers)) {
            $section->section_list = json_encode([
                ['content' => 'LinkProfilde nedir?|||LinkProfilde; bio link profili, kısa link yönetimi, QR kod oluşturma ve analytics özelliklerini bir araya getiren bir dijital profil platformudur.', 'icon' => null, 'url' => null],
                ['content' => 'Bio Link nedir?|||Bio Link, tüm önemli bağlantılarını, sosyal hesaplarını ve içeriklerini tek bir kişisel profil sayfasında toplamanı sağlar.', 'icon' => null, 'url' => null],
                ['content' => 'QR kod oluşturabilir miyim?|||Evet. Renk, boyut, köşe stili ve logo ile QR kod oluşturup indirebilir; projelerin altında düzenleyebilirsin.', 'icon' => null, 'url' => null],
                ['content' => 'Kısa linklerimin performansını görebilir miyim?|||Evet. Kısa linklerin için ülke, cihaz, işletim sistemi, tarayıcı, dil ve referrer gibi analytics verilerini görebilirsin.', 'icon' => null, 'url' => null],
                ['content' => 'Profilimi özelleştirebilir miyim?|||Evet. Hazır temalar, sosyal bağlantılar, içerik blokları ve plana göre özel tema ile profilini özelleştirebilirsin.', 'icon' => null, 'url' => null],
                ['content' => 'NFC kartımla kullanabilir miyim?|||Evet. LinkProfilde public profil URL’sini NFC kartına yazarak kartını dijital profiline yönlendirebilirsin. NFC donanımı ayrıca satılmaz veya entegre edilmez.', 'icon' => null, 'url' => null],
            ]);
            $dirty = true;
        }

        if ($dirty) {
            $section->save();
        }
    }

    private function ensureSection(string $name, array $payload): void
    {
        $exists = AppSection::query()->where('name', $name)->exists();
        if ($exists) {
            return;
        }

        $section = new AppSection();
        $section->forceFill([
            'name' => $name,
            'title' => $payload['title'],
            'description' => $payload['description'],
            'thumbnail' => $payload['thumbnail'],
            'section_list' => $payload['section_list'],
        ])->save();
    }

    private function normalizeList(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }

    private function listContainsAllContents(array $list, array $markers): bool
    {
        $contents = collect($list)->pluck('content')->filter()->values()->all();
        foreach ($markers as $marker) {
            if (!in_array($marker, $contents, true)) {
                return false;
            }
        }
        return true;
    }

    private function isBlankOrLegacyDescription(mixed $description, array $legacyDescriptions): bool
    {
        if ($description === null) {
            return true;
        }

        if (!is_string($description)) {
            return false;
        }

        $normalized = trim(preg_replace('/\s+/', ' ', $description) ?? '');
        if ($normalized === '') {
            return true;
        }

        foreach ($legacyDescriptions as $legacy) {
            $legacyNormalized = trim(preg_replace('/\s+/', ' ', $legacy) ?? '');
            if ($normalized === $legacyNormalized) {
                return true;
            }
        }

        return false;
    }
};
