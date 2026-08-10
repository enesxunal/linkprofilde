<?php

return [
    [
        'title' => 'Kullanıcı Paneli',
        'role' => 'USER',
        'pages' => [
            [
                'icon' => 'Dashboard',
                'name' => 'Kontrol Paneli',
                'path' => '/dashboard',
            ],
            [
                'icon' => 'BioLink',
                'name' => 'Profiller',
                'path' => '/bio-links',
            ],
            [
                'icon' => 'ShortLink',
                'name' => 'Kısa Linkler',
                'path' => '/short-links',
            ],
            [
                'icon' => 'Projects',
                'name' => 'Projeler',
                'path' => '/projects',
            ],
            [
                'icon' => 'QRcode',
                'name' => 'QR Kodlar',
                'path' => '/qrcodes',
            ],
            [
                'icon' => 'Pricing',
                'name' => 'Fiyatlandırma',
                'path' => '/current-plan',
            ],
            [
                'icon' => 'Setting',
                'name' => 'Ayarlar',
                'path' => '/settings',
            ],
            [
                'icon' => 'LogOut',
                'name' => 'Çıkış yap',
                'path' => '/logout',
            ],
        ],
    ],
    [
        'title' => 'Yönetim Paneli',
        'role' => 'SUPER-ADMIN',
        'pages' => [
            [
                'icon' => 'Users',
                'name' => 'Kullanıcılar',
                'path' => '/admin/users',
            ],
            [
                'icon' => 'IdCard',
                'name' => 'Abonelikler',
                'path' => '/admin/subscriptions',
            ],
            [
                'icon' => 'Calendar',
                'name' => 'Fiyatlandırma Planları',
                'path' => '/admin/pricing-plans',
            ],
            [
                'icon' => 'Chat',
                'name' => 'Müşteri Yorumları',
                'path' => '/admin/testimonials',
            ],
            [
                'icon' => 'Palette',
                'name' => 'Tema Yönetimi',
                'path' => '/admin/manage-themes',
            ],
            [
                'icon' => 'PaymentSettings',
                'name' => 'Ödeme Ayarları',
                'path' => '/admin/payments-setup',
            ],
            [
                'icon' => 'Page',
                'name' => 'Özel Sayfa',
                'path' => '/admin/custom-page',
            ],
            [
                'icon' => 'Setting',
                'name' => 'Uygulama Ayarları',
                'path' => '/admin/app-settings',
            ],
        ],
    ],
];
