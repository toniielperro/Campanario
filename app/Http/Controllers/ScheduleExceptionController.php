<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleException;
use Illuminate\Support\Facades\Log;

class ScheduleExceptionController extends Controller
{
    public function index()
    {
        return view('exceptions.index');
    }

    public function create()
    {
        return view('exceptions.create');
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('ScheduleException store request', $request->all());
        $data = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'fechas_especificas' => 'nullable|array',
            'activo' => 'nullable|boolean',
        ]);
        // normalize fechas_especificas: remove empties and format as Y-m-d
        $rawFechas = $data['fechas_especificas'] ?? [];
        $fechas = [];
        if(is_array($rawFechas)){
            foreach($rawFechas as $f){
                if(empty($f)) continue;
                try{ $fechas[] = \Carbon\Carbon::parse($f)->toDateString(); }catch(\Throwable $e){ continue; }
            }
        }
        $data['fechas_especificas'] = array_values($fechas);
        $data['activo'] = (bool) ($data['activo'] ?? true);
        // normalize times to H:i:s
        try{
            $data['start_time'] = \Carbon\Carbon::createFromFormat('H:i', $data['start_time'])->format('H:i:s');
            $data['end_time'] = \Carbon\Carbon::createFromFormat('H:i', $data['end_time'])->format('H:i:s');
        }catch(\Throwable $e){ }
        try{
            $ex = ScheduleException::create($data);
            \Illuminate\Support\Facades\Log::info('ScheduleException created', ['id'=>$ex->id, 'data'=>$data]);
            return redirect()->route('exceptions.index')->with('pilar_success','Excepción creada correctamente.');
        }catch(\Throwable $e){
            report($e); \Illuminate\Support\Facades\Log::error('ScheduleException create failed', ['error'=>$e->getMessage(), 'data'=>$data]);
            return back()->withInput()->withErrors(['exception'=>$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $item = ScheduleException::withTrashed()->findOrFail($id);
        return view('exceptions.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = ScheduleException::withTrashed()->findOrFail($id);
        \Illuminate\Support\Facades\Log::info('ScheduleException update request', ['id'=>$id]+$request->all());
        $data = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'fechas_especificas' => 'nullable|array',
            'activo' => 'nullable|boolean',
        ]);
        $data['fechas_especificas'] = array_values($data['fechas_especificas'] ?? []);
        $data['activo'] = (bool) ($data['activo'] ?? true);
        try{
            $data['start_time'] = \Carbon\Carbon::createFromFormat('H:i', $data['start_time'])->format('H:i:s');
            $data['end_time'] = \Carbon\Carbon::createFromFormat('H:i', $data['end_time'])->format('H:i:s');
        }catch(\Throwable $e){ }
        try{ $item->update($data); \Illuminate\Support\Facades\Log::info('ScheduleException updated', ['id'=>$item->id, 'data'=>$data]); return redirect()->route('exceptions.index')->with('pilar_success','Excepción actualizada correctamente.'); }catch(\Throwable $e){ report($e); \Illuminate\Support\Facades\Log::error('ScheduleException update failed', ['error'=>$e->getMessage(), 'id'=>$id, 'data'=>$data]); return back()->withInput()->withErrors(['exception'=>$e->getMessage()]); }
    }

    public function destroy($id)
    {
        $item = ScheduleException::findOrFail($id);
        $item->delete();
        return back();
    }

    public function restore($id)
    {
        $item = ScheduleException::withTrashed()->findOrFail($id);
        $item->restore();
        return back();
    }

    public function forceDelete($id)
    {
        $item = ScheduleException::withTrashed()->findOrFail($id);
        $item->forceDelete();
        return back();
    }
}
