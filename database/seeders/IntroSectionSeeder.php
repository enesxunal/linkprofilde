<?php

namespace Database\Seeders;

use App\Models\AppSection;
use Illuminate\Database\Seeder;

class IntroSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appSections = array(
            [
                'name' => 'Header',
                'title' => 'Tek link. Tüm dijital dünyan.',
                'description' => 'Profilini oluştur, linklerini yönet, QR kodlarını paylaş ve ziyaretçilerini tek panelden analiz et.',
                'thumbnail' => 'assets/themes/theme-group2.png',
                'section_list' => '[
                    {"content": "Bio Link", "icon": "link", "url": null},
                    {"content": "Kısa Link", "icon": "link-slash", "url": null},
                    {"content": "QR Kod", "icon": "qrcode", "url": null},
                    {"content": "Analytics", "icon": "chart-line-up", "url": null}
                ]',
            ],
            [
                'name' => 'Features',
                'title' => 'Features',
                'description' => null,
                'thumbnail' => null,
                'section_list' => '[
                    {"content": "Bio Link", "icon": "link", "url": null},
                    {"content": "Kısa Link", "icon": "link-slash", "url": null},
                    {"content": "QR Kod", "icon": "qrcode", "url": null},
                    {"content": "Analytics", "icon": "chart-line-up", "url": null}
                ]',
            ],
            [
                'name' => 'Create Link',
                'title' => 'Kendine ait dijital profilini oluştur.',
                'description' => 'Temalar, sosyal bağlantılar ve içerik bloklarıyla profesyonel bir bio link sayfası hazırla.',
                'thumbnail' => 'assets/create-link.svg',
                'section_list' => '[
                    {"content": "~40 hazır tema", "icon": "double-check", "url": null},
                    {"content": "Sosyal medya ve iletişim bağlantıları", "icon": "double-check", "url": null},
                    {"content": "Link, Heading, Paragraph, Image blokları", "icon": "double-check", "url": null},
                    {"content": "YouTube, Spotify, Vimeo, TikTok, SoundCloud", "icon": "double-check", "url": null},
                    {"content": "Sürükle-bırak sıralama", "icon": "double-check", "url": null},
                    {"content": "Özel tema (plana göre)", "icon": "double-check", "url": null},
                    {"content": "Telefon varsa rehbere ekleme / vCard", "icon": "double-check", "url": null},
                    {"content": "NFC kartını LinkProfilde profilinle kullan", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name' => 'Add Blocks',
                'title' => 'Linklerini daha sade, daha yönetilebilir hale getir.',
                'description' => 'Kısa link oluştur, düzenle, ara ve yönlendir. Ziyaret performansını analytics ile takip et.',
                'thumbnail' => 'assets/blocks.svg',
                'section_list' => '[
                    {"content":"Kısa link oluşturma", "icon": "double-check", "url": null},
                    {"content":"Düzenleme ve arama", "icon": "double-check", "url": null},
                    {"content":"Harici URL yönlendirme", "icon": "double-check", "url": null},
                    {"content":"Ziyaret analytics (ülke, cihaz, tarayıcı, referrer)", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name' => 'QR Codes',
                'title' => 'Markana uygun QR kodlar oluştur',
                'description' => 'Renk, boyut, köşe stili ve logo ile QR kodunu özelleştir; indir ve projelerin altında düzenle.',
                'thumbnail' => 'assets/qr-code.svg',
                'section_list' => '[
                    {"content":"QR oluşturma ve indirme", "icon": "double-check", "url": null},
                    {"content":"Renk, boyut ve köşe/stil özelleştirme", "icon": "double-check", "url": null},
                    {"content":"Logo ekleme", "icon": "double-check", "url": null},
                    {"content":"Proje bazlı organizasyon", "icon": "double-check", "url": null},
                    {"content":"Bio link ve kısa link için QR", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name' => 'Analytics',
                'title' => 'Ne kadar paylaştığını değil, ne kadar etki yarattığını gör.',
                'description' => 'Dashboard özetleri ve kısa link analytics ile ziyaretçilerini net şekilde takip et.',
                'thumbnail' => null,
                'section_list' => '[
                    {"content":"Toplam link, page view, proje ve QR özetleri", "icon": "double-check", "url": null},
                    {"content":"Aylık ziyaretçi grafiği", "icon": "double-check", "url": null},
                    {"content":"Haftalık page view", "icon": "double-check", "url": null},
                    {"content":"Kısa link: ülke, cihaz, işletim sistemi, tarayıcı, dil, referrer", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name' => 'UseCases',
                'title' => 'Kimler için?',
                'description' => 'Kişisel markadan işletmeye, etkinlikten NFC karta kadar aynı altyapı.',
                'thumbnail' => null,
                'section_list' => '[
                    {"content":"İçerik Üreticileri|||Tüm sosyal hesaplarını, videolarını ve iş birliği linklerini tek profilde topla.", "icon": null, "url": null},
                    {"content":"Freelancerlar|||Portfolyo, iletişim ve proje linklerini müşterilerine tek bağlantıyla sun.", "icon": null, "url": null},
                    {"content":"İşletmeler|||Marka profili, kampanya linkleri ve QR ile fiziksel-dijital trafiği birleştir.", "icon": null, "url": null},
                    {"content":"Sosyal Medya|||Instagram, TikTok ve diğer platformlardan tek sabit linkle yönlendir.", "icon": null, "url": null},
                    {"content":"Etkinlik & Fuar|||Kayıt, harita ve sponsor linklerini QR ile hızlıca paylaş.", "icon": null, "url": null},
                    {"content":"NFC Kart Kullanıcıları|||NFC kartını LinkProfilde profil adresine yönlendir.", "icon": null, "url": null}
                ]',
            ],
            [
                'name' => 'Follow On',
                'title' => 'Takip:',
                'description' => null,
                'thumbnail' => null,
                'section_list' => '[
                    {"content": null, "icon": "twitter", "url": "https://twitter.com"},
                    {"content": null, "icon": "linkedin", "url": "https://linkedin.com"},
                    {"content": null, "icon": "facebook", "url": "https://facebook.com"},
                    {"content": null, "icon": "youtube", "url": "https://youtube.com"}
                ]',
            ],
            [
                'name' => 'Support',
                'title' => 'Sık sorulan sorular',
                'description' => 'LinkProfilde hakkında en çok merak edilenler.',
                'thumbnail' => null,
                'section_list' => '[
                    {"content": "LinkProfilde nedir?|||LinkProfilde; bio link profili, kısa link yönetimi, QR kod oluşturma ve analytics özelliklerini bir araya getiren bir dijital profil platformudur.", "icon": null, "url": null},
                    {"content": "Bio Link nedir?|||Bio Link, tüm önemli bağlantılarını, sosyal hesaplarını ve içeriklerini tek bir kişisel profil sayfasında toplamanı sağlar.", "icon": null, "url": null},
                    {"content": "QR kod oluşturabilir miyim?|||Evet. Renk, boyut, köşe stili ve logo ile QR kod oluşturup indirebilir; projelerin altında düzenleyebilirsin.", "icon": null, "url": null},
                    {"content": "Kısa linklerimin performansını görebilir miyim?|||Evet. Kısa linklerin için ülke, cihaz, işletim sistemi, tarayıcı, dil ve referrer gibi analytics verilerini görebilirsin.", "icon": null, "url": null},
                    {"content": "Profilimi özelleştirebilir miyim?|||Evet. Hazır temalar, sosyal bağlantılar, içerik blokları ve plana göre özel tema ile profilini özelleştirebilirsin.", "icon": null, "url": null},
                    {"content": "NFC kartımla kullanabilir miyim?|||Evet. LinkProfilde public profil URL’sini NFC kartına yazarak kartını dijital profiline yönlendirebilirsin. NFC donanımı ayrıca satılmaz veya entegre edilmez.", "icon": null, "url": null}
                ]',
            ],
            [
                'name' => 'FinalCTA',
                'title' => 'Dijital dünyanı tek bağlantıda birleştir.',
                'description' => 'LinkProfilde profilini dakikalar içinde oluştur ve paylaşmaya başla.',
                'thumbnail' => null,
                'section_list' => null,
            ],
            [
                'name' => 'Address',
                'title' => 'Adres',
                'description' => null,
                'thumbnail' => null,
                'section_list' => '[
                    {"content": "Mustafa Kemal, Deniz Life, 2139. Sk. No:15, 06000 Çankaya/Ankara", "icon": null, "url": null},
                    {"content": "0850 840 3011", "icon": null, "url": null},
                    {"content": "info@linkprofilde.com", "icon": null, "url": null},
                    {"content": "www.linkprofilde.com", "icon": null, "url": null}
                ]',
            ],
        );

        foreach ($appSections as $value1) {
            $existing = AppSection::query()->where('name', $value1['name'])->first();
            if ($existing) {
                continue;
            }

            $section = new AppSection();
            $section->forceFill([
                'name' => $value1['name'],
                'title' => $value1['title'],
                'description' => $value1['description'],
                'thumbnail' => $value1['thumbnail'],
                'section_list' => $value1['section_list'],
            ])->save();
        }
    }
}
