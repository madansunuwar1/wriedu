<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CommentHandler;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CommentHandler::class, function ($app) {
            return new CommentHandler();
        });
    }

    public function boot()
    {
        //
    }
}