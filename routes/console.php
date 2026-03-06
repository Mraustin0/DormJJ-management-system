<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// อัปเดต status สัญญาทุกวัน เวลา 00:05 น.
Schedule::command('contracts:update-status')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();
