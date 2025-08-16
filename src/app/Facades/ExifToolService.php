<?php

namespace App\Facades;

use App\Services\ExifToolService as Service;
use Illuminate\Support\Facades\Facade;

class ExifToolService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}
