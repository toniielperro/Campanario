<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\BellSound;
use App\Models\Sequence;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $trashed = $request->get('trashed');
        $activoFilter = $request->get('activo');
        // Build query depending on filters. Cases:
        // - activo=0: show schedules with activo=false OR soft-deleted
        // - trashed param: show only soft-deleted
        // - activo=1: show only activo=true
        if ($activoFilter !== null && $activoFilter === '0') {
            $query = Schedule::withTrashed()->with(['sequence','bellSound'])->where(function($q){
                $q->where('activo', false)->orWhereNotNull('deleted_at');
            });
        } else {
            $query = Schedule::with(['sequence','bellSound']);
            if ($trashed) {
                $query = Schedule::onlyTrashed();
            }
            if ($activoFilter !== null && $activoFilter !== '') {
                if ($activoFilter == '1') {
                    $query->where('activo', true);
                }
            }
        }

        if ($search) {
            $query->whereHas('bellSound', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
        }

        $items = $query->orderBy('hora')->paginate(10)->withQueryString();

        return view('schedules.index', compact('items', 'search', 'trashed', 'activoFilter'));
    }

    public function create()
    {
        $sounds = BellSound::orderBy('nombre')->get();
        $sequences = Sequence::orderBy('nombre')->get();
        return view('schedules.create', compact('sequences'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'sequence_id' => 'required|exists:sequences,id',
            'hora' => 'required',
            'dias_semana' => 'nullable|array',
            'fechas_especificas' => 'nullable|array',
            'tipo' => 'nullable|in:ordinaria,especial',
            'frecuencia' => 'nullable|in:lunes_a_viernes,diario,una_sola_vez,personalizado',
            'single_date' => 'nullable|date',
            'activo' => 'nullable|boolean',
        ]);
        // ensure we clear bell_sound_id (we now use sequence only)
        $data['bell_sound_id'] = null;

        // normalize hora to HH:MM:00 if seconds missing
        if (preg_match('/^\d{2}:\d{2}$/', $data['hora'])) {
            $data['hora'] = $data['hora'] . ':00';
        }

        // handle tipo/frecuencia
        $tipo = $data['tipo'] ?? 'ordinaria';
        if($tipo === 'especial'){
            // fechas_especificas come from hidden inputs managed by the calendar widget
            $fechas = array_values(array_filter(array_map(function($d){ if(!$d) return null; try{ return \Carbon\Carbon::parse($d)->toDateString(); }catch(\Throwable $e){ return null; } }, $data['fechas_especificas'] ?? [])));
            $data['fechas_especificas'] = $fechas;
            $data['dias_semana'] = [];
            $data['tipo'] = 'especial';
            $data['frecuencia'] = null;
        } else {
            $freq = $data['frecuencia'] ?? 'lunes_a_viernes';
            if($freq === 'una_sola_vez'){
                $single = $data['single_date'] ?? null;
                $data['fechas_especificas'] = $single ? [\Carbon\Carbon::parse($single)->toDateString()] : [];
                $data['dias_semana'] = [];
                $data['tipo'] = 'ordinaria';
                $data['frecuencia'] = 'una_sola_vez';
            } elseif($freq === 'diario'){
                $data['dias_semana'] = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
                $data['fechas_especificas'] = [];
                $data['tipo'] = 'ordinaria';
                $data['frecuencia'] = 'diario';
            } elseif($freq === 'personalizado'){
                // keep user-selected dias_semana if provided
                $data['dias_semana'] = array_values($data['dias_semana'] ?? []);
                $data['fechas_especificas'] = [];
                $data['tipo'] = 'ordinaria';
                $data['frecuencia'] = 'personalizado';
            } else { // lunes_a_viernes
                $data['dias_semana'] = ['lunes','martes','miercoles','jueves','viernes'];
                $data['fechas_especificas'] = [];
                $data['tipo'] = 'ordinaria';
                $data['frecuencia'] = 'lunes_a_viernes';
            }
        }
        $data['activo'] = (bool) ($data['activo'] ?? true);
        try {
            Log::info('Schedule store payload', $data);
            Schedule::create($data);
            return redirect()->route('schedules.index')->with('pilar_success', 'Programación creada correctamente.');
        } catch (\Throwable $e) {
            report($e);
            $msg = $e->getMessage();
            return back()->withInput()->withErrors(['exception' => 'Error al guardar la programación: ' . $msg]);
        }
    }

    public function edit($id)
    {
        $item = Schedule::withTrashed()->findOrFail($id);
        $sequences = Sequence::orderBy('nombre')->get();
        return view('schedules.edit', compact('item','sequences'));
    }

    public function update(Request $request, $id)
    {
        $item = Schedule::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'sequence_id' => 'required|exists:sequences,id',
            'hora' => 'required',
            'dias_semana' => 'nullable|array',
            'fechas_especificas' => 'nullable|array',
            'activo' => 'nullable|boolean',
            'tipo' => 'nullable|in:ordinaria,especial',
            'frecuencia' => 'nullable|in:lunes_a_viernes,diario,una_sola_vez,personalizado',
        ]);
        $data['bell_sound_id'] = null;
        if (preg_match('/^\d{2}:\d{2}$/', $data['hora'])) {
            $data['hora'] = $data['hora'] . ':00';
        }

        // normalize incoming arrays and dates
        $data['dias_semana'] = array_values($data['dias_semana'] ?? []);
        $data['fechas_especificas'] = array_values(array_filter(array_map(function($d){
            if(!$d) return null; try{ $dt = \Carbon\Carbon::parse($d); return $dt->toDateString(); }catch(\Throwable $e){ return null; }
        }, $data['fechas_especificas'] ?? [])));

        // decide tipo/frecuencia and ensure dias_especificas/dias_semana reflect the choice
        $tipo = $data['tipo'] ?? 'ordinaria';
        $freq = $data['frecuencia'] ?? null;

        if($tipo === 'especial'){
            // fechas_especificas should already be set from inputs
            $data['dias_semana'] = [];
            $data['frecuencia'] = null;
            $data['tipo'] = 'especial';
        } else {
            // ordinary schedule: set dias based on frecuencia
            if($freq === 'una_sola_vez'){
                $single = $request->input('single_date');
                if($single){ try{ $data['fechas_especificas'] = [\Carbon\Carbon::parse($single)->toDateString()]; }catch(\Throwable $e){} }
                $data['dias_semana'] = [];
                $data['frecuencia'] = 'una_sola_vez';
                $data['tipo'] = 'ordinaria';
            } elseif($freq === 'diario'){
                $data['dias_semana'] = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
                $data['fechas_especificas'] = [];
                $data['frecuencia'] = 'diario';
                $data['tipo'] = 'ordinaria';
            } elseif($freq === 'personalizado'){
                // keep the user-provided dias_semana (already normalized above)
                $data['fechas_especificas'] = [];
                $data['frecuencia'] = 'personalizado';
                $data['tipo'] = 'ordinaria';
            } else {
                // default to lunes_a_viernes
                $data['dias_semana'] = ['lunes','martes','miercoles','jueves','viernes'];
                $data['fechas_especificas'] = [];
                $data['frecuencia'] = 'lunes_a_viernes';
                $data['tipo'] = 'ordinaria';
            }
        }
        $data['activo'] = (bool) ($data['activo'] ?? true);

        Log::info('Schedule update payload', $data);

        try {
            $item->update($data);
            return redirect()->route('schedules.index')->with('pilar_success', 'Programación actualizada correctamente.');
        } catch (\Throwable $e) {
            report($e);
            $msg = $e->getMessage();
            return back()->withInput()->withErrors(['exception' => 'Error al actualizar la programación: ' . $msg]);
        }
    }

    public function destroy($id)
    {
        $item = Schedule::findOrFail($id);
        $item->delete();
        return back();
    }

    public function restore($id)
    {
        $item = Schedule::withTrashed()->findOrFail($id);
        $item->restore();
        return back();
    }

    public function forceDelete($id)
    {
        $item = Schedule::withTrashed()->findOrFail($id);
        $item->forceDelete();
        return back();
    }
}
