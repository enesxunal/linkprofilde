<?php

use App\Models\Theme;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $themes = [
            ['name' => 'Midnight', 'background' => "background: #0f172a;", 'text_color' => '#f8fafc', 'button_style' => 'background: #334155; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);', 'font_family' => "'Inter', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/dark-carbon.png', 'bg_image' => null, 'type' => 'Free'],
            ['name' => 'Coral', 'background' => "background: #fff5f5;", 'text_color' => '#1d2939', 'button_style' => 'background: #f97316; border-radius: 30px; color: #fff; box-shadow: 0 4px 14px rgba(249,115,22,0.35);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/autumn.png', 'bg_image' => null, 'type' => 'Free'],
            ['name' => 'Mint', 'background' => "background: #f0fdf4;", 'text_color' => '#1d2939', 'button_style' => 'background: #22c55e; border-radius: 30px; box-shadow: 0 4px 14px rgba(34,197,94,0.3);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/leaf.png', 'bg_image' => null, 'type' => 'Free'],
            ['name' => 'Lavender', 'background' => "background: #faf5ff;", 'text_color' => '#1d2939', 'button_style' => 'background: #a78bfa; border-radius: 12px; box-shadow: 0 4px 14px rgba(167,139,250,0.35);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/unicorn.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Ocean', 'background' => "background: #f0f9ff;", 'text_color' => '#1d2939', 'button_style' => 'background: #0ea5e9; border-radius: 12px; box-shadow: 0 4px 14px rgba(14,165,233,0.35);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/clear-sky.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Sunset', 'background' => "background: #fff7ed;", 'text_color' => '#1d2939', 'button_style' => 'background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 30px; box-shadow: 0 4px 14px rgba(249,115,22,0.4);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/autumn.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Forest', 'background' => "background: #f0fdf4;", 'text_color' => '#1d2939', 'button_style' => 'background: #166534; border-radius: 30px; box-shadow: 0 4px 14px rgba(22,101,52,0.35);', 'font_family' => "'Quicksand', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/chameleon.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Rose', 'background' => "background: #fff1f2;", 'text_color' => '#1d2939', 'button_style' => 'background: #e11d48; border-radius: 12px; box-shadow: 0 4px 14px rgba(225,29,72,0.35);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/blush.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Slate', 'background' => "background: #f8fafc;", 'text_color' => '#1d2939', 'button_style' => 'background: #64748b; border-radius: 8px; box-shadow: 0 4px 14px rgba(100,116,139,0.25);', 'font_family' => "'Inter', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/basic.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Amber', 'background' => "background: #fffbeb;", 'text_color' => '#1d2939', 'button_style' => 'background: #d97706; border-radius: 30px; box-shadow: 0 4px 14px rgba(217,119,6,0.35);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/sunny.png', 'bg_image' => null, 'type' => 'Standard'],
            ['name' => 'Navy', 'background' => "background: #0f172a;", 'text_color' => '#f8fafc', 'button_style' => 'background: rgba(248,250,252,0.95); border-radius: 12px; color: #0f172a; box-shadow: 0 4px 14px rgba(0,0,0,0.2);', 'font_family' => "'Inter', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/dark-carbon.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Peach', 'background' => "background: #fff7ed;", 'text_color' => '#1d2939', 'button_style' => 'background: #fdba74; border-radius: 30px; border: 2px solid #ea580c; box-shadow: 0 4px 14px rgba(251,146,60,0.35);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/autumn.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Ice', 'background' => "background: linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 100%);", 'text_color' => '#0c4a6e', 'button_style' => 'background: rgba(255,255,255,0.9); border-radius: 12px; border: 1px solid #bae6fd; box-shadow: 0 4px 14px rgba(14,165,233,0.2);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/clear-sky.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Wine', 'background' => "background: #1e1b4b;", 'text_color' => '#e9d5ff', 'button_style' => 'background: #c4b5fd; border-radius: 12px; color: #1e1b4b; box-shadow: 0 4px 14px rgba(196,181,253,0.4);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/unicorn.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Lime', 'background' => "background: #f7fee7;", 'text_color' => '#1d2939', 'button_style' => 'background: #84cc16; border-radius: 30px; box-shadow: 0 4px 14px rgba(132,204,22,0.4);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/leaf.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Cream', 'background' => "background: #fefce8;", 'text_color' => '#1d2939', 'button_style' => 'background: #fef08a; border-radius: 30px; border: 2px solid #eab308; box-shadow: 0 4px 14px rgba(250,204,21,0.3);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/sunny.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Steel', 'background' => "background: #f1f5f9;", 'text_color' => '#1d2939', 'button_style' => 'background: #475569; border-radius: 8px; box-shadow: 0 4px 14px rgba(71,85,105,0.3);', 'font_family' => "'Inter', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/basic.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Honey', 'background' => "background: #fffbeb;", 'text_color' => '#1d2939', 'button_style' => 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 30px; box-shadow: 0 4px 14px rgba(245,158,11,0.4);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/sunny.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Sage', 'background' => "background: #f0fdf4;", 'text_color' => '#1d2939', 'button_style' => 'background: #86efac; border-radius: 30px; border: 2px solid #22c55e; box-shadow: 0 4px 14px rgba(34,197,94,0.25);', 'font_family' => "'Quicksand', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/leaf.png', 'bg_image' => null, 'type' => 'Premium'],
            ['name' => 'Berry', 'background' => "background: #faf5ff;", 'text_color' => '#1d2939', 'button_style' => 'background: #7c3aed; border-radius: 12px; box-shadow: 0 4px 14px rgba(124,58,237,0.4);', 'font_family' => "'DM Sans', sans-serif", 'theme_demo' => 'assets/themes/theme-demo/unicorn.png', 'bg_image' => null, 'type' => 'Premium'],
        ];

        foreach ($themes as $theme) {
            Theme::firstOrCreate(
                ['name' => $theme['name']],
                [
                    'background' => $theme['background'],
                    'text_color' => $theme['text_color'],
                    'button_style' => $theme['button_style'],
                    'font_family' => $theme['font_family'],
                    'theme_demo' => $theme['theme_demo'],
                    'bg_image' => $theme['bg_image'],
                    'type' => $theme['type'],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $names = ['Midnight', 'Coral', 'Mint', 'Lavender', 'Ocean', 'Sunset', 'Forest', 'Rose', 'Slate', 'Amber', 'Navy', 'Peach', 'Ice', 'Wine', 'Lime', 'Cream', 'Steel', 'Honey', 'Sage', 'Berry'];
        Theme::whereIn('name', $names)->delete();
    }
};
