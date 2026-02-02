<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule expiry notifications to run daily at 9 AM
Schedule::command('documents:send-expiry-notifications')
    ->dailyAt('09:00')
    ->timezone('America/New_York');
