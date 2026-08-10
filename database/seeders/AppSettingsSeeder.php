<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppSetting::create([
            'title' => 'LinkProfilde',
            'logo' => 'assets/icons/link-drop.png',
            'copyright' => '3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi
            Telif hakları © 2023 LinkBurada. Tüm hakları saklıdır.',
            'description' => 'Link Profilde, takipçilerinizin önemli içeriklerinizi kolayca takip etmeleri için özel bağlantılar
            oluşturmanıza ve QR kodları oluşturup düzenlemenize yardımcı olur.',
        ]);
    }
}
