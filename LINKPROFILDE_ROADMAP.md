# LinkProfilde Uygulama Yol Haritası

## P0 - Kritik güvenlik ve üretim temizliği
- [x] Çalışma branch'i: `linkprofilde-roadmap-p0`
- [x] `.env.bak`, `build.zip` ve zip çıktılarının Git'e yanlışlıkla eklenmesini engelle
- [x] Kullanıcıya dönen controller exception mesajlarını production'da güvenli genel mesaja çevir, gerçek hatayı logla
- [x] Frontend production build doğrulaması
- [x] Bio-link / block / short-link / project / QR / analytics temel sahiplik-IDOR kontrolleri
- [x] Bio-link blok sıralamasındaki çapraz kullanıcı IDOR açığını kapat
- [x] Auth rate limit: login, register, password reset, Google auth ve hassas hesap işlemleri
- [x] Upload doğrulama: JPEG/PNG, MIME-uzantı eşleşmesi, çözünürlük limiti, güvenli dosya adı ve güvenli silme
- [x] Tosla callback/idempotency kod kontrolü: PaymentAttempt, transaction kilidi ve tekrar abonelik engeli mevcut
- [x] CustomPage raw exception sızıntılarını kapat; route uniqueness ve içerik boyutu doğrulaması ekle
- [x] Eski ödeme gateway controller'larındaki raw exception mesajlarını güvenli hata katmanına al
- [x] Eski vendor uzaktan ZIP/upgrade.php güncelleme motorunu VersionController'dan kaldır
- [x] JavaScript dependency audit: production ve dev dahil `npm audit` sonucu 0 açık
- [x] Vite toolchain'i Vite 8 / React plugin 6 / Laravel Vite plugin 3 hattına yükselt
- [x] Docker PHP ortamında backend feature/security testlerini çalıştır — 184 test / 854 assertion geçti

## P1 - Kullanıcı deneyimi
- [x] Profil listesine mobil kart görünümü ekle; mobilde 1000px tablo zorunluluğunu kaldır
- [x] Public profil için dinamik title, description, canonical, OpenGraph ve Twitter meta bilgileri
- [x] Public profil fotoğrafındaki eski `linkdrop` alt metnini kaldır
- [x] Public profil sosyal JSON verisini bozuk veri durumuna dayanıklı hale getir
- [x] Kayıt sonrası ilk profil onboarding akışını e-posta doğrulama sonrasına bağla
- [x] Profil editörünü Profil / Bağlantılar / Görünüm / Paylaşım bilgi mimarisine geçir
- [x] Onboarding ilerleme kartı ve profil tamamlama kontrol listesi ekle
- [x] Public profil görünümünü mobile-first modern yapıya geçir; paylaşım ve vCard aksiyonlarını iyileştir
- [x] Blok linklerinin mobil tıklama alanlarını ve görsel hiyerarşisini iyileştir
- [x] Ortak liste aramalarına loading ve hata durumu ekle
- [x] Dashboard boş profil durumuna ilk aksiyon CTA'sı ekle
- [x] Blok sıralamadaki yanlış DOM pozisyon hesabını düzelt
- [x] Mobil blok sıralaması için yukarı/aşağı kontrolleri ekle
- [x] Blok sırası kaydedilirken durum ve hata geri bildirimi ekle
- [x] Dashboard abonelik uyarısı ve auth/reset-password akışındaki görünür İngilizce kalıntıları Türkçeleştir
- [x] Profil editörü canlı önizlemesindeki template/alt metin kalıntılarını temizle
- [x] Özel sayfa editörünü hafif semantic HTML editörüne geçir
- [ ] Empty/loading/error/success durumlarını kalan panel ekranlarında standartlaştır
- [ ] MaterialLite Dialog/Menu/Tabs davranışlarını gerçek tarayıcıda smoke-test et

