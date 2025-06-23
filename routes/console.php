<?php

use App\Console\Commands\MakeDto;
use App\Console\Commands\MakeInterface;
use App\Console\Commands\MakeService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//# registering make service class command
Artisan::command('make:service {name}', function ($name) {
    $this->call(MakeService::class, ['name' => $name]);
});

//~ registering make interface class command
Artisan::command('make:interface {name}', function ($name) {
    $this->call(MakeInterface::class, ['name' => $name]);
});

//^ registering make dto class command
Artisan::command('make:dto {name}', function ($name) {
    $this->call(MakeDto::class, ['name' => $name]);
});
