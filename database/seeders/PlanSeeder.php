<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PricingPlan::create([
            'name' => 'BASIC',
            'description' => 'Temel kullanım için ücretsiz plan',
            'monthly_price' => 0,
            'yearly_price' => 0,
            'currency' => 'USD',
            'status' => "active",
            'biolinks' => '5',
            'biolink_blocks' => 4,
            'shortlinks' => '10',
            'projects' => '10',
            'qrcodes' => '10',
            'themes' => 'Free',
            'custom_theme' => false,
            'support' => 72,
        ]);
        PricingPlan::create([
            'name' => 'STANDARD',
            'description' => 'Standard plan for standard use',
            'monthly_price' => 50,
            'yearly_price' => 500,
            'currency' => 'TL',
            'status' => "active",
            'biolinks' => '100',
            'biolink_blocks' => 7,
            'shortlinks' => '100',
            'projects' => '100',
            'qrcodes' => '100',
            'themes' => 'Standard',
            'custom_theme' => true,
            'support' => 48,
        ]);
        PricingPlan::create([
            'name' => 'PREMIUM',
            'description' => 'İş amaçlı kullanıma yönelik premium plan',
            'monthly_price' => 150,
            'yearly_price' => 1500,
            'currency' => 'TL',
            'status' => "active",
            'biolinks' => 'Sınırsız',
            'biolink_blocks' => 9,
            'shortlinks' => 'Sınırsız',
            'projects' => 'Sınırsız',
            'qrcodes' => 'Sınırsız',
            'themes' => 'Premium',
            'custom_theme' => true,
            'support' => 24,
        ]);
    }
}
