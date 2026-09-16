<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $posts = [
            [
                'title' => 'Bio Link Nedir? Ne İşe Yarar ve Kimler Kullanmalı?',
                'slug' => 'bio-link-nedir',
                'focus_keyword' => 'bio link nedir',
                'excerpt' => 'Bio link nedir, hangi platformlarda kullanılır ve kişisel marka ya da işletme için hangi bağlantıları tek sayfada toplamak mantıklıdır?',
                'meta_title' => 'Bio Link Nedir? Ne İşe Yarar? | LinkProfilde',
                'meta_description' => 'Bio link nedir ve ne işe yarar? Sosyal medya, web sitesi, WhatsApp, portfolyo ve kampanya bağlantılarını tek sayfada toplama rehberi.',
                'content' => '<p><strong>Bio link</strong>, bir kişinin veya markanın farklı dijital bağlantılarını tek bir web sayfasında toplamasını sağlayan bağlantı sayfasıdır. Özellikle Instagram ve TikTok gibi profillerde sınırlı bağlantı alanını daha verimli kullanmak için tercih edilir.</p><h2>Bio link ne işe yarar?</h2><p>Tek bir URL paylaşırsınız; ziyaretçi bu sayfadan web sitenize, WhatsApp hattınıza, YouTube kanalınıza, mağazanıza, portfolyonuza veya diğer sosyal hesaplarınıza geçebilir. Böylece farklı platformlarda farklı URL paylaşmak zorunda kalmazsınız.</p><h2>Kimler kullanabilir?</h2><ul><li>İçerik üreticileri ve influencerlar</li><li>Freelancer ve danışmanlar</li><li>Küçük işletmeler ve mağazalar</li><li>Etkinlik ve fuar ekipleri</li><li>Portfolyo paylaşan profesyoneller</li><li>Dijital kartvizit kullanan ekipler</li></ul><h2>Bio link ile normal web sitesi arasındaki fark nedir?</h2><p>Web sitesi genellikle çok sayfalı ve ayrıntılıdır. Bio link ise hızlı karar verilen mobil ziyaretlerde önemli aksiyonları tek ekranda sunmaya odaklanır. Bu nedenle web sitesinin yerine geçmekten çok ona ve diğer kanallara yönlendiren bir merkez görevi görür.</p><h2>İyi bir bio link nasıl olmalı?</h2><p>Sayfa hızlı açılmalı, kim olduğunuz ilk bakışta anlaşılmalı ve en önemli aksiyonlar üst bölümde görünmelidir. Çok fazla buton yerine ziyaretçinin gerçekten ihtiyaç duyduğu bağlantıları göstermek daha etkilidir.</p><p>LinkProfilde, bio link profilini QR kod, vCard, kısa link ve analitik özellikleriyle aynı panelde yönetmek isteyen kullanıcılar için bu yapıyı tek üründe birleştirir.</p>',
            ],
            [
                'title' => 'Instagram’da Tek Linke Birden Fazla Link Nasıl Eklenir?',
                'slug' => 'instagram-tek-linke-birden-fazla-link-ekleme',
                'focus_keyword' => 'instagram tek linke birden fazla link ekleme',
                'excerpt' => 'Instagram profilinde tek bağlantı üzerinden WhatsApp, YouTube, web sitesi, mağaza ve diğer hesaplara yönlendirme yapmanın pratik yolu.',
                'meta_title' => 'Instagram Tek Linke Birden Fazla Link Ekleme | LinkProfilde',
                'meta_description' => 'Instagram’da tek linke birden fazla link ekleyin. WhatsApp, YouTube, TikTok, mağaza ve web sitesi bağlantılarını tek profilde toplayın.',
                'content' => '<p>Instagram profilinizde birden fazla hedefe trafik göndermek istiyorsanız her kampanya döneminde profil linkini değiştirmek yerine bir bio link sayfası kullanabilirsiniz. Bu sayfa tek URL altında birden fazla buton ve sosyal bağlantı barındırır.</p><h2>Nasıl yapılır?</h2><ol><li>Bir bio link profili oluşturun.</li><li>Web sitesi, WhatsApp, YouTube, mağaza veya randevu linklerinizi ekleyin.</li><li>En önemli bağlantıyı en üst sıraya taşıyın.</li><li>Oluşan tek profil adresini Instagram bio alanına yerleştirin.</li></ol><h2>Kaç bağlantı eklemek doğru?</h2><p>Teknik olarak çok sayıda bağlantı eklenebilir ancak kullanıcı deneyimi açısından öncelikli 4-8 bağlantı çoğu profil için yeterlidir. Daha fazla içerik varsa başlıklarla gruplamak veya eski kampanyaları kaldırmak sayfayı daha anlaşılır tutar.</p><h2>Bağlantı isimleri nasıl yazılmalı?</h2><p>“Tıkla” gibi belirsiz ifadeler yerine “WhatsApp’tan Teklif Al”, “Son Videoyu İzle”, “Ürünleri İncele” veya “Randevu Oluştur” gibi sonuç odaklı metinler kullanın.</p><p>LinkProfilde üzerinde bağlantıları sıralayabilir, sosyal hesapları ekleyebilir ve hangi aksiyonların daha fazla etkileşim aldığını takip edebilirsiniz.</p>',
            ],
            [
                'title' => 'WhatsApp Linki Nasıl Oluşturulur ve Bio Profile Eklenir?',
                'slug' => 'whatsapp-linki-nasil-olusturulur',
                'focus_keyword' => 'whatsapp link oluşturma',
                'excerpt' => 'Telefon numarasını paylaşmadan tıklanabilir WhatsApp bağlantısı kullanmak ve bu linki bio profilinde doğru şekilde konumlandırmak için rehber.',
                'meta_title' => 'WhatsApp Linki Nasıl Oluşturulur? | LinkProfilde',
                'meta_description' => 'WhatsApp link oluşturma ve bio profile ekleme rehberi. Ziyaretçileri tek tıkla WhatsApp sohbetine yönlendirin.',
                'content' => '<p>WhatsApp bağlantısı, ziyaretçinin telefon numaranızı rehbere kaydetmeden doğrudan sohbet ekranını açmasını sağlar. Özellikle teklif, randevu, sipariş ve müşteri iletişimi için hızlı bir aksiyon sunar.</p><h2>WhatsApp bağlantısı nerede kullanılabilir?</h2><p>Instagram bio, dijital kartvizit, web sitesi, QR kod, e-posta imzası ve sosyal medya profillerinde kullanılabilir. Bağlantıyı tek başına paylaşmak mümkün olsa da diğer iletişim kanallarıyla birlikte bir bio profilinde sunmak daha düzenli olabilir.</p><h2>Bio profilde nereye yerleştirilmeli?</h2><p>WhatsApp işletmeniz için ana iletişim kanalıysa üst sıralarda görünmelidir. Buton adında kullanıcının ne yapacağı açıkça belirtilmelidir: “WhatsApp’tan Yaz”, “Fiyat Teklifi Al” veya “Randevu Sor” gibi.</p><h2>QR kodla WhatsApp’a yönlendirme</h2><p>WhatsApp bağlantısını QR kodun hedefi yapabilirsiniz. Ancak ileride numara veya süreç değişebilecekse dinamik QR kullanmak daha esnek olur. Alternatif olarak QR kodu doğrudan LinkProfilde profilinize bağlayıp WhatsApp ile diğer iletişim yollarını birlikte sunabilirsiniz.</p>',
            ],
            [
                'title' => 'QR Kod Tarama Sayısı Nasıl Ölçülür? QR Analitik Rehberi',
                'slug' => 'qr-kod-tarama-sayisi-nasil-olculur',
                'focus_keyword' => 'qr kod tarama sayısı',
                'excerpt' => 'Basılı materyaldeki QR kodun kaç kez tarandığını, hangi kampanyanın trafik getirdiğini ve tarama sonrası etkileşimi nasıl değerlendirebilirsiniz?',
                'meta_title' => 'QR Kod Tarama Sayısı Nasıl Ölçülür? | LinkProfilde',
                'meta_description' => 'QR kod tarama sayısını ölçme rehberi. Dinamik QR ile taramaları takip edin ve profil/link etkileşimleriyle birlikte değerlendirin.',
                'content' => '<p>Standart statik QR kod yalnızca hedef bilgiyi taşır; kendi başına tarama istatistiği üretmez. Tarama sayısını ölçmek için genellikle <strong>dinamik QR</strong> yapısı gerekir.</p><h2>Dinamik QR ile ölçüm nasıl çalışır?</h2><p>QR kod önce takip edilebilir bir yönlendirme adresine gider. Sistem taramayı kaydeder ve ardından ziyaretçiyi gerçek hedefe gönderir. Böylece QR görseli değişmeden tarama sayısı ölçülebilir.</p><h2>Hangi metriklere bakılmalı?</h2><ul><li>Toplam QR taraması</li><li>QR sonrası profil veya sayfa ziyareti</li><li>Bağlantı tıklamaları</li><li>WhatsApp veya sosyal medya geçişleri</li><li>vCard kaydı ve paylaşım gibi etkileşimler</li></ul><h2>Kampanya karşılaştırması nasıl yapılır?</h2><p>Farklı fiziksel alanlarda farklı QR kodlar kullanırsanız hangi materyalin daha fazla trafik ürettiğini ayırabilirsiniz. Örneğin mağaza vitrini, masa kartı ve fuar standı için ayrı dinamik QR kodlar oluşturulabilir.</p><p>LinkProfilde QR tarama verilerini profil ve bağlantı etkileşimleriyle aynı panelde göstermeyi amaçlar; böylece sadece tarama değil, tarama sonrasında ne olduğu da görülebilir.</p>',
            ],
            [
                'title' => 'QR Kodun Linki Sonradan Değiştirilebilir mi?',
                'slug' => 'qr-kod-linki-sonradan-degistirme',
                'focus_keyword' => 'qr kod link değiştirme',
                'excerpt' => 'Basılmış QR kodu değiştirmeden hedef web adresini güncellemek mümkün mü? Statik ve dinamik QR farkını pratik örneklerle açıklıyoruz.',
                'meta_title' => 'QR Kod Linki Sonradan Değişir mi? | LinkProfilde',
                'meta_description' => 'QR kodun hedef linkini sonradan değiştirmek için dinamik QR kullanın. Baskıyı yenilemeden yönlendirme adresini güncelleyin.',
                'content' => '<p>Bir QR kodun hedef linkinin sonradan değiştirilip değiştirilemeyeceği, kodun statik mi dinamik mi olduğuna bağlıdır. <strong>Statik QR kod</strong> doğrudan hedef URL’yi içerdiği için basıldıktan sonra değiştirilemez. <strong>Dinamik QR kod</strong> ise yönetilebilir bir yönlendirme katmanı kullanır.</p><h2>Dinamik QR neden avantajlı?</h2><p>Kartvizit, broşür, ürün etiketi veya fuar materyali basıldıktan sonra hedef sayfayı değiştirmeniz gerekebilir. Dinamik QR ile QR görselini yeniden üretmeden panel üzerinden hedef URL güncellenebilir.</p><h2>Örnek</h2><p>Bir etkinlik öncesinde QR kod kayıt sayfasına gider. Etkinlik bittikten sonra aynı QR kodu sunum dosyasına veya teşekkür sayfasına yönlendirebilirsiniz. Fiziksel materyal aynı kalır.</p><h2>Ne zaman statik QR yeterlidir?</h2><p>Hedefin kesinlikle değişmeyeceği ve analitik ihtiyacınızın olmadığı basit kullanımda statik QR yeterli olabilir. Uzun ömürlü baskı ve kampanya takibi için dinamik QR daha güvenli seçimdir.</p>',
            ],
            [
                'title' => 'Dijital Kartvizit mi Klasik Kartvizit mi? Farklar ve Birlikte Kullanım',
                'slug' => 'dijital-kartvizit-mi-klasik-kartvizit-mi',
                'focus_keyword' => 'dijital kartvizit',
                'excerpt' => 'Klasik baskılı kartvizit ile dijital kartvizitin farklarını, QR ve NFC ile birlikte kullanım senaryolarını ve hangi durumda hangisinin mantıklı olduğunu karşılaştırıyoruz.',
                'meta_title' => 'Dijital Kartvizit mi Klasik Kartvizit mi? | LinkProfilde',
                'meta_description' => 'Dijital ve klasik kartvizit farkları. QR, NFC, vCard ve güncellenebilir iletişim bilgileriyle hibrit kullanım seçeneklerini inceleyin.',
                'content' => '<p>Klasik kartvizit fiziksel temasın güçlü olduğu toplantı, fuar ve satış görüşmelerinde hâlâ kullanışlıdır. Dijital kartvizit ise iletişim bilgilerinin güncellenebilir, tıklanabilir ve ölçülebilir olmasını sağlar. En pratik yaklaşım çoğu zaman ikisini birlikte kullanmaktır.</p><h2>Klasik kartvizitin güçlü yönleri</h2><ul><li>Fiziksel olarak kolay verilir</li><li>Marka ve baskı kalitesini hissettirir</li><li>Telefon gerektirmeden ilk temas kurulabilir</li></ul><h2>Dijital kartvizitin güçlü yönleri</h2><ul><li>Telefon, e-posta ve sosyal hesaplar tıklanabilir</li><li>Bilgiler baskı yenilenmeden güncellenebilir</li><li>vCard ile rehbere kayıt kolaylaşır</li><li>QR ve NFC ile paylaşılabilir</li><li>Profil etkileşimleri ölçülebilir</li></ul><h2>Hibrit kullanım</h2><p>Basılı kartvizit üzerinde LinkProfilde profilinize giden dinamik QR kod ve isteğe bağlı NFC kullanabilirsiniz. Kullanıcı fiziksel kartı alır; detaylı iletişim ve sosyal bağlantılar için dijital profile geçer.</p>',
            ],
            [
                'title' => 'Freelancer İçin Bio Link ve Dijital Portfolyo Nasıl Hazırlanır?',
                'slug' => 'freelancer-icin-bio-link',
                'focus_keyword' => 'freelancer bio link',
                'excerpt' => 'Freelancerlar için portfolyo, hizmetler, referanslar, iletişim ve sosyal hesapları tek bir profesyonel profil bağlantısında toplama rehberi.',
                'meta_title' => 'Freelancer İçin Bio Link ve Portfolyo Rehberi | LinkProfilde',
                'meta_description' => 'Freelancer bio link profili oluşturun. Portfolyo, hizmet, iletişim, WhatsApp ve sosyal medya bağlantılarını tek adreste toplayın.',
                'content' => '<p>Freelancer olarak müşteriyle ilk temas çoğu zaman Instagram, LinkedIn, WhatsApp veya e-posta üzerinden gerçekleşir. Her kanalda ayrı ayrı portfolyo ve iletişim linki göndermek yerine tek bir profesyonel profil adresi kullanmak süreci sadeleştirir.</p><h2>Profilde hangi bölümler olmalı?</h2><ul><li>Ad, uzmanlık ve kısa değer önerisi</li><li>Portfolyo veya örnek işler</li><li>Hizmetler</li><li>Teklif veya iletişim bağlantısı</li><li>LinkedIn ve diğer profesyonel sosyal hesaplar</li><li>WhatsApp veya e-posta</li></ul><h2>Portfolyo bağlantısı nasıl sunulmalı?</h2><p>En iyi 3-5 işi öne çıkarmak çoğu zaman onlarca örnek göstermenin önüne geçer. Ziyaretçiyi önce uzmanlık alanınıza, sonra ilgili örneklere ve en sonunda iletişim aksiyonuna yönlendiren sıra oluşturun.</p><h2>QR ve NFC kullanımı</h2><p>Toplantı ve etkinliklerde aynı profil URL’sini QR veya NFC kart üzerinden paylaşabilirsiniz. Böylece fiziksel kartvizit ile dijital portfolyo arasında kopukluk kalmaz.</p>',
            ],
            [
                'title' => 'İşletme İçin Dijital Kartvizit Nasıl Hazırlanır?',
                'slug' => 'isletme-icin-dijital-kartvizit',
                'focus_keyword' => 'işletme dijital kartvizit',
                'excerpt' => 'Şirket veya küçük işletme için telefon, WhatsApp, konum, sosyal medya, ürün ve randevu bağlantılarını tek dijital kartvizitte toplama rehberi.',
                'meta_title' => 'İşletme İçin Dijital Kartvizit Rehberi | LinkProfilde',
                'meta_description' => 'İşletme dijital kartviziti hazırlayın. WhatsApp, telefon, web sitesi, sosyal medya ve kampanya bağlantılarını QR ve NFC ile paylaşın.',
                'content' => '<p>İşletmelerde kartvizit yalnızca ad ve telefon bilgisi taşımak zorunda değildir. Dijital kartvizit; iletişim, konum, sosyal medya, kampanya, katalog ve randevu gibi farklı hedefleri tek profil altında toplar.</p><h2>Temel bilgiler</h2><p>İşletme adı, faaliyet alanı, telefon, WhatsApp, e-posta ve web sitesi profilin temelini oluşturur. Ardından müşteri davranışına göre harita, ürün kataloğu, menü, rezervasyon veya sosyal medya bağlantıları eklenebilir.</p><h2>Personel bazlı mı şirket bazlı mı?</h2><p>Satış ekibi gibi bireysel iletişimin önemli olduğu yapılarda her personel için ayrı profil oluşturulabilir. Marka iletişiminin merkezi olduğu işletmelerde ise tek şirket profili daha uygundur.</p><h2>QR kod nereye eklenebilir?</h2><p>Basılı kartvizit, masa üstü stand, mağaza vitrini, fuar alanı, broşür ve araç üzeri materyaller QR kod için kullanılabilir. Dinamik QR hedefi daha sonra değiştirilebildiği için uzun ömürlü baskılarda avantaj sağlar.</p>',
            ],
            [
                'title' => 'Etkinlik ve Fuar İçin QR Kod Nasıl Kullanılır?',
                'slug' => 'etkinlik-fuar-icin-qr-kod',
                'focus_keyword' => 'etkinlik için qr kod',
                'excerpt' => 'Fuar standı, etkinlik kaydı, katalog, harita ve iletişim süreçlerinde QR kodları doğru hedeflere bağlamak ve performansı ölçmek için rehber.',
                'meta_title' => 'Etkinlik ve Fuar İçin QR Kod Kullanımı | LinkProfilde',
                'meta_description' => 'Etkinlik ve fuar için QR kod oluşturun. Kayıt, katalog, iletişim, dijital kartvizit ve kampanya hedeflerini dinamik olarak yönetin.',
                'content' => '<p>Etkinlik ve fuarlarda ziyaretçiyle temas süresi kısadır. QR kod, uzun URL yazmak veya broşürde çok fazla bilgi göstermek yerine kullanıcıyı hızlıca dijital içeriğe taşır.</p><h2>QR kod hangi hedeflere gidebilir?</h2><ul><li>Etkinlik kayıt formu</li><li>Ürün veya hizmet kataloğu</li><li>Dijital kartvizit</li><li>WhatsApp iletişim</li><li>Sunum veya tanıtım videosu</li><li>Konum ve program bilgisi</li></ul><h2>Tek QR mı, birden fazla QR mı?</h2><p>Farklı amaçların performansını ayrı ölçmek istiyorsanız stand, broşür ve sunum için farklı QR kodlar kullanmak daha sağlıklıdır. Tek profil altında birden fazla bağlantı sunmak istiyorsanız QR’ı bio link profiline yönlendirebilirsiniz.</p><h2>Etkinlik sonrası ne olur?</h2><p>Dinamik QR kullanıyorsanız hedefi etkinlik sonrasında teşekkür sayfası, kampanya veya takip formuna çevirebilirsiniz. Böylece basılı materyal boşa çıkmaz.</p>',
            ],
            [
                'title' => 'Link Analizi Nedir? Tıklama ve Etkileşim Verileri Nasıl Okunur?',
                'slug' => 'link-analizi-nedir',
                'focus_keyword' => 'link analizi',
                'excerpt' => 'Link tıklaması, profil görüntüleme, sosyal geçiş, QR taraması ve diğer etkileşimleri birlikte okuyarak daha anlamlı kararlar verme rehberi.',
                'meta_title' => 'Link Analizi Nedir? Tıklama ve Etkileşim Rehberi | LinkProfilde',
                'meta_description' => 'Link analizi nedir? Tıklama, profil görüntüleme, sosyal medya geçişi, QR taraması, paylaşım ve vCard etkileşimlerini yorumlayın.',
                'content' => '<p>Link analizi, bir bağlantının yalnızca kaç kez açıldığını değil, ziyaretçinin hangi kanaldan geldiğini ve hangi aksiyona geçtiğini anlamaya çalışır. Bio link ve kısa link kullanımında bu veriler içerik ve kampanya kararlarını destekler.</p><h2>Temel metrikler</h2><ul><li>Profil veya link görüntüleme</li><li>Bağlantı tıklaması</li><li>Sosyal medya geçişi</li><li>QR taraması</li><li>Paylaşım</li><li>vCard kaydı</li></ul><h2>Tıklama oranı nasıl yorumlanır?</h2><p>Profil çok görüntüleniyor ancak önemli butonlar az tıklanıyorsa başlık, sıralama veya teklif yeterince net olmayabilir. Az trafik alan ama yüksek tıklama oranına sahip profil ise doğru kitleye ulaşıyor olabilir.</p><h2>Veriyi bağlamla değerlendirin</h2><p>Tek bir sayıya odaklanmak yerine kampanya dönemi, trafik kaynağı, cihaz ve hedef aksiyon birlikte incelenmelidir. LinkProfilde; profil, kısa link, QR ve etkileşim verilerini tek panelde bir araya getirerek bu resmi daha okunur hale getirir.</p>',
            ],
            [
                'title' => 'LinkProfilde Nedir? Bio Link, QR Kod, Kısa Link ve Analitik Tek Platformda',
                'slug' => 'linkprofilde-nedir',
                'focus_keyword' => 'LinkProfilde nedir',
                'excerpt' => 'LinkProfilde’nin hangi problemleri çözdüğünü, bio link, dijital kartvizit, dinamik QR, kısa link ve analitik özelliklerinin nasıl birlikte çalıştığını öğrenin.',
                'meta_title' => 'LinkProfilde Nedir? Özellikler ve Kullanım Alanları',
                'meta_description' => 'LinkProfilde nedir? Bio link, dijital kartvizit, dinamik QR kod, kısa link ve etkileşim analitiğini tek panelde yönetin.',
                'content' => '<p><strong>LinkProfilde</strong>, dijital profil ve bağlantı yönetimini tek panelde toplamak için geliştirilen web tabanlı bir platformdur. Kullanıcılar bio link profili oluşturabilir, kısa linklerini yönetebilir, dinamik QR kodlar hazırlayabilir ve bağlantı etkileşimlerini takip edebilir.</p><h2>LinkProfilde hangi ihtiyaçları çözer?</h2><ul><li>Sosyal medya ve web bağlantılarını tek URL’de toplama</li><li>Dijital kartvizit oluşturma</li><li>QR kod ile fiziksel materyalden dijital profile geçiş</li><li>Uzun URL’leri kısa linklerle yönetme</li><li>Profil ve bağlantı etkileşimlerini ölçme</li></ul><h2>Bio link ve dijital profil</h2><p>Profil adı, kısa açıklama, sosyal hesaplar, iletişim bağlantıları ve içerik blokları tek bir mobil uyumlu sayfada yayınlanabilir. Profil adresi Instagram bio, TikTok, LinkedIn, e-posta imzası, QR veya NFC kartta kullanılabilir.</p><h2>Dinamik QR kod</h2><p>Dinamik QR kodlarda hedef adres daha sonra değiştirilebilir ve tarama olayları ölçülebilir. Bu özellik kartvizit, fuar, mağaza, broşür ve kampanya materyallerinde kullanışlıdır.</p><h2>Kısa link ve analitik</h2><p>Uzun bağlantılar sadeleştirilerek paylaşılabilir ve ziyaret performansı takip edilebilir. Profil içindeki link tıklamaları, sosyal geçişler, QR taramaları, paylaşım ve vCard gibi etkileşimler de analitik görünümüne dahil edilir.</p><h2>Kimler kullanabilir?</h2><p>İçerik üreticileri, freelancerlar, danışmanlar, küçük işletmeler, satış ekipleri, etkinlik organizasyonları ve dijital kartvizit kullanan profesyoneller LinkProfilde’yi farklı senaryolarda kullanabilir.</p>',
            ],
        ];

        foreach ($posts as $index => $post) {
            DB::table('blog_posts')->updateOrInsert(
                ['slug' => $post['slug']],
                array_merge($post, [
                    'author_name' => 'LinkProfilde Editör Ekibi',
                    'is_published' => true,
                    'published_at' => $now->copy()->subDays(20 - $index),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('blog_posts')->whereIn('slug', [
            'bio-link-nedir',
            'instagram-tek-linke-birden-fazla-link-ekleme',
            'whatsapp-linki-nasil-olusturulur',
            'qr-kod-tarama-sayisi-nasil-olculur',
            'qr-kod-linki-sonradan-degistirme',
            'dijital-kartvizit-mi-klasik-kartvizit-mi',
            'freelancer-icin-bio-link',
            'isletme-icin-dijital-kartvizit',
            'etkinlik-fuar-icin-qr-kod',
            'link-analizi-nedir',
            'linkprofilde-nedir',
        ])->delete();
    }
};
