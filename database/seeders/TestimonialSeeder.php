<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testimonials = array(
            array(
                'name'=>'Emre A.',
                'title'=>'- İşletme Sahibi',
                'thumbnail'=>'assets/testimonials/customer-2.png',
                'testimonial'=>"Link Profilde ile işimizi kolaylaştırdık, QR kodları oluşturmak ve bağlantıları özelleştirmek
                    artık çok hızlı ve basit.",
            ),
            array(
                'name'=>'Ayşe K.',
                'title'=>'Blogger',
                'thumbnail'=>'assets/testimonials/customer-1.png',
                'testimonial'=>"Link Profilde, blog yazılarımın daha fazla okuyucuya ulaşmasını sağladı. Mükemmel bir
dijital pazarlama aracı!",

            ),
            array(
                'name'=>'Mehmet C.',
                'title'=>'Etkinlik Organizatörü',
                'thumbnail'=>'assets/testimonials/customer-3.png',
                'testimonial'=>"Etkinliklerimiz için QR kodları oluşturmak artık çok kolay. Müşterilerimizin geri bildirimleri
            harika!",
            ),
        );

        foreach($testimonials as $item){
            Testimonial::create([
                'name' => $item['name'],
                'title' => $item['title'],
                'thumbnail' => $item['thumbnail'],
                'testimonial' => $item['testimonial'],
            ]);
        }
    }
}
