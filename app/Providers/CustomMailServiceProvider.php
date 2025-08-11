<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class CustomMailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Config::set('mail.from.address', 'noreply@wrieducations.com');
        Config::set('mail.from.name', 'WRIEducations');
    }
}