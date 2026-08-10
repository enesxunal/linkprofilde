# Proje klasöründe çalıştırın.
# 1) Önce frontend derlenir (npm run build)
# 2) Sonra PHP sunucusu başlar (artisan serve)

$phpPath = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
$projectRoot = $PSScriptRoot

Write-Host "Frontend derleniyor (npm run build)..." -ForegroundColor Yellow
Set-Location $projectRoot
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "Build basarisiz. npm run build hatasi." -ForegroundColor Red
    exit 1
}
Write-Host "Build tamamlandi. Sunucu baslatiliyor..." -ForegroundColor Green
& $phpPath artisan serve
