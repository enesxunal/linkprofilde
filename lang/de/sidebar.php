<?php

return [
    [
        'title' => 'Benutzer Panel',
        'role' => 'USER',
        'pages' => [
            [
                'icon' => 'Dashboard',
                'name' => 'Dashboard',
                'path' => '/dashboard',
            ],
            [
                'icon' => 'BioLink',
                'name' => 'Biografie-Links',
                'path' => '/bio-links',
            ],
            [
                'icon' => 'ShortLink',
                'name' => 'Kurzlinks',
                'path' => '/short-links',
            ],
            [
                'icon' => 'Projects',
                'name' => 'Projekte',
                'path' => '/projects',
            ],
            [
                'icon' => 'QRcode',
                'name' => 'QR-Codes',
                'path' => '/qrcodes',
            ],
            [
                'icon' => 'Pricing',
                'name' => 'Aktueller Plan',
                'path' => '/current-plan',
            ],
            [
                'icon' => 'Setting',
                'name' => 'Einstellungen',
                'path' => '/settings',
            ],
            [
                'icon' => 'LogOut',
                'name' => 'Abmelden',
                'path' => '/logout',
            ],
        ],
    ],
    [
        'title' => 'Admin-Panel',
        'role' => 'SUPER-ADMIN',
        'pages' => [
            [
                'icon' => 'Users',
                'name' => 'Benutzer',
                'path' => '/admin/users',
            ],
            [
                'icon' => 'IdCard',
                'name' => 'Abonnements',
                'path' => '/admin/subscriptions',
            ],
            [
                'icon' => 'Calendar',
                'name' => 'Preispläne',
                'path' => '/admin/pricing-plans',
            ],
            [
                'icon' => 'Chat',
                'name' => 'Kundenbewertungen',
                'path' => '/admin/testimonials',
            ],
            [
                'icon' => 'Palette',
                'name' => 'Themen verwalten',
                'path' => '/admin/manage-themes',
            ],
            [
                'icon' => 'PaymentSettings',
                'name' => 'Zahlungseinstellungen',
                'path' => '/admin/payments-setup',
            ],
            [
                'icon' => 'Page',
                'name' => 'Benutzerdefinierte',
                'path' => '/admin/custom-page',
            ],
            [
                'icon' => 'Setting',
                'name' => 'App-Einstellungen',
                'path' => '/admin/app-settings',
            ],
        ],
    ],
];
