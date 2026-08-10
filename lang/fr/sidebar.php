<?php

return [
    [
        'title' => 'Panneau utilisateur',
        'role' => 'USER',
        'pages' => [
            [
                'icon' => 'Dashboard',
                'name' => 'Tableau de bord',
                'path' => '/dashboard',
            ],
            [
                'icon' => 'BioLink',
                'name' => 'Liens bio',
                'path' => '/bio-links',
            ],
            [
                'icon' => 'ShortLink',
                'name' => 'Liens courts',
                'path' => '/short-links',
            ],
            [
                'icon' => 'Projects',
                'name' => 'Projets',
                'path' => '/projects',
            ],
            [
                'icon' => 'QRcode',
                'name' => 'Codes QR',
                'path' => '/qrcodes',
            ],
            [
                'icon' => 'Pricing',
                'name' => 'Plan actuel',
                'path' => '/current-plan',
            ],
            [
                'icon' => 'Setting',
                'name' => 'Paramètres',
                'path' => '/settings',
            ],
            [
                'icon' => 'LogOut',
                'name' => 'Se déconnecter',
                'path' => '/logout',
            ],
        ],
    ],
    [
        'title' => "Panneau d'administration",
        'role' => 'SUPER-ADMIN',
        'pages' => [
            [
                'icon' => 'Users',
                'name' => 'Utilisateurs',
                'path' => '/admin/users',
            ],
            [
                'icon' => 'IdCard',
                'name' => 'Abonnements',
                'path' => '/admin/subscriptions',
            ],
            [
                'icon' => 'Calendar',
                'name' => 'Plans de tarification',
                'path' => '/admin/pricing-plans',
            ],
            [
                'icon' => 'Chat',
                'name' => 'Témoignages',
                'path' => '/admin/testimonials',
            ],
            [
                'icon' => 'Palette',
                'name' => 'Gérer les thèmes',
                'path' => '/admin/manage-themes',
            ],
            [
                'icon' => 'PaymentSettings',
                'name' => 'Configuration des paiements',
                'path' => '/admin/payments-setup',
            ],
            [
                'icon' => 'Page',
                'name' => 'Page personnalisée',
                'path' => '/admin/custom-page',
            ],
            [
                'icon' => 'Setting',
                'name' => "Paramètres de l'application",
                'path' => '/admin/app-settings',
            ],
        ],
    ],
];
