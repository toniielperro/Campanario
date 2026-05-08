<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use Carbon\Carbon;

class DashboardMonitor extends Component
{
    public $masterEnabled = false;

    protected $listeners = ['toggleMaster' => 'toggleMaster'];

    public function toggleMaster($value)
    {
        $this->masterEnabled = (bool) $value;
    }

    public function render()
    {
        // Provide active schedules to the front-end
        $now = Carbon::now()->format('H:i:00');
        $day = strtolower(Carbon::now()->format('l'));

        $events = Schedule::with('bellSound')
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get()
            ->filter(function ($s) use ($now, $day) {
                $dias = $s->dias_semana ?: [];
                return in_array($day, $dias) && $s->hora === $now;
            })
            ->values();

        return view('livewire.dashboard-monitor', ['events' => $events]);
    }
}
