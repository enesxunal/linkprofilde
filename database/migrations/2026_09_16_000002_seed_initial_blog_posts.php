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
                'title' => 'Instagram Bio Link Nasıl Oluşturulur? Tek Linkte Tüm Bağlantılar',
                'slug' => 'instagram-bio-link-nasil-olusturulur',
                'focus_keyword' => 'instagram bio link oluşturma',
                'excerpt' => 'Instagram profilinde tek bağlantı alanını daha verimli kullanmak için bio link sayfası nasıl hazırlanır, hangi bağlantılar eklenir ve performans nasıl ölçülür?',
                'meta_title' => 'Instagram Bio Link Nasıl Oluşturulur? | LinkProfilde',
                'meta_description' => 'Instagram bio link oluşturma rehberi: sosyal medya, WhatsApp, web sitesi, portfolyo ve kampanya linklerini tek sayfada toplayın.',
                'content' => '<p>Instagram profilinde yalnızca birkaç saniyede karar veren ziyaretçiye doğru yönü göstermek önemlidir. Bio alanındaki bağlantıyı sadece ana sayfanıza vermek yerine, en önemli içeriklerinizi tek bir sayfada toplamak daha kullanışlı bir yapı sağlar. Bu yapıya genellikle <strong>bio link sayfası</strong> denir.</p><h2>Instagram bio link nedir?</h2><p>Bio link; web sitesi, WhatsApp, YouTube, TikTok, portfolyo, randevu, ürün, kampanya veya iletişim bağlantılarınızı tek bir URL altında toplamanızı sağlar. Böylece Instagram profilinizde tek bir bağlantı paylaşır, ziyaretçiyi içeride ihtiyacına göre yönlendirirsiniz.</p><h2>İyi bir bio link sayfasında neler olmalı?</h2><ul><li>Net bir profil adı ve kısa açıklama</li><li>Markayı veya kişiyi tanıtan profil görseli</li><li>En önemli 3-6 bağlantının üst sıralarda yer alması</li><li>WhatsApp, Instagram, YouTube ve diğer sosyal ağ kısayolları</li><li>Mobil ekranda rahat dokunulabilen butonlar</li><li>Gereksiz kalabalık oluşturmayan sade bir tasarım</li></ul><h2>Adım adım bio link oluşturma</h2><p>Önce hangi aksiyonu önceliklendireceğinize karar verin. Satış yapıyorsanız ürün veya iletişim, içerik üretiyorsanız son video veya kanal, hizmet veriyorsanız randevu ve teklif bağlantıları ilk sıralarda olmalıdır. Ardından LinkProfilde gibi bir bio link aracıyla profilinizi oluşturup bağlantılarınızı ekleyebilirsiniz.</p><p>Profil tamamlandıktan sonra oluşan adresi Instagram profilinizdeki bağlantı alanına ekleyin. Kampanya dönemlerinde buton sıralamasını değiştirmeniz, yeni içerikleri yukarı taşımanız ve kullanılmayan bağlantıları kaldırmanız dönüşümü artırabilir.</p><h2>Bio link performansı nasıl ölçülür?</h2><p>Sadece ziyaret sayısına bakmak yeterli değildir. Hangi butonun daha çok tıklandığı, sosyal bağlantılara geçiş, QR taraması, vCard kaydı ve paylaşım gibi etkileşimleri birlikte değerlendirmek daha anlamlıdır. LinkProfilde paneli bu etkileşimleri tek yerde takip etmeyi kolaylaştırır.</p><h2>Sık yapılan hatalar</h2><ul><li>Çok fazla buton ekleyerek karar vermeyi zorlaştırmak</li><li>Eski kampanya bağlantılarını kaldırmamak</li><li>Buton isimlerinde ne olacağını açık yazmamak</li><li>Mobil önizlemeyi kontrol etmeden yayına almak</li><li>Profilde güven veren isim, açıklama ve görsel kullanmamak</li></ul><h2>Sonuç</h2><p>Instagram bio link oluştururken amaç daha fazla bağlantı göstermek değil, ziyaretçiyi doğru aksiyona daha hızlı ulaştırmaktır. Sade bir yapı, net buton isimleri ve ölçülebilir etkileşimler iyi bir bio link sayfasının temelidir.</p>',
            ],
            [
                'title' => 'Ücretsiz Bio Link Oluşturma: Kişisel ve Kurumsal Profil Rehberi',
                'slug' => 'ucretsiz-bio-link-olusturma',
                'focus_keyword' => 'ücretsiz bio link oluşturma',
                'excerpt' => 'Ücretsiz bio link sayfası oluştururken profil düzeni, bağlantı sırası, sosyal medya ikonları ve ölçüm tarafında nelere dikkat etmelisiniz?',
                'meta_title' => 'Ücretsiz Bio Link Oluşturma Rehberi | LinkProfilde',
                'meta_description' => 'Ücretsiz bio link oluşturun; web sitesi, WhatsApp, sosyal medya ve portfolyo bağlantılarınızı tek profil altında toplayın.',
                'content' => '<p>Bio link sayfası, farklı platformlardaki bağlantılarınızı tek bir adres altında toplamanın en pratik yollarından biridir. Özellikle Instagram, TikTok, YouTube ve LinkedIn gibi kanalları aynı anda kullanan kişiler ve işletmeler için tek bir profil adresi yönetimi kolaylaştırır.</p><h2>Ücretsiz bio link kimler için uygundur?</h2><p>İçerik üreticileri, freelancerlar, danışmanlar, küçük işletmeler, mağazalar, etkinlik ekipleri ve portfolyosunu tek sayfada göstermek isteyen herkes başlangıçta ücretsiz bir bio link kullanabilir.</p><h2>Profil yapısını nasıl planlamalısınız?</h2><p>Sayfanın en üst bölümünde kim olduğunuz ve ne sunduğunuz anlaşılmalıdır. İsim, kısa açıklama ve profil görselinden sonra en değerli bağlantılar gelmelidir. Bir ziyaretçi sayfaya girdiğinde 5-10 saniye içinde ne yapacağını anlayabiliyorsa yapı doğru çalışıyor demektir.</p><h2>Önerilen bağlantı sırası</h2><ol><li>Birincil aksiyon: satın al, randevu al, teklif iste veya portfolyoyu gör</li><li>İkinci önemli içerik veya kampanya</li><li>WhatsApp ya da iletişim</li><li>Sosyal medya hesapları</li><li>Destekleyici içerikler</li></ol><h2>Tasarımda nelere dikkat edilmeli?</h2><p>Arka plan, buton rengi ve yazı rengi arasında yeterli kontrast bulunmalıdır. Butonlar mobilde kolay tıklanmalı, profil fotoğrafı sıkıştırılmamalı ve uzun açıklamalardan kaçınılmalıdır. Tasarım kişisel markanızı desteklemeli ancak içeriğin önüne geçmemelidir.</p><h2>Ücretsiz profilden ne beklemelisiniz?</h2><p>Temel seviyede profil oluşturma, bağlantı ekleme, sosyal hesapları gösterme ve mobil uyum yeterlidir. Daha ileri kullanımda özel tema, daha detaylı analitik, QR kod yönetimi veya farklı markalama seçenekleri önem kazanabilir.</p><p>LinkProfilde ile ücretsiz başlayıp bio link profilinizi oluşturabilir, bağlantılarınızı tek panelde düzenleyebilirsiniz. Kullanım büyüdükçe analitik ve QR gibi araçları aynı profil yapısına bağlamak da mümkündür.</p>',
            ],
            [
                'title' => 'Linktree Alternatifi Arayanlar İçin Bio Link Seçim Rehberi',
                'slug' => 'linktree-alternatifi-bio-link',
                'focus_keyword' => 'linktree alternatifi',
                'excerpt' => 'Linktree alternatifi seçerken yalnızca tasarıma değil; QR, analitik, dijital kartvizit, kısa link ve yerel kullanım ihtiyaçlarına da bakın.',
                'meta_title' => 'Linktree Alternatifi: Bio Link Seçim Rehberi | LinkProfilde',
                'meta_description' => 'Linktree alternatifi arayanlar için bio link seçim rehberi. Tema, analitik, QR, kısa link ve dijital kartvizit özelliklerini karşılaştırın.',
                'content' => '<p>Bir bio link aracı seçerken sadece kaç tema sunduğuna bakmak yeterli değildir. Asıl soru, tek bağlantı sayfasının günlük iş akışınızda hangi problemleri çözdüğüdür. Linktree benzeri araçlara alternatif ararken profil yönetimi, analitik, QR ve iletişim özelliklerini birlikte değerlendirmek daha doğru sonuç verir.</p><h2>Bir bio link aracında hangi özellikler aranmalı?</h2><ul><li>Mobil uyumlu ve hızlı profil sayfası</li><li>Sosyal medya ve iletişim bağlantıları</li><li>Kolay bağlantı sıralama</li><li>Profil görüntüleme ve tıklama analitiği</li><li>QR kod oluşturma</li><li>Dijital kartvizit veya vCard desteği</li><li>Kısa link yönetimi</li><li>Markaya uygun tema ve görünüm seçenekleri</li></ul><h2>Sadece bio link yeterli mi?</h2><p>Bazı kullanıcılar için evet. Ancak işletmeler ve profesyoneller çoğu zaman aynı bağlantıyı kartvizitte, mağazada, fuarda, sosyal medyada ve reklam kampanyasında kullanır. Bu durumda QR kod, kısa link ve analitik tek ürün altında olduğunda takip kolaylaşır.</p><h2>Yerel kullanım neden önemli olabilir?</h2><p>Türkçe arayüz, yerel ödeme akışları, destek dili ve Türkiye’deki kullanım alışkanlıklarına uygun ürün yapısı bazı ekipler için avantaj sağlar. Özellikle müşterilere teslim edilen dijital kartvizitlerde karmaşık İngilizce paneller yerine daha anlaşılır bir deneyim tercih edilebilir.</p><h2>LinkProfilde yaklaşımı</h2><p>LinkProfilde yalnızca bio link sayfası oluşturmayı değil, aynı profil etrafında kısa link, QR kod ve etkileşim ölçümünü bir araya getirmeyi hedefler. Böylece kullanıcı tek bir profil adresi üzerinden hem dijital hem fiziksel temas noktalarını yönetebilir.</p><h2>Karar verirken kontrol listesi</h2><p>Seçeceğiniz aracın profilinizi hızlı açtığından, bağlantıların mobilde rahat kullanıldığından, URL yapısının paylaşılabilir olduğundan ve temel analitiklerin anlaşılır sunulduğundan emin olun. Gereksiz özellik yerine gerçekten kullandığınız akışlara odaklanın.</p>',
            ],
            [
                'title' => 'Ücretsiz QR Kod Oluşturma: Statik ve Dinamik QR Kod Farkı',
                'slug' => 'ucretsiz-qr-kod-olusturma',
                'focus_keyword' => 'ücretsiz qr kod oluşturma',
                'excerpt' => 'QR kod oluştururken statik ve dinamik QR farkını, kullanım alanlarını, yönlendirme mantığını ve tarama takibinin neden önemli olduğunu öğrenin.',
                'meta_title' => 'Ücretsiz QR Kod Oluşturma ve Dinamik QR Rehberi | LinkProfilde',
                'meta_description' => 'Ücretsiz QR kod oluşturma rehberi: statik ve dinamik QR farkı, link değiştirme, tarama analitiği ve doğru kullanım alanları.',
                'content' => '<p>QR kod; bir web sayfasına, profile, menüye, kampanyaya veya iletişim bilgisine hızlı erişim sağlayan pratik bir köprü görevi görür. Ancak tüm QR kodlar aynı değildir. Statik ve dinamik QR kod arasındaki farkı bilmek, özellikle baskılı materyallerde sonradan sorun yaşamamanızı sağlar.</p><h2>Statik QR kod nedir?</h2><p>Statik QR kodda hedef bilgi doğrudan QR içine yazılır. Kod basıldıktan sonra hedefi değiştirmek mümkün değildir. Basit ve değişmeyecek bağlantılar için yeterli olabilir.</p><h2>Dinamik QR kod nedir?</h2><p>Dinamik QR kod önce yönetilebilir bir yönlendirme adresine gider, ardından gerçek hedefe aktarılır. Böylece kartvizit, afiş veya masa üstü materyali yeniden basılmadan hedef URL değiştirilebilir. Ayrıca tarama sayısı gibi metrikler takip edilebilir.</p><h2>Hangi kullanım alanlarında dinamik QR tercih edilmeli?</h2><ul><li>Kartvizit ve dijital kartvizit</li><li>Restoran, kafe ve mağaza materyalleri</li><li>Fuar ve etkinlik standları</li><li>Kampanya afişleri</li><li>Ürün ambalajları</li><li>Uzun süre kullanılacak basılı materyaller</li></ul><h2>QR kod tasarımında dikkat edilmesi gerekenler</h2><p>Kontrast düşük olmamalı, kodun etrafında yeterli boşluk bırakılmalı ve gereğinden küçük basılmamalıdır. Logo eklendiğinde QR kodun okunabilirliği gerçek telefonlarla test edilmelidir.</p><h2>Tarama analitiği neden önemli?</h2><p>Bir QR kodu fiziksel dünyada nerede kullandığınızı biliyorsanız, taramaları ölçmek hangi materyalin gerçekten trafik ürettiğini anlamanıza yardımcı olur. LinkProfilde dinamik QR akışını profil ve kısa linklerle ilişkilendirerek bu ölçümü tek panelde toplamayı mümkün kılar.</p>',
            ],
            [
                'title' => 'Dinamik QR Kod Nedir? Sonradan Link Değiştirme ve Takip',
                'slug' => 'dinamik-qr-kod-nedir',
                'focus_keyword' => 'dinamik qr kod',
                'excerpt' => 'Dinamik QR kodun nasıl çalıştığını, sonradan hedef link değiştirme avantajını ve tarama analitiğinin işletmeler için nasıl kullanılabileceğini açıklıyoruz.',
                'meta_title' => 'Dinamik QR Kod Nedir? Link Değiştirme ve Analitik | LinkProfilde',
                'meta_description' => 'Dinamik QR kod nedir? Baskıyı değiştirmeden hedef linki güncelleyin, tarama sayılarını ölçün ve kampanyaları daha esnek yönetin.',
                'content' => '<p>Dinamik QR kod, QR görselini değiştirmeden arka plandaki hedef adresi yönetebilmenizi sağlayan QR türüdür. Bu özellik basılı materyallerde önemli bir avantaj sunar: kartviziti, afişi veya broşürü yeniden bastırmadan yönlendirmeyi değiştirebilirsiniz.</p><h2>Dinamik QR nasıl çalışır?</h2><p>QR kodun içinde nihai hedef yerine sabit bir yönlendirme adresi bulunur. Kullanıcı kodu taradığında bu adres hedef sayfaya yönlendirir. Siz yönetim panelinden hedefi değiştirseniz bile QR görseli aynı kalır.</p><h2>Neden statik QR yerine dinamik QR kullanılabilir?</h2><ul><li>Hedef URL sonradan değiştirilebilir</li><li>Baskı maliyeti azaltılır</li><li>Tarama performansı izlenebilir</li><li>Farklı kampanya dönemlerinde aynı fiziksel materyal tekrar kullanılabilir</li></ul><h2>Örnek senaryo</h2><p>Bir fuar standında kullandığınız QR kod önce ürün kataloğuna yönlenebilir. Fuar bittikten sonra aynı QR hedefini teklif formuna veya genel dijital profile çevirebilirsiniz. Kodun kendisi değişmediği için basılı stand malzemesi yeniden hazırlanmaz.</p><h2>Analitik ne sağlar?</h2><p>Tarama sayısı tek başına satış anlamına gelmez ancak fiziksel materyalin dijital trafik üretip üretmediğini gösterir. QR taramasını profil ziyaretleri ve link tıklamalarıyla birlikte değerlendirmek daha anlamlı bir tablo sunar.</p><p>LinkProfilde içinde oluşturulan dinamik QR kodlar, bio link veya kısa link hedefleriyle birlikte yönetilebilir ve hedef değişiklikleri tek panelden yapılabilir.</p>',
            ],
            [
                'title' => 'Dijital Kartvizit Nasıl Oluşturulur? QR ve vCard ile Temassız Paylaşım',
                'slug' => 'dijital-kartvizit-nasil-olusturulur',
                'focus_keyword' => 'dijital kartvizit oluşturma',
                'excerpt' => 'Dijital kartvizit hazırlarken profil, iletişim bağlantıları, QR kod, vCard ve NFC kullanımını tek bir akışta nasıl birleştirebilirsiniz?',
                'meta_title' => 'Dijital Kartvizit Nasıl Oluşturulur? QR + vCard | LinkProfilde',
                'meta_description' => 'Dijital kartvizit oluşturma rehberi: profil bilgileri, QR kod, vCard, NFC ve sosyal bağlantıları tek sayfada birleştirin.',
                'content' => '<p>Dijital kartvizit, klasik kartvizitin iletişim bilgilerini tek bir web profiline taşıyan modern bir paylaşım yöntemidir. Telefon numarası, e-posta, sosyal medya, web sitesi ve portfolyo bağlantıları tek sayfada tutulabilir.</p><h2>Dijital kartvizitte hangi bilgiler olmalı?</h2><ul><li>Ad soyad veya işletme adı</li><li>Unvan veya kısa tanım</li><li>Telefon ve WhatsApp</li><li>E-posta</li><li>Web sitesi ve sosyal medya hesapları</li><li>Portfolyo, hizmet veya randevu bağlantıları</li></ul><h2>QR kod ile paylaşım</h2><p>Dijital kartvizit profiliniz için oluşturulan QR kodu basılı kartvizit, masa üstü aparat, broşür, roll-up veya fuar standında kullanabilirsiniz. Dinamik QR tercih edilirse profil hedefi ileride daha esnek yönetilebilir.</p><h2>vCard nedir?</h2><p>vCard, iletişim bilgilerinin telefon rehberine daha hızlı kaydedilmesini sağlayan standart formattır. Kullanıcı tek tek telefon ve e-posta kopyalamak yerine “Rehbere Ekle” aksiyonuyla bilgileri cihazına aktarabilir.</p><h2>NFC ile birlikte kullanılabilir mi?</h2><p>Evet. NFC kart veya etikete dijital profil URL’si yazıldığında, telefon karta yaklaştırıldığında profil açılabilir. Böylece aynı profil hem QR hem NFC hem de normal URL ile paylaşılır.</p><h2>İşletmeler için avantajı</h2><p>Personel değişikliği, telefon güncellemesi veya sosyal hesap değişikliği olduğunda baskı yenilemek yerine dijital profili güncellemek yeterlidir. LinkProfilde bu profilin yanında QR, vCard ve bağlantı analitiğini de tek panelde bir araya getirir.</p>',
            ],
            [
                'title' => 'NFC Kartvizit Nedir? Dijital Profil ile Nasıl Kullanılır?',
                'slug' => 'nfc-kartvizit-nedir',
                'focus_keyword' => 'nfc kartvizit',
                'excerpt' => 'NFC kartvizitlerin çalışma mantığı, hangi telefonlarla kullanılabildiği ve dijital profil bağlantısıyla nasıl daha esnek hale geldiği hakkında temel rehber.',
                'meta_title' => 'NFC Kartvizit Nedir? Dijital Profil Kullanımı | LinkProfilde',
                'meta_description' => 'NFC kartvizit nedir, nasıl çalışır ve dijital profil ile nasıl kullanılır? QR kod ve vCard ile birlikte doğru kullanım akışını öğrenin.',
                'content' => '<p>NFC kartvizit, fiziksel kartın içine yerleştirilen NFC etiketi sayesinde telefonla temassız olarak dijital bir bağlantı açar. Kullanıcı kartı telefonun NFC alanına yaklaştırdığında tarayıcı üzerinden dijital profil açılır.</p><h2>NFC kartın içine ne yazılmalı?</h2><p>En esnek yöntem doğrudan telefon veya tek bir sosyal medya hesabı yerine yönetilebilir dijital profil URL’si kullanmaktır. Böylece daha sonra telefon, e-posta veya sosyal hesap değişse bile NFC kartın içeriğini yeniden yazmanız gerekmez; profil içeriğini güncellemeniz yeterli olur.</p><h2>NFC ve QR birlikte neden kullanılır?</h2><p>Her cihazda NFC açık olmayabilir veya kullanıcı NFC ile nasıl işlem yapacağını bilmeyebilir. Kart üzerinde aynı profile giden QR kod bulunması ikinci bir erişim yolu sağlar.</p><h2>Profesyonel kullanım önerileri</h2><ul><li>Profil URL’sini kısa ve okunabilir tutun</li><li>QR kodu da aynı profile bağlayın</li><li>“Rehbere Ekle” aksiyonu ekleyin</li><li>En önemli iletişim butonunu üstte gösterin</li><li>Profil ziyaretlerini ve tıklamaları ölçün</li></ul><p>LinkProfilde dijital profil adresi, NFC kartın hedefi olarak kullanılabilir. Aynı profil üzerinde sosyal bağlantılar, vCard, QR ve etkileşim verileri yönetilebilir.</p>',
            ],
            [
                'title' => 'Kısa Link Nasıl Oluşturulur? URL Kısaltma ve Takip Rehberi',
                'slug' => 'kisa-link-nasil-olusturulur',
                'focus_keyword' => 'kısa link oluşturma',
                'excerpt' => 'Uzun URL’leri daha düzenli paylaşmak için kısa link nasıl oluşturulur, hangi durumlarda kullanılır ve tıklama verileri nasıl değerlendirilir?',
                'meta_title' => 'Kısa Link Nasıl Oluşturulur? URL Kısaltma | LinkProfilde',
                'meta_description' => 'Kısa link oluşturma ve URL kısaltma rehberi. Uzun bağlantıları sadeleştirin, kampanyaları düzenleyin ve tıklamaları takip edin.',
                'content' => '<p>Kısa link, uzun ve karmaşık bir URL’yi daha kolay paylaşılabilir bir adrese dönüştürür. Özellikle sosyal medya, SMS, baskılı materyal, kampanya ve sunumlarda temiz görünüm sağlar.</p><h2>Kısa link ne zaman kullanılır?</h2><ul><li>Uzun kampanya URL’lerini paylaşırken</li><li>UTM parametreli reklam bağlantılarında</li><li>SMS ve mesajlaşma kanallarında</li><li>Sunum ve basılı materyallerde</li><li>Farklı kampanyaların tıklamalarını ayrı takip etmek için</li></ul><h2>İyi bir kısa link nasıl olmalı?</h2><p>Mümkünse kolay okunmalı ve hedefi çağrıştırmalıdır. Rastgele karakter dizileri teknik olarak çalışsa da kullanıcı güveni açısından anlamlı takma adlar daha iyi olabilir. Kısa linkin HTTPS kullanması ve güvenilir bir alan adı altında açılması da önemlidir.</p><h2>Tıklama takibi neden faydalıdır?</h2><p>Aynı hedef sayfaya farklı kanallardan trafik gönderiyorsanız her kanal için ayrı kısa link kullanarak hangi kaynağın daha fazla tıklama getirdiğini görebilirsiniz. Ülke, cihaz, tarayıcı ve yönlendiren kaynak gibi veriler kampanya optimizasyonunda yardımcı olabilir.</p><h2>Kısa link ve QR birlikte kullanılabilir mi?</h2><p>Evet. Kısa linki bir QR kodun hedefi olarak kullanmak fiziksel materyalden gelen trafiği de ayrı izlemeyi kolaylaştırır. LinkProfilde kısa link, dinamik QR ve bio link yapılarını aynı panelde yönetmeye izin verir.</p>',
            ],
            [
                'title' => 'Sosyal Medya Linklerini Tek Linkte Toplama Rehberi',
                'slug' => 'sosyal-medya-linklerini-tek-linkte-toplama',
                'focus_keyword' => 'sosyal medya linklerini tek linkte toplama',
                'excerpt' => 'Instagram, TikTok, YouTube, LinkedIn, WhatsApp ve web sitesi bağlantılarını tek link altında toplarken doğru sıralama ve tasarım nasıl olmalı?',
                'meta_title' => 'Sosyal Medya Linklerini Tek Linkte Toplama | LinkProfilde',
                'meta_description' => 'Instagram, TikTok, YouTube, LinkedIn, WhatsApp ve web sitesi linklerini tek bir bio link profilinde toplayın ve düzenli yönetin.',
                'content' => '<p>Birden fazla sosyal medya hesabı kullandığınızda her platformda farklı bağlantı paylaşmak zorlaşabilir. Tüm sosyal medya linklerini tek bir profil adresinde toplamak hem yönetimi kolaylaştırır hem de ziyaretçiye seçim özgürlüğü verir.</p><h2>Hangi bağlantılar eklenebilir?</h2><p>Instagram, TikTok, YouTube, LinkedIn, X, Facebook, WhatsApp, web sitesi, e-ticaret mağazası, portfolyo, rezervasyon ve iletişim formları aynı profil içinde gösterilebilir.</p><h2>Bağlantı sırası nasıl belirlenir?</h2><p>En çok değer üreten bağlantıyı en üstte tutun. Örneğin içerik üreticisiyseniz son video veya kanal, işletmeyseniz WhatsApp ya da randevu, freelancer iseniz portfolyo veya teklif formu ilk sırada olabilir.</p><h2>Sosyal ikon mu, büyük buton mu?</h2><p>Temel sosyal medya hesapları küçük ikonlarla gösterilebilir. Ancak kullanıcıdan özellikle bir aksiyon beklediğiniz bağlantılar daha büyük ve açıklayıcı butonlar halinde sunulmalıdır. “Buraya tıkla” yerine “Portfolyoyu Gör” veya “WhatsApp’tan Teklif Al” gibi metinler daha anlaşılırdır.</p><h2>Tek linkin avantajı</h2><p>Profil adresini Instagram bio, TikTok, e-posta imzası, QR kod, NFC kart ve basılı kartvizitte aynı şekilde kullanabilirsiniz. İçerideki bağlantılar değişse bile ana adres sabit kalır.</p><p>LinkProfilde bu yaklaşımı bio link, QR, vCard ve etkileşim analitiğiyle birleştirerek tek bağlantının farklı kanallarda kullanılmasını kolaylaştırır.</p>',
            ],
            [
                'title' => 'İşletmeler İçin QR Kod ve Bio Link Kullanım Fikirleri',
                'slug' => 'isletmeler-icin-qr-kod-bio-link',
                'focus_keyword' => 'işletmeler için qr kod',
                'excerpt' => 'Mağaza, kafe, hizmet işletmesi, fuar ve etkinliklerde QR kod ile bio link profilini birlikte kullanabileceğiniz pratik senaryolar.',
                'meta_title' => 'İşletmeler İçin QR Kod ve Bio Link Fikirleri | LinkProfilde',
                'meta_description' => 'İşletmeler için QR kod kullanım fikirleri: iletişim, menü, kampanya, sosyal medya, yorum, katalog ve dijital kartvizit senaryoları.',
                'content' => '<p>QR kod ve bio link birlikte kullanıldığında işletmenin fiziksel temas noktaları dijital profile bağlanabilir. Böylece müşteri tek bir taramayla iletişim, sosyal medya, kampanya veya ürün bağlantılarına ulaşabilir.</p><h2>Mağaza ve hizmet işletmeleri</h2><p>Kasa önü, vitrin veya kartvizitte kullanılan QR kodu doğrudan tek bir kampanya sayfasına bağlamak yerine işletmenin dijital profiline yönlendirebilirsiniz. Profil içinde WhatsApp, yol tarifi, sosyal medya, randevu ve güncel kampanya butonları yer alabilir.</p><h2>Fuar ve etkinlikler</h2><p>Stand üzerindeki QR kod ürün kataloğu, iletişim formu, LinkedIn, WhatsApp ve şirket sitesini tek sayfada sunabilir. Etkinlik sonrasında aynı profil güncellenerek yeni kampanyaya uyarlanabilir.</p><h2>Katalog ve ürün ambalajı</h2><p>Dinamik QR kod kullanıldığında ürün ambalajındaki kod değişmeden hedef içerik güncellenebilir. Kullanım kılavuzu, video, kampanya veya destek sayfası zaman içinde değiştirilebilir.</p><h2>Ölçüm tarafı</h2><p>QR tarama sayısı ile profil üzerindeki tıklamaları birlikte takip etmek, sadece “kaç kişi taradı?” sorusundan daha değerli bilgi verir. Kullanıcıların taramadan sonra WhatsApp’a mı, web sitesine mi yoksa sosyal hesaba mı geçtiği gözlemlenebilir.</p><p>LinkProfilde işletmelere bio link profilini, kısa linkleri ve dinamik QR kodları aynı panelde yönetme imkânı verir.</p>',
            ],
        ];

        foreach ($posts as $index => $post) {
            DB::table('blog_posts')->updateOrInsert(
                ['slug' => $post['slug']],
                array_merge($post, [
                    'author_name' => 'LinkProfilde Editör Ekibi',
                    'is_published' => true,
                    'published_at' => $now->copy()->subDays(count($posts) - $index - 1),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('blog_posts')->whereIn('slug', [
            'instagram-bio-link-nasil-olusturulur',
            'ucretsiz-bio-link-olusturma',
            'linktree-alternatifi-bio-link',
            'ucretsiz-qr-kod-olusturma',
            'dinamik-qr-kod-nedir',
            'dijital-kartvizit-nasil-olusturulur',
            'nfc-kartvizit-nedir',
            'kisa-link-nasil-olusturulur',
            'sosyal-medya-linklerini-tek-linkte-toplama',
            'isletmeler-icin-qr-kod-bio-link',
        ])->delete();
    }
};
