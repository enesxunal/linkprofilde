<?php

namespace App\Helpers;

use App\Models\Link;
use App\Models\SmtpSetting;
use App\Models\User;
use Intervention\Image\ImageManagerStatic as Image;

class AppHelper
{
    public static function smtp()
    {
        $smtp = SmtpSetting::first();

        config(['mail.mailers.smtp.host' => $smtp->host]);
        config(['mail.mailers.smtp.port' => (int) $smtp->port]);
        config(['mail.mailers.smtp.username' => $smtp->username]);
        config(['mail.mailers.smtp.password' => $smtp->password]);
        config(['mail.mailers.smtp.encryption' => $smtp->encryption]);
        config(['mail.from.address' => $smtp->sender_email]);
        config(['mail.from.name' => $smtp->sender_name]);

        return $smtp;
    }


    public static function user()
    {
        $id = auth()->user()->id;
        // ->with('qrcodes')
        // ->with('billing')
        // ->with('pricing_plan')
        // ->with('subscription')

        return User::where('id', $id)->first();
    }


    public static function image_uploader($reqImage)
    {
        // $image->save($location.time().$req->branding->getClientOriginalName());
        $location = public_path('/upload/');
        $image = Image::make($reqImage);
        $filename = $reqImage->getClientOriginalName();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $image->save($location . time() . '.' . $extension);
        $imgUrl = 'upload/' . $image->filename . '.' . $image->extension;

        return $imgUrl;
    }


    public static function get_link($id)
    {
        $SA = self::user()->hasRole('SUPER-ADMIN');
        if ($SA) {
            $link = Link::where('id', $id)
                ->with('items')
                ->with('theme')
                ->with('custom_theme')
                ->first();
        } else {
            $link = Link::where('user_id', self::user()->id)
                ->where('id', $id)
                ->with('items')
                ->with('theme')
                ->with('custom_theme')
                ->first();
        }

        return $link;
    }


    public static function limit_checker($item, $count)
    {
        $user = self::user();
        if ($user->hasRole('SUPER-ADMIN')) return false;

        $plan = $user->pricing_plan ?? \App\Models\PricingPlan::orderBy('monthly_price')->first();
        if (!$plan) {
            $limit = 1;
        } else {
            $limit = $plan->{$item} ?? null;
            if ($limit === null) {
                return false;
            }
        }

        if ($limit != 'Limitsiz') {
            if ((int) $limit <= $count) {
                return ucfirst($item) . ' oluşturma sınırı artık bitti. Daha fazla limit almak için lütfen mevcut planınızı güncelleyin.';
            }
        }

        return false;
    }
}
