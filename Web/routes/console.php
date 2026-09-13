<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:check-absent')
    ->dailyAt('15:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

Schedule::command('sanctum:prune-expired --hours=24')
    ->daily()
    ->withoutOverlapping();
