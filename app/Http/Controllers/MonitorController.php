<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\SchedulePlay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MonitorController extends Controller
{
    private const MONITOR_TIMEZONE = 'America/Caracas';

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }

    public function poll(Request $request)
    {
        $now = Carbon::now(self::MONITOR_TIMEZONE);
        $time = $now->format('H:i:00');
        $dayEng = strtolower($now->format('l'));
        $dayMap = ['monday'=>'lunes','tuesday'=>'martes','wednesday'=>'miercoles','thursday'=>'jueves','friday'=>'viernes','saturday'=>'sabado','sunday'=>'domingo'];
        $day = $dayMap[$dayEng] ?? $dayEng;
        $todayDate = $now->toDateString();

        $events = Schedule::with('bellSound')
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get()
            ->filter(function ($s) use ($time, $day, $todayDate) {
                $dias = $s->dias_semana ?: [];
                $fechas = $s->fechas_especificas ?: [];
                $weekdayMatch = in_array($day, $dias);
                $dateMatch = in_array($todayDate, $fechas);
                return ($weekdayMatch || $dateMatch) && $s->hora === $time;
            })
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'hora' => $s->hora,
                    'bell_sound' => [
                        'id' => $s->bellSound->id,
                        'nombre' => $s->bellSound->nombre,
                        'ruta_archivo' => $s->bellSound->ruta_archivo,
                    ],
                ];
            })->values();

        return response()->json($events);
    }

    public function activeSchedules(Request $request)
    {
        $schedules = Schedule::with(['bellSound','sequence.items.bellSound'])
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get();

        // load today's plays for efficient lookup (be defensive if table/migration missing)
        try {
            $startOfDay = Carbon::today(self::MONITOR_TIMEZONE)->startOfDay();
            $endOfDay = Carbon::today(self::MONITOR_TIMEZONE)->endOfDay();
            $plays = SchedulePlay::whereBetween('played_at', [$startOfDay, $endOfDay])->get()->groupBy('schedule_id');
        } catch (\Throwable $e) {
            Log::warning('Unable to load schedule plays for dashboard: '.$e->getMessage());
            $plays = collect([]);
        }

        $todayDate = Carbon::today(self::MONITOR_TIMEZONE)->toDateString();
        $exceptions = \App\Models\ScheduleException::where('activo',true)->whereNull('deleted_at')->get();

        $result = $schedules->map(function ($s) use ($plays, $todayDate, $exceptions) {
            $played = false;
            if ($plays && isset($plays[$s->id])) {
                foreach ($plays[$s->id] as $p) {
                    try {
                        $playedTime = Carbon::parse($p->played_at)->format('H:i');
                        $scheduleTime = substr($s->hora, 0, 5);
                        if ($playedTime === $scheduleTime) { $played = true; break; }
                    } catch (\Throwable $e) {
                        // ignore malformed played_at values
                    }
                }
            }

            $sequence = null;
            $fechas = $s->fechas_especificas ?: [];
            if($s->sequence){
                $items = $s->sequence->items()->orderBy('orden')->with('bellSound')->get()->map(function($it){
                    return [
                        'id' => $it->id,
                        'orden' => $it->orden,
                        'interval_seconds' => $it->interval_seconds,
                        'bell_sound' => [
                            'id' => $it->bellSound->id ?? null,
                            'nombre' => $it->bellSound->nombre ?? null,
                            'ruta_archivo' => $it->bellSound->ruta_archivo ?? null,
                        ],
                    ];
                })->values()->toArray();

                $sequence = [
                    'id' => $s->sequence->id,
                    'nombre' => $s->sequence->nombre,
                    'repetitions' => $s->sequence->repetitions ?? 1,
                    'items' => $items,
                ];
            }

            // indicate if schedule applies today either by weekday or specific date
            $todayEng = strtolower(Carbon::today(self::MONITOR_TIMEZONE)->format('l'));
            $todayMap = ['monday'=>'lunes','tuesday'=>'martes','wednesday'=>'miercoles','thursday'=>'jueves','friday'=>'viernes','saturday'=>'sabado','sunday'=>'domingo'];
            $todayEs = $todayMap[$todayEng] ?? $todayEng;
            $todayApplies = false;
            if(!empty($fechas)){
                foreach($fechas as $f){
                    try{
                        if(Carbon::parse($f)->toDateString() === $todayDate){
                            $todayApplies = true; break;
                        }
                    }catch(\Throwable $e){ }
                }
            }
            if(!$todayApplies && in_array($todayEs, $s->dias_semana ?: [])){
                $todayApplies = true;
            }
            if(!empty($fechas) && !$todayApplies){
                Log::info('schedule applies_today check', ['id'=>$s->id, 'fechas'=>$fechas, 'today'=>$todayDate, 'dias'=>$s->dias_semana]);
            }

            // determine if any exception blocks this schedule today/time
            $blockedByException = false;
            foreach($exceptions as $ex){
                $fe = $ex->fechas_especificas ?: [];
                $matchDate = false;
                foreach($fe as $f){
                    try{ if(\Carbon\Carbon::parse($f)->toDateString() === $todayDate) { $matchDate = true; break; } }catch(\Throwable $e){}
                }
                if($matchDate){
                    try{
                        $st = \Carbon\Carbon::createFromFormat('H:i:s', $ex->start_time);
                        $et = \Carbon\Carbon::createFromFormat('H:i:s', $ex->end_time);
                        $sch = \Carbon\Carbon::createFromFormat('H:i:s', $s->hora);
                    }catch(\Throwable $e){ continue; }
                    if($sch->betweenIncluded($st,$et)){
                        $blockedByException = true; break;
                    }
                }
            }

            return [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'hora' => $s->hora,
                'dias_semana' => $s->dias_semana ?: [],
                'fechas_especificas' => $fechas,
                'played' => $played,
                'bell_sound' => [
                    'id' => $s->bellSound->id ?? null,
                    'nombre' => $s->bellSound->nombre ?? null,
                    'ruta_archivo' => $s->bellSound->ruta_archivo ?? null,
                ],
                'sequence' => $sequence,
                'applies_today' => $todayApplies,
                'blocked_by_exception' => $blockedByException,
            ];
        });

        try {
            $todayPlaysCount = SchedulePlay::whereDate('played_at', Carbon::today(self::MONITOR_TIMEZONE)->toDateString())->count();
        } catch (\Throwable $e) {
            Log::warning('Unable to count today schedule plays: '.$e->getMessage());
            $todayPlaysCount = 0;
        }

        return response()->json([
            'schedules' => $result,
            'today_plays_count' => $todayPlaysCount,
        ]);
    }

    public function recordPlay(Request $request)
    {
        $request->validate(['schedule_id' => 'required|exists:schedules,id']);
        $id = $request->input('schedule_id');
        SchedulePlay::create(['schedule_id' => $id, 'played_at' => Carbon::now(self::MONITOR_TIMEZONE)]);
        return response()->json(['ok' => true]);
    }
}
