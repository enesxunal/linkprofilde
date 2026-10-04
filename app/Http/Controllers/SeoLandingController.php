<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\BlogPost;

class SeoLandingController extends Controller
{
    private const PAGES = [
        'bio-link' => [
            'slug' => 'bio-link',
            'title' => 'Ücretsiz Bio Link Oluştur | LinkProfilde',
            'description' => 'Instagram, TikTok, YouTube, WhatsApp ve diğer bağlantılarını tek bir bio link sayfasında topla. Ücretsiz başla, profilini özelleştir ve tıklamaları takip et.',
            'eyebrow' => 'Bio Link',
            'h1' => 'Tüm bağlantılarını tek bir bio linkte topla',
            'lead' => 'Sosyal medya profillerini, web siteni, iletişim kanallarını ve öne çıkarmak istediğin içerikleri tek bir paylaşılabilir sayfada birleştir.',
            'benefits' => [
                ['title' => 'Tek bağlantı, çok kanal', 'text' => 'Instagram, TikTok, YouTube, WhatsApp, web sitesi ve kampanya linklerini tek sayfada sun.'],
                ['title' => 'Markana uygun görünüm', 'text' => 'Profil görseli, açıklama, bağlantılar ve tema seçenekleriyle kişisel veya kurumsal sayfanı özelleştir.'],
                ['title' => 'Performansı ölç', 'text' => 'Ziyaretleri ve bağlantı etkileşimlerini tek panelden takip ederek hangi içeriğin daha çok ilgi gördüğünü gör.'],
            ],
            'steps' => [
                'Ücretsiz hesabını oluştur ve ilk bio link profilini aç.',
                'Profil fotoğrafı, kısa açıklama ve sosyal medya hesaplarını ekle.',
                'Önemli bağlantılarını sırala ve görünümü markana göre düzenle.',
                'Tek URL’yi sosyal medya biyografilerinde, e-posta imzanda veya QR kodunda paylaş.',
            ],
            'use_cases' => [
                'İçerik üreticileri ve influencer’lar',
                'Freelancer ve danışmanlar',
                'Restoran, mağaza ve yerel işletmeler',
                'Etkinlik ve kampanya sayfaları',
            ],
            'faq' => [
                ['q' => 'Bio link nedir?', 'a' => 'Bio link; birden fazla bağlantıyı tek bir paylaşılabilir sayfada toplamanızı sağlayan bağlantıdır. Özellikle Instagram ve TikTok gibi profil alanlarında tek URL üzerinden tüm kanallarınıza yönlendirme yapmak için kullanılır.'],
                ['q' => 'LinkProfilde bio link ücretsiz mi?', 'a' => 'LinkProfilde üzerinde ücretsiz başlayabilir ve temel bio link profilinizi oluşturabilirsiniz. Kullanılabilen özellikler seçtiğiniz plana göre genişleyebilir.'],
                ['q' => 'Bio link sayfam Google’da görünebilir mi?', 'a' => 'Yeterli ve anlamlı içerik içeren herkese açık profiller arama motorları tarafından taranabilir. Boş veya çok ince profiller indeks kalitesini korumak için noindex olarak işaretlenir.'],
            ],
            'blog_slugs' => ['bio-link-nedir','ucretsiz-bio-link-olusturma','instagram-bio-link-nasil-olusturulur','sosyal-medya-linklerini-tek-linkte-toplama'],
        ],
        'dijital-kartvizit' => [
            'slug' => 'dijital-kartvizit',
            'title' => 'Dijital Kartvizit Oluştur | LinkProfilde',
            'description' => 'Telefon, WhatsApp, sosyal medya ve web bağlantılarını tek dijital kartvizitte topla. QR kodla veya linkle paylaş, bilgilerini istediğin zaman güncelle.',
            'eyebrow' => 'Dijital Kartvizit',
            'h1' => 'Güncellenebilir ve paylaşılabilir dijital kartvizit oluştur',
            'lead' => 'İletişim bilgilerini, sosyal hesaplarını, web siteni ve önemli bağlantılarını tek bir profesyonel dijital profil altında birleştir.',
            'benefits' => [
                ['title' => 'Her zaman güncel', 'text' => 'Telefon, unvan, web sitesi veya sosyal medya bilgilerin değiştiğinde baskı maliyeti olmadan profilini güncelle.'],
                ['title' => 'Link ve QR ile paylaş', 'text' => 'Kartvizitini mesajla, e-posta imzasında veya QR kod üzerinden fiziksel materyallerde paylaş.'],
                ['title' => 'Tek profesyonel profil', 'text' => 'İletişim, sosyal medya ve marka bağlantılarını dağınık olmadan tek sayfada sun.'],
            ],
            'steps' => [
                'Hesabını oluştur ve dijital profilini aç.',
                'İsim, açıklama, iletişim ve sosyal medya bilgilerini ekle.',
                'Renk ve görünümü markana göre düzenle.',
                'Kartvizit linkini veya QR kodunu müşterilerinle paylaş.',
            ],
            'use_cases' => [
                'Satış ekipleri ve danışmanlar',
                'Ajans ve freelancer ekipleri',
                'Fuar ve etkinlik katılımcıları',
                'Küçük işletmeler ve girişimciler',
            ],
            'faq' => [
                ['q' => 'Dijital kartvizit nedir?', 'a' => 'Dijital kartvizit, iletişim ve profil bilgilerinizi web tabanlı tek bir sayfada sunar. Fiziksel kartvizitten farklı olarak bilgiler sonradan güncellenebilir.'],
                ['q' => 'Dijital kartvizit QR kodla paylaşılabilir mi?', 'a' => 'Evet. Dijital kartvizit bağlantınızı QR koda bağlayarak basılı kartvizit, masaüstü materyali, broşür veya fuar standında kullanabilirsiniz.'],
                ['q' => 'Fiziksel kartvizitin yerini tamamen alır mı?', 'a' => 'Kullanım senaryosuna göre tek başına kullanılabilir veya fiziksel kartviziti tamamlayabilir. En büyük avantajı bilgilerin güncellenebilir ve ölçülebilir olmasıdır.'],
            ],
            'blog_slugs' => ['dijital-kartvizit-nasil-olusturulur','dijital-kartvizit-mi-klasik-kartvizit-mi','nfc-kartvizit-nedir','isletme-icin-dijital-kartvizit'],
        ],
        'qr-kod' => [
            'slug' => 'qr-kod',
            'title' => 'Ücretsiz QR Kod Oluştur ve Yönet | LinkProfilde',
            'description' => 'Web sitesi, bio link veya kampanyan için QR kod oluştur. Dinamik QR ile hedef bağlantıyı sonradan değiştir, taramaları takip et ve tek panelden yönet.',
            'eyebrow' => 'QR Kod',
            'h1' => 'Paylaşılabilir ve yönetilebilir QR kodlar oluştur',
            'lead' => 'Dijital profilini, web siteni veya kampanya bağlantını QR koda dönüştür. Dinamik QR kullanarak hedefi sonradan değiştirebilir ve tarama performansını ölçebilirsin.',
            'benefits' => [
                ['title' => 'Dinamik hedef yönetimi', 'text' => 'QR kodu yeniden basmadan bağlı olduğu hedef adresi değiştirebilirsin.'],
                ['title' => 'Fizikselden dijitale geçiş', 'text' => 'Broşür, menü, ambalaj, kartvizit ve etkinlik materyallerinden kullanıcıları dijital içeriğe yönlendir.'],
                ['title' => 'Tarama analitiği', 'text' => 'QR kod kullanımını ve etkileşimini takip ederek kampanyalarının performansını ölç.'],
            ],
            'steps' => [
                'QR kodun yönlendireceği hedefi seç.',
                'Kodunu oluştur ve gerekiyorsa görünümünü özelleştir.',
                'PNG olarak indirip dijital veya basılı materyaline ekle.',
                'Dinamik hedef ve tarama verilerini panelden yönet.',
            ],
            'use_cases' => [
                'Restoran menüleri ve masaüstü materyalleri',
                'Fuar, etkinlik ve stand yönlendirmeleri',
                'Ürün ambalajları ve kampanyalar',
                'Kartvizit ve basılı tanıtım materyalleri',
            ],
            'faq' => [
                ['q' => 'Dinamik QR kod nedir?', 'a' => 'Dinamik QR kod, basılı kod değişmeden arka plandaki hedef bağlantının sonradan güncellenebilmesini sağlar.'],
                ['q' => 'QR kodun linkini sonradan değiştirebilir miyim?', 'a' => 'Dinamik QR kod kullanıyorsanız hedef adresi panelden güncelleyebilirsiniz. Böylece QR görselini yeniden üretmeniz gerekmez.'],
                ['q' => 'QR kod taramaları ölçülebilir mi?', 'a' => 'LinkProfilde dinamik QR yapısında tarama kayıtları tutulabilir ve ilgili QR kodun performansı panel üzerinden takip edilebilir.'],
            ],
            'blog_slugs' => ['ucretsiz-qr-kod-olusturma','dinamik-qr-kod-nedir','qr-kod-linki-sonradan-degistirme','qr-kod-tarama-sayisi-nasil-olculur'],
        ],
        'kisa-link' => [
            'slug' => 'kisa-link',
            'title' => 'Kısa Link Oluştur ve Tıklamaları Takip Et | LinkProfilde',
            'description' => 'Uzun URL’lerini kısa ve paylaşılabilir bağlantılara dönüştür. Kısa linklerini tek panelden yönet, kampanyalarda kullan ve tıklama performansını ölç.',
            'eyebrow' => 'Kısa Link',
            'h1' => 'Uzun bağlantılarını kısa, sade ve ölçülebilir hale getir',
            'lead' => 'Kampanya, sosyal medya ve paylaşım linklerini daha düzenli hale getir. Kısa linklerini tek panelden yönet ve ziyaret performansını takip et.',
            'benefits' => [
                ['title' => 'Daha temiz paylaşım', 'text' => 'Uzun ve karmaşık URL’leri mesaj, sosyal medya ve kampanyalarda daha sade biçimde kullan.'],
                ['title' => 'Merkezi yönetim', 'text' => 'Farklı kısa linkleri tek panelde tutarak kampanya bağlantılarını daha kolay yönet.'],
                ['title' => 'Tıklama analitiği', 'text' => 'Kısa bağlantı ziyaretlerini takip ederek hangi kanalın daha fazla trafik getirdiğini gör.'],
            ],
            'steps' => [
                'Kısaltmak istediğin hedef URL’yi belirle.',
                'LinkProfilde panelinden kısa bağlantını oluştur.',
                'Kısa URL’yi sosyal medya, mesaj veya reklam kampanyalarında paylaş.',
                'Ziyaret ve etkileşim verilerini panelden incele.',
            ],
            'use_cases' => [
                'Sosyal medya kampanyaları',
                'SMS ve WhatsApp paylaşımları',
                'E-posta ve reklam bağlantıları',
                'Offline kampanyalardaki QR hedefleri',
            ],
            'faq' => [
                ['q' => 'Kısa link ne işe yarar?', 'a' => 'Kısa link, uzun URL’leri daha kolay paylaşılabilir hale getirir ve bağlantı trafiğinin merkezi olarak yönetilmesini kolaylaştırır.'],
                ['q' => 'Kısa link tıklamaları takip edilebilir mi?', 'a' => 'Evet. LinkProfilde üzerinden oluşturulan bağlantılar için ziyaret ve etkileşim analitiği görüntülenebilir.'],
                ['q' => 'Kısa link SEO için zararlı mı?', 'a' => 'Kısa link doğru yönlendirme mantığıyla kullanıldığında kampanya ve paylaşım yönetimi için uygundur. Arama motorlarında sıralanması gereken ana içerik için kalıcı ve açıklayıcı URL kullanmak daha doğrudur.'],
            ],
            'blog_slugs' => ['kisa-link-nasil-olusturulur','link-analizi-nedir','whatsapp-linki-nasil-olusturulur','sosyal-medya-linklerini-tek-linkte-toplama'],
        ],
    ];

    public function show(string $slug)
    {
        abort_unless(isset(self::PAGES[$slug]), 404);

        $app = AppSetting::first() ?? (object) [
            'title' => config('app.name', 'LinkProfilde'),
            'description' => 'Bio link, dijital kartvizit, kısa link, QR kod ve analitik araçları.',
            'logo' => 'assets/icons/link-drop.png',
        ];

        $page = self::PAGES[$slug];
        $related = collect(self::PAGES)
            ->reject(fn ($item) => $item['slug'] === $slug)
            ->values();

        $posts = BlogPost::published()
            ->whereIn('slug', $page['blog_slugs'])
            ->get()
            ->sortBy(fn ($post) => array_search($post->slug, $page['blog_slugs'], true))
            ->values();

        return view('seo.landing', compact('app', 'page', 'related', 'posts'));
    }

    public static function sitemapPages(): array
    {
        return array_values(self::PAGES);
    }
}