## P2 - Ürün, performans ve büyüme
- [x] Landing ana mesajını dijital profil + QR + analitik ürün yapısına yaklaştır
- [x] Landing canonical / OpenGraph / Twitter / SoftwareApplication schema ekle
- [x] Fiyatlandırmayı kullanıcı tarafında Ücretsiz / Pro / Business olarak sadeleştir
- [x] QR özelliğini profil editörüne profil kimliğiyle bağlı oluşturma akışı olarak göm
- [x] Profil editöründen QR ekranına geçildiğinde profil hedefini otomatik seç
- [x] Dijital kartvizit/vCard aksiyonunu public profile entegre et
- [x] Event bazlı analitik: profil link tıklaması, sosyal tıklama, vCard ve paylaşım
- [x] Analitik ekranında etkileşim toplamı, oranı, link/sosyal/vCard/paylaşım metrikleri
- [x] QR tarama metriğini profile bağlı QR kodlar üzerinden profil analitiğine ekle
- [x] Dashboard'a son 30 gün etkileşim, link/sosyal tıklama, QR, paylaşım ve vCard metrikleri ekle
- [x] Public profil schema.org Person
- [x] Global Material Tailwind ThemeProvider bağımlılığını kaldır
- [x] Public profil LinkBlock accordion'unu native React/Tailwind yapısına geçir
- [x] Dashboard navbar Material Tailwind bağımlılığını kaldır
- [x] Material Tailwind paketini tamamen kaldır; 40 importu 5 KB'lık MaterialLite katmanına geçir
- [x] Ana JS bundle'ı yaklaşık 932 KB'den ~290 KB seviyesine indir
- [x] Dashboard layout chunk'ını yaklaşık 675 KB'den ~13 KB seviyesine indir
- [x] ApexCharts'i kaldırıp native SVG grafiklere geç; ~515 KB chart chunk'ını ~1.8 KB seviyesine indir
- [x] ReactQuill + Quill + KaTeX'i kaldır; ~499 KB KaTeX chunk'ını tamamen kaldır
- [x] Tailwind Material wrapper'ını kaldır; CSS çıktısını yaklaşık 165 KB'den ~72 KB seviyesine indir
- [x] Son production build'i Vite 8 ile yaklaşık 1 saniyede başarıyla tamamla

## SEO / GEO / içerik altyapısı
- [x] Yönetilebilir blog sistemi: admin listeleme, oluşturma, düzenleme, yayın/taslak ve silme
- [x] Public `/blog` ve `/blog/{slug}` sayfaları; mobil uyumlu tasarım ve iç linkleme
- [x] BlogPosting + BreadcrumbList structured data
- [x] Ana sayfaya Organization + WebSite + SoftwareApplication schema graph
- [x] Dinamik `/sitemap.xml`: ana sayfa, blog, özel sayfalar ve public bio profiller
- [x] `/llms.txt`: AI/agentlar için ürün tanımı, temel kullanım alanları ve güncel rehber dizini
- [x] `robots.txt`: public arama/AI botlarına açık; admin/auth/panel alanları kapalı
- [x] Auth sayfalarına `noindex,nofollow`
- [x] Blog ve sistem yollarını kullanıcı profil slug çakışmalarına karşı ayır
- [x] 21 adet yüksek arama niyetli başlangıç rehberi oluştur
- [x] Blog/SEO otomasyon testleri eklendi ve geçti

## Not
Branch oluşturulmadan önce repoda mevcut, commitlenmemiş login/register/home/payment değişiklikleri vardı. Bunlar korunmuştur ve bu çalışmanın parçası olarak geri alınmamıştır.

Laravel/PHP testleri Docker çalışma ortamında doğrulandı. Test komutu konteynerin yerel `APP_ENV` değerini miras alabildiği için test doğrulamasında `APP_ENV=testing`, array cache/session değişkenleri açıkça veriliyor. Son tam doğrulama: 184 test, 854 assertion başarılı.
