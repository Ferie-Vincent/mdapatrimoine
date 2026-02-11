<?php

use App\Jobs\GenerateMonthliesJob;
use App\Jobs\GenerateMonthlySciReportJob;
use App\Jobs\SendRemindersJob;
use App\Models\Sci;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate monthly entries on the 1st of each month at 01:00
Schedule::job(new GenerateMonthliesJob())->monthlyOn(1, '01:00');

// Send reminders daily at 08:00
Schedule::job(new SendRemindersJob())->dailyAt('08:00');

// Generate monthly SCI reports on the 1st of each month at 06:00
Schedule::call(function () {
    $previousMonth = Carbon::now()->subMonth()->format('Y-m');
    $scis = Sci::where('is_active', true)->get();

    foreach ($scis as $sci) {
        GenerateMonthlySciReportJob::dispatch($sci, $previousMonth);
    }

    Log::info("Scheduled GenerateMonthlySciReportJob for {$scis->count()} active SCIs for month {$previousMonth}.");
})->monthlyOn(1, '06:00');

// Expire leases past their end date daily at 01:30
Schedule::command('leases:expire')->dailyAt('01:30');

// Update overdue status daily at 00:30
Schedule::call(function () {
    $updated = DB::table('lease_monthlies')
        ->where('status', 'impaye')
        ->where('due_date', '<', Carbon::today())
        ->update(['status' => 'en_retard']);

    Log::info("Updated {$updated} overdue lease monthlies from 'impaye' to 'en_retard'.");
})->dailyAt('00:30');
