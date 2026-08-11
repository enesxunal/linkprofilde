<?php

namespace App\Helpers;

use App\Models\Link;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use InvalidArgumentException;

class AppHelper
{
    private const BLOCKED_IMAGE_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'html', 'htm', 'shtml', 'svg', 'svgz', 'js', 'cgi', 'pl', 'py', 'sh',
        'webp',
    ];

    private const MIME_TO_EXTENSION = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    private const EXTENSION_TO_FAMILY = [
        'jpg' => 'jpg',
        'jpeg' => 'jpg',
        'png' => 'png',
    ];

    private const MAX_MEGAPIXELS = 20;

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

        return User::where('id', $id)->first();
    }


    public static function imageRules(int $maxKb): array
    {
        return [
            'image',
            'mimes:jpeg,jpg,png',
            'max:' . $maxKb,
        ];
    }


    public static function image_uploader($reqImage)
    {
        $clientFamily = null;

        if ($reqImage instanceof UploadedFile) {
            $clientExt = strtolower($reqImage->getClientOriginalExtension());
            $clientName = str_replace('\\', '/', $reqImage->getClientOriginalName());

            if (
                $clientExt === ''
                || in_array($clientExt, self::BLOCKED_IMAGE_EXTENSIONS, true)
                || !isset(self::EXTENSION_TO_FAMILY[$clientExt])
                || str_contains($clientName, '..')
            ) {
                throw new InvalidArgumentException('Invalid image file.');
            }

            $clientFamily = self::EXTENSION_TO_FAMILY[$clientExt];
        }

        $uploadDir = public_path('upload');
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            throw new InvalidArgumentException('Upload directory is not writable.');
        }

        $image = Image::make($reqImage);

        $width = (int) $image->width();
        $height = (int) $image->height();
        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Invalid image dimensions.');
        }
        if (($width * $height) > (self::MAX_MEGAPIXELS * 1000000)) {
            throw new InvalidArgumentException('Image resolution is too large.');
        }

        $mime = strtolower((string) $image->mime());
        if (!isset(self::MIME_TO_EXTENSION[$mime])) {
            throw new InvalidArgumentException('Only JPEG and PNG images are allowed.');
        }

        $extension = self::MIME_TO_EXTENSION[$mime];
        if ($clientFamily !== null && $clientFamily !== $extension) {
            throw new InvalidArgumentException('Image type does not match file extension.');
        }

        $filename = Str::uuid()->toString() . '.' . $extension;
        $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
        $image->save($fullPath, 90, $extension);

        return 'upload/' . $filename;
    }


    public static function safeDeleteUpload(?string $path): bool
    {
        if ($path === null) {
            return false;
        }

        $path = str_replace('\\', '/', trim($path));
        if ($path === '') {
            return false;
        }

        if (
            str_contains($path, '..')
            || str_contains($path, "\0")
            || str_contains($path, '://')
            || str_starts_with($path, '/')
            || preg_match('/^[a-zA-Z]:\//', $path)
        ) {
            return false;
        }

        if (!str_starts_with($path, 'upload/')) {
            return false;
        }

        $relative = substr($path, strlen('upload/'));
        if ($relative === '' || str_contains($relative, '/') || $relative === '.' || $relative === '..') {
            return false;
        }

        $uploadRoot = public_path('upload');
        $realRoot = realpath($uploadRoot);
        if ($realRoot === false) {
            return false;
        }

        $candidate = $uploadRoot . DIRECTORY_SEPARATOR . $relative;
        if (is_link($candidate)) {
            return false;
        }
        if (!is_file($candidate)) {
            return false;
        }

        $realTarget = realpath($candidate);
        if ($realTarget === false || is_link($realTarget)) {
            return false;
        }
        if (!is_file($realTarget)) {
            return false;
        }

        $rootPrefix = rtrim($realRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($realTarget, $rootPrefix)) {
            return false;
        }

        return @unlink($realTarget);
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
