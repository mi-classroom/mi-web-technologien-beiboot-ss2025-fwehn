<?php

namespace App\Providers;

use App\Services\ExifToolService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ExifToolServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(ExifToolService::class, function () {
            return new ExifToolService();
        });
    }

    public function provides(): array
    {
        return [ExifToolService::class];
    }
}
