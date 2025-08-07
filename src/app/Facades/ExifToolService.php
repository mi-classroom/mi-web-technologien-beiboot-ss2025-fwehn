<?php

namespace App\Facades;

use App\Services\ExifToolService as Service;
use Pest\Mutate\Event\Facade;

class ExifToolService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}
