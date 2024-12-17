<?php
namespace App\Traits;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

trait MailConfiguration
{
    public function configureMailCredentials()
    {
        if(Schema::hasTable('settings')) {
            $emailSettings = Setting::where(['group' => 'email_notification', 'type' => 'General'])
                ->get();
            if ($emailSettings) {
                Config::set('mail.mailers.smtp.host', isset($emailSettings[4]->value)
                    ? $emailSettings[4]->value : env('MAIL_HOST'),
                );
                Config::set('mail.mailers.smtp.port', isset($emailSettings[5]->value)
                    ? $emailSettings[5]->value : env('MAIL_PORT'),
                );
                Config::set('mail.mailers.smtp.encryption', isset($emailSettings[8]->value)
                    ? $emailSettings[8]->value : env('MAIL_ENCRYPTION'),
                );
                Config::set('mail.mailers.smtp.username', isset($emailSettings[6]->value)
                    ? $emailSettings[6]->value : env('MAIL_USERNAME'),
                );
                Config::set('mail.mailers.smtp.password', isset($emailSettings[7]->value)
                    ? $emailSettings[7]->value : env('MAIL_PASSWORD'),
                );
                Config::set('mail.from.address', isset($emailSettings[2]->value)
                    ? $emailSettings[2]->value : env('MAIL_FROM_ADDRESS'),
                );
                Config::set('mail.from.name', isset($emailSettings[0]->value)
                    ? $emailSettings[0]->value : env('MAIL_FROM_NAME'),
                );
            }
        }
    }
}
