<?php

namespace Yna\PortToSms;

use Illuminate\Support\ServiceProvider;

class PortToSmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PortToSmsApi::class, function () {
            $config = config('services.port2sms');

            return new PortToSmsApi(
                $config['account'],
                $config['user'],
                $config['password'],
                $config['sender']
            );
        });
    }
}
