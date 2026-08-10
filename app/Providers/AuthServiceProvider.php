<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\AppSetting;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $app = AppSetting::first();
            // dd($app, $notifiable->name);
            // return (new MailMessage)
            //     ->subject('Verify Email Address')
            //     ->line('Click the button below to verify your email address.')
            //     ->action('Verify Email Address', $url);

            return (new MailMessage)->view('mail.email-verification', [
                'url' => $url, 'app' => $app, 'user' => $notifiable,
            ]);
        });


        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $app = AppSetting::first();
            // Get the user's email address
            $email = $notifiable->getEmailForPasswordReset();

            // Build the password reset URL using the $token
            $resetUrl = url('reset-password', $token) . "?email=$email";

            // dd($email, $resetUrl);
            // return (new MailMessage)
            //     ->view('emails.custom_password_reset', ['url' => url('password/reset', $token)])
            //     ->line('You are receiving this email because we received a password reset request for your account.')
            //     ->line('If you did not request a password reset, no further action is required.');

            return (new MailMessage)->view('mail.reset-password', [
                'url' => $resetUrl, 'app' => $app, 'user' => $notifiable,
            ]);
        });
    }
}
