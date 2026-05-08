<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Http\Livewire\DashboardMonitor;
use App\Http\Livewire\BellSounds\Index as BellSoundsIndex;
use App\Http\Livewire\BellSounds\Form as BellSoundsForm;
use App\Http\Livewire\Schedules\Index as SchedulesIndex;
use App\Http\Livewire\Schedules\Form as SchedulesForm;
use App\Http\Livewire\ScheduleExceptionsIndex;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Livewire components explicitly to ensure discovery
        if (class_exists(Livewire::class)) {
            Livewire::component('dashboard-monitor', DashboardMonitor::class);
            Livewire::component('bell-sounds.index', BellSoundsIndex::class);
            Livewire::component('bell-sounds.form', BellSoundsForm::class);
            Livewire::component('schedules.index', SchedulesIndex::class);
            Livewire::component('schedules.form', SchedulesForm::class);
            Livewire::component('schedule-exceptions-index', ScheduleExceptionsIndex::class);
        }
    }
}
