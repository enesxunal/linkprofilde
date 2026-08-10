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
                'name'=>'Header',
                'title'=>'Tüm Linkler Profildeki Link’te!',
                'description'=>'',
                'thumbnail'=>'assets/themes/theme-group2.png',
                'section_list'=>'[
                    {"content": "Ücretsiz Link", "icon": "link", "url": null},
                    {"content": "Premium Link", "icon": "premium", "url": null},
                    {"content": "QR Kod Oluşturucu", "icon": "qrcode", "url": null},
                    {"content": "Link Kısaltma", "icon": "link-slash", "url": null}
                ]',
            ],
            [
                'name'=>'Features',
                'title'=>'Features',
                'description'=>null,
                'thumbnail'=>null,
                'section_list'=>'[
                    {"content": "Link Kısaltma", "icon": "link-slash", "url": null},
                    {"content": "19+ Tema", "icon": "palette", "url": null},
                    {"content": "Ziyaretçi Takibi", "icon": "chart-line-up", "url": null},
                    {"content": "Tam Özelleştirme", "icon": "fill-drip", "url": null}
                ]',
            ],
            [
                'name'=>'Create Link',
                'title'=>'Basit bir formla link oluşturun',
                'description'=>'Benzersiz linkinizi oluşturun ve takipçilerinizin tüm önemli içeriklerinizi takip etmeleri için
                   linklerinizi ekleyin.',
                'thumbnail'=>'assets/create-link.svg',
                'section_list'=>'[
                    {"content": "Özel renkler", "icon": "double-check", "url": null},
                    {"content": "Ayarları özelleştirin veya sürükleyip bırakın", "icon": "double-check", "url": null},
                    {"content": "19+ Tema", "icon": "double-check", "url": null},
                    {"content": "Ayarları özelleştirin veya sürükleyip bırakın", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name'=>'Add Blocks',
                'title'=>'Block Ekle',
                'description'=>'Linkinizi özelleştirilebilir şekilde ayarlamak için sürükle ve bırak özelliğini kullanabilirsiniz.',
                'thumbnail'=>'assets/blocks.svg',
                'section_list'=>'[
                    {"content":"Özel renkler", "icon": "double-check", "url": null},
                    {"content":"Ayarları özelleştirin veya sürükleyip bırakın", "icon": "double-check", "url": null},
                    {"content":"19+ Tema", "icon": "double-check", "url": null},
                    {"content":"Ayarları özelleştirin veya sürükleyip bırakın", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name'=>'QR Codes',
                'title'=>'QR Kod Oluştur',
                'description'=>'Kendi benzersiz QR kodunuzu oluşturun ve içeriğini istediğiniz gibi düzenleyin.',
                'thumbnail'=>'assets/qr-code.svg',
                'section_list'=>'[
                    {"content":"Özel renkler", "icon": "double-check", "url": null},
                    {"content":"Ayarları özelleştirin veya sürükleyip bırakın", "icon": "double-check", "url": null},
                    {"content":"19+ Tema", "icon": "double-check", "url": null},
                    {"content":"Ayarları özelleştirin veya sürükleyip bırakın", "icon": "double-check", "url": null}
                ]',
            ],
            [
                'name'=>'Follow On',
                'title'=>'Takip:',
                'description'=>null,
                'thumbnail'=>null,
                'section_list'=>'[
                    {"content": null, "icon": "twitter", "url": "https://twitter.com"},
                    {"content": null, "icon": "linkedin", "url": "https://linkedin.com"},
                    {"content": null, "icon": "facebook", "url": "https://facebook.com"},
                    {"content": null, "icon": "youtube", "url": "https://youtube.com"}
                ]',
            ],
            [
                'name'=>'Support',
                'title'=>'Destek',
                'description'=>null,
                'thumbnail'=>null,
                'section_list'=>'[
                    {"content": "Help", "icon": null, "url": "#"},
                    {"content": "Getting Started", "icon": null, "url": "#"},
                    {"content": "FAQs", "icon": null, "url": "#"},
                    {"content": "Privacy Policy", "icon": null, "url": "#"},
                    {"content": "Terms & Conditions", "icon": null, "url": "#"}
                ]',
            ],
            [
                'name'=>'Address',
                'title'=>'Adres',
                'description'=>null,
                'thumbnail'=>null,
                'section_list'=>'[
                    {"content": "Mustafa Kemal, Deniz Life, 2139. Sk. No:15, 06000 Çankaya/Ankara", "icon": null, "url": null},
                    {"content": "0850 840 3011", "icon": null, "url": null},
                    {"content": "info@linkprofilde.com", "icon": null, "url": null},
                    {"content": "www.linkprofilde.com", "icon": null, "url": null}
                ]',
            ],
        );

        foreach ($appSections as $value1) {
            AppSection::create([
                'name' => $value1['name'],
                'title' => $value1['title'],
                'description' => $value1['description'],
                'thumbnail' => $value1['thumbnail'],
                'section_list' => $value1['section_list']
            ]);
        }
    }
}
