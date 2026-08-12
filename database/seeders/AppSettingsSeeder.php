<?php

namespace Database\Seeders;

use App\Models\AppSetting;
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
        if (AppSetting::query()->exists()) {
            return;
        }

        $setting = new AppSetting();
        $setting->forceFill([
            'title' => 'LinkProfilde',
            'logo' => 'assets/icons/link-drop.png',
            'copyright' => '3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi. Tüm hakları saklıdır.',
            'description' => 'LinkProfilde; kişisel dijital profil, kısa link, QR kod ve analytics özelliklerini tek panelde birleştirir.',
        ])->save();
    }
}
