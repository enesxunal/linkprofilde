<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tosla ile Ödeme</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f3f4f6; }
        .box { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; text-align: center; }
        h1 { font-size: 1.25rem; color: #1f2937; margin-bottom: 1rem; }
        p { color: #6b7280; margin-bottom: 1.5rem; }
        a { display: inline-block; padding: 0.75rem 1.5rem; background: #3b82f6; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 500; }
        a:hover { background: #2563eb; }
        .cancel { margin-top: 1rem; font-size: 0.875rem; }
        .cancel a { background: transparent; color: #6b7280; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Tosla ile Ödeme</h1>
        <p>Ödeme işlemini tamamlamak için aşağıdaki butona tıklayın. Gerçek entegrasyonda bu sayfa Tosla ödeme sayfasına yönlendirir.</p>
        <a href="{{ route('tosla.success') }}">Ödemeyi Tamamla</a>
        <p class="cancel"><a href="{{ route('tosla.cancel') }}">İptal</a></p>
    </div>
</body>
</html>
