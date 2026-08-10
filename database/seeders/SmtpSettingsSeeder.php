<?php

namespace Database\Seeders;

use App\Models\SmtpSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmtpSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            "host" => session('mail_host') ?? "",
            "port" => session('mail_port') ?? "",
            "username" => session('mail_username') ?? "",
            "password" => session('mail_password') ?? "",
            "encryption" => session('mail_encryption') ?? "",
            "sender_email" => session('mail_from_address') ?? "",
            "sender_name" => session('mail_from_name') ?? "",
        );

        SmtpSetting::create($data);
    }
}
