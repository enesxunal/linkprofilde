<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $themes = array(
            array(
                'name' => 'Basic',
                'background' => "
                    background: #FFFFFF;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 30px;
                    box-shadow: 0px 6px 14px -6px rgb(24 39 75 / 12%), 0px 10px 32px -4px rgb(24 39 75 / 10%), inset 0px 0px 2px 1px rgb(24 39 75 / 5%);
                ',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/basic.png',
                'bg_image' => null,
                'type' => 'Free',
            ),
            array(
                'name' => 'Dark Carbon',
                'background' => "
                    background: #131212;
                ",
                'text_color' => '#FFFFFF',
                'button_style' => '
                    background: #212121;
                    border-radius: 8px;
                    box-shadow: 0px 6px 14px -6px rgb(24 39 75 / 12%), 0px 10px 32px -4px rgb(24 39 75 / 10%), inset 0px 0px 2px 1px rgb(255 255 255 / 5%);
                ',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/dark-carbon.png',
                'bg_image' => null,
                'type' => 'Free',
            ), 
            array(
                'name' => 'Glitch',
                'background' => "
                    background: #FFFFFF;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    border-radius: 4px;
                    background: #FFFFFF;
                    border: 2px solid #000000;
                    box-shadow: 4px 4px 0 #222222;
                ',
                'font_family' => "'MintGrotesk', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/glitch.png',
                'bg_image' => null,
                'type' => 'Free',
            ), 
            array(
                'name' => 'Unicorn',
                'background' => '
                    background: #f5fdf4;
                ',
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #BFB9FA;
                    border-radius: 12px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/unicorn.png',
                'bg_image' => null,
                'type' => 'Free',
            ),
            array(
                'name' => 'Chameleon',
                'background' => "
                    background: #E0EDCD;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    border-radius: 30px;
                    background: #007034;
                ',
                'font_family' => "'Quicksand', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/chameleon.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),  
            array(
                'name' => 'Sunny',
                'background' => '
                    background: #fefceb;
                ',
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFDD00;
                    border-radius: 30px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/sunny.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Autumn',
                'background' => '
                    background: #fff4f1;
                ',
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FF9877;
                    border-radius: 30px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/autumn.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Leaf',
                'background' => '
                    background: #f5fdf4;
                ',
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #A6EB99;
                    border-radius: 30px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/leaf.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Clear Sky',
                'background' => '
                    background: #eff9ff;
                ',
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #5ACAF9;
                    border-radius: 12px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/clear-sky.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Blush',
                'background' => '
                    background: #fff3fc;
                ',
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FF90E8;
                    border-radius: 12px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/blush.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Colorful',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 4px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/colorful.png',
                'bg_image' => 'assets/themes/colorful.jpg',
                'type' => 'Standard',
            ),
            array(
                'name' => 'Winter',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 8px;
                    box-shadow: 0px 6px 14px -6px rgb(24 39 75 / 12%), 0px 10px 32px -4px rgb(24 39 75 / 10%), inset 0px 0px 2px 1px rgb(24 39 75 / 5%);
                ',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/winter.png',
                'bg_image' => 'assets/themes/winter.png',
                'type' => 'Premium',
            ), 
            array(
                'name' => 'Lumen',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 8px;
                    box-shadow: 0px 6px 14px -6px rgb(24 39 75 / 12%), 0px 10px 32px -4px rgb(24 39 75 / 10%), inset 0px 0px 2px 1px rgb(24 39 75 / 5%);
                ',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/lumen.png',
                'bg_image' => 'assets/themes/lumen.jpg',
                'type' => 'Premium',
            ), 
            array(
                'name' => 'Grey Mix',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 8px;
                    box-shadow: 0px 6px 14px -6px rgb(24 39 75 / 12%), 0px 10px 32px -4px rgb(24 39 75 / 10%), inset 0px 0px 2px 1px rgb(24 39 75 / 5%);
                ',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/grey-mix.png',
                'bg_image' => 'assets/themes/greyky.jpg',
                'type' => 'Premium',
            ),  
            array(
                'name' => 'Rainy Night',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#FFFFFF',
                'button_style' => '
                    border-radius: 30px;
                    background: rgba(255, 255, 255, 0.075);
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/rainy-night.png',
                'bg_image' => 'assets/themes/rainy_night.jpg',
                'type' => 'Premium',
            ),  
            array(
                'name' => 'Neon',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#FFFFFF',
                'button_style' => '
                    border-radius: 30px;
                    background: rgba(196, 196, 196, 0.01);
                    box-shadow: inset 0px 17.7895px 25.5438px -16.421px rgb(255 255 255 / 50%), inset 0px -5.92982px 4.10526px -6.38596px rgb(255 255 255 / 75%), inset 0px 3.19298px 5.01754px -1.82456px #ffffff, inset 0px -37.4035px 31.0175px -29.193px rgb(96 68 145 / 30%), inset 0px 44.7017px 45.614px -21.8947px rgb(202 172 255 / 30%), inset 0px 1.82456px 8.21052px rgb(154 146 210 / 30%), inset 0px 0.45614px 18.2456px rgb(227 222 255 / 20%);
                    backdrop-filter: blur(45.614px);
                ',
                'font_family' => "'Bebas Neue', cursive",
                'theme_demo' => 'assets/themes/theme-demo/neon.png',
                'bg_image' => 'assets/themes/neon.jpg',
                'type' => 'Premium',
            ),  
            array(
                'name' => 'Glassy',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    border-radius: 30px;
                    border: 1px solid rgba(255,255,255,0.3);
                    background: linear-gradient(263.81deg, rgba(255, 255, 255, 0.4) 18.8%, rgba(255, 255, 255, 0) 73.34%), rgba(255, 255, 255, 0.25);
                    box-shadow: 0px 1px 2px rgb(0 0 0 / 3%), inset -1px -0.5px 2px rgb(255 255 255 / 40%);
                    backdrop-filter: blur(12px);
                ',
                'font_family' => "'Poppins', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/glassy.png',
                'bg_image' => 'assets/themes/glassmorphism.jpg',
                'type' => 'Premium',
            ), 
            array(
                'name' => 'Desert',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 4px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/desert.png',
                'bg_image' => 'assets/themes/desert.jpg',
                'type' => 'Premium',
            ),
            array(
                'name' => 'Bloody',
                'background' => "
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: cover;
                ",
                'text_color' => '#1d2939',
                'button_style' => '
                    background: #FFFFFF;
                    border-radius: 4px;
                ',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/bloody.png',
                'bg_image' => 'assets/themes/bloody.jpg',
                'type' => 'Premium',
            ),
            // --- 20 yeni tema (rakip sitelerden ilham alındı) ---
            array(
                'name' => 'Midnight',
                'background' => "background: #0f172a;",
                'text_color' => '#f8fafc',
                'button_style' => 'background: #334155; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/dark-carbon.png',
                'bg_image' => null,
                'type' => 'Free',
            ),
            array(
                'name' => 'Coral',
                'background' => "background: #fff5f5;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #f97316; border-radius: 30px; color: #fff; box-shadow: 0 4px 14px rgba(249,115,22,0.35);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/autumn.png',
                'bg_image' => null,
                'type' => 'Free',
            ),
            array(
                'name' => 'Mint',
                'background' => "background: #f0fdf4;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #22c55e; border-radius: 30px; box-shadow: 0 4px 14px rgba(34,197,94,0.3);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/leaf.png',
                'bg_image' => null,
                'type' => 'Free',
            ),
            array(
                'name' => 'Lavender',
                'background' => "background: #faf5ff;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #a78bfa; border-radius: 12px; box-shadow: 0 4px 14px rgba(167,139,250,0.35);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/unicorn.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Ocean',
                'background' => "background: #f0f9ff;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #0ea5e9; border-radius: 12px; box-shadow: 0 4px 14px rgba(14,165,233,0.35);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/clear-sky.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Sunset',
                'background' => "background: #fff7ed;",
                'text_color' => '#1d2939',
                'button_style' => 'background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 30px; box-shadow: 0 4px 14px rgba(249,115,22,0.4);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/autumn.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Forest',
                'background' => "background: #f0fdf4;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #166534; border-radius: 30px; box-shadow: 0 4px 14px rgba(22,101,52,0.35);',
                'font_family' => "'Quicksand', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/chameleon.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Rose',
                'background' => "background: #fff1f2;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #e11d48; border-radius: 12px; box-shadow: 0 4px 14px rgba(225,29,72,0.35);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/blush.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Slate',
                'background' => "background: #f8fafc;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #64748b; border-radius: 8px; box-shadow: 0 4px 14px rgba(100,116,139,0.25);',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/basic.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Amber',
                'background' => "background: #fffbeb;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #d97706; border-radius: 30px; box-shadow: 0 4px 14px rgba(217,119,6,0.35);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/sunny.png',
                'bg_image' => null,
                'type' => 'Standard',
            ),
            array(
                'name' => 'Navy',
                'background' => "background: #0f172a;",
                'text_color' => '#f8fafc',
                'button_style' => 'background: rgba(248,250,252,0.95); border-radius: 12px; color: #0f172a; box-shadow: 0 4px 14px rgba(0,0,0,0.2);',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/dark-carbon.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Peach',
                'background' => "background: #fff7ed;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #fdba74; border-radius: 30px; border: 2px solid #ea580c; box-shadow: 0 4px 14px rgba(251,146,60,0.35);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/autumn.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Ice',
                'background' => "background: linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 100%);",
                'text_color' => '#0c4a6e',
                'button_style' => 'background: rgba(255,255,255,0.9); border-radius: 12px; border: 1px solid #bae6fd; box-shadow: 0 4px 14px rgba(14,165,233,0.2);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/clear-sky.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Wine',
                'background' => "background: #1e1b4b;",
                'text_color' => '#e9d5ff',
                'button_style' => 'background: #c4b5fd; border-radius: 12px; color: #1e1b4b; box-shadow: 0 4px 14px rgba(196,181,253,0.4);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/unicorn.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Lime',
                'background' => "background: #f7fee7;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #84cc16; border-radius: 30px; box-shadow: 0 4px 14px rgba(132,204,22,0.4);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/leaf.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Cream',
                'background' => "background: #fefce8;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #fef08a; border-radius: 30px; border: 2px solid #eab308; box-shadow: 0 4px 14px rgba(250,204,21,0.3);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/sunny.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Steel',
                'background' => "background: #f1f5f9;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #475569; border-radius: 8px; box-shadow: 0 4px 14px rgba(71,85,105,0.3);',
                'font_family' => "'Inter', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/basic.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Honey',
                'background' => "background: #fffbeb;",
                'text_color' => '#1d2939',
                'button_style' => 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 30px; box-shadow: 0 4px 14px rgba(245,158,11,0.4);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/sunny.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Sage',
                'background' => "background: #f0fdf4;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #86efac; border-radius: 30px; border: 2px solid #22c55e; box-shadow: 0 4px 14px rgba(34,197,94,0.25);',
                'font_family' => "'Quicksand', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/leaf.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
            array(
                'name' => 'Berry',
                'background' => "background: #faf5ff;",
                'text_color' => '#1d2939',
                'button_style' => 'background: #7c3aed; border-radius: 12px; box-shadow: 0 4px 14px rgba(124,58,237,0.4);',
                'font_family' => "'DM Sans', sans-serif",
                'theme_demo' => 'assets/themes/theme-demo/unicorn.png',
                'bg_image' => null,
                'type' => 'Premium',
            ),
        );

        // insert theme in the database
        foreach($themes as $theme){
            Theme::create([
                'name' => $theme['name'],
                'background' => $theme['background'],
                'text_color' => $theme['text_color'],
                'button_style' => $theme['button_style'],
                'font_family' => $theme['font_family'],
                'theme_demo' => $theme['theme_demo'],
                'bg_image' => $theme['bg_image'],
                'type' => $theme['type'],
            ]);
        }
    }
}
