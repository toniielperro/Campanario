<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sequence;
use App\Models\BellSound;
use App\Models\SequenceItem;

class SequenceController extends Controller
{
    public function index()
    {
        $activo = request('activo', '1');
        $query = Sequence::withCount('items');
        if ($activo === '0') {
            // show only trashed records in the papelera
            $query = Sequence::onlyTrashed()->withCount('items');
        }
        $sequences = $query->paginate(20);
        return view('sequences.index', compact('sequences', 'activo'));
    }

    public function create()
    {
        $sounds = BellSound::all();
        return view('sequences.create', compact('sounds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'items' => 'nullable|array'
        ]);

        $sequence = Sequence::create($data);
        if(!empty($data['items'])){
            // sort items by 'orden' if present
            usort($data['items'], function($a,$b){ return intval($a['orden'] ?? 0) <=> intval($b['orden'] ?? 0); });
            foreach($data['items'] as $i => $it){
                SequenceItem::create([
                    'sequence_id' => $sequence->id,
                    'bell_sound_id' => $it['bell_sound_id'],
                    'orden' => $it['orden'] ?? $i,
                    'interval_seconds' => $it['interval_seconds'] ?? 1,
                ]);
            }
        }

        return redirect()->route('sequences.index')->with('pilar_success','Secuencia creada correctamente.');
    }

    public function edit(Sequence $sequence)
    {
        $sounds = BellSound::all();
        $sequence->load(['items' => function($q){ $q->orderBy('orden'); }, 'items.bellSound']);
        return view('sequences.edit', compact('sequence','sounds'));
    }

    public function update(Request $request, Sequence $sequence)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'items' => 'nullable|array'
        ]);

        $sequence->update($data);
        // replace items
        $sequence->items()->delete();
        if(!empty($data['items'])){
            usort($data['items'], function($a,$b){ return intval($a['orden'] ?? 0) <=> intval($b['orden'] ?? 0); });
            foreach($data['items'] as $i => $it){
                SequenceItem::create([
                    'sequence_id' => $sequence->id,
                    'bell_sound_id' => $it['bell_sound_id'],
                    'orden' => $it['orden'] ?? $i,
                    'interval_seconds' => $it['interval_seconds'] ?? 1,
                ]);
            }
        }

        return redirect()->route('sequences.index')->with('pilar_success','Secuencia actualizada correctamente.');
    }

    public function destroy(Sequence $sequence)
    {
        $sequence->delete();
        return redirect()->route('sequences.index')->with('pilar_success','Secuencia enviada a la papelera.');
    }

    // preview JSON used by admin to play a quick preview of the sequence
    public function preview($id)
    {
        // allow previewing trashed sequences from the papelera
        $sequence = Sequence::withTrashed()->findOrFail($id);
        // ensure items are returned ordered by `orden`
        $itemsCollection = $sequence->items()->orderBy('orden')->with('bellSound')->get();
        $items = $itemsCollection->map(function($it){
            return [
                'id' => $it->id,
                'orden' => $it->orden,
                'interval_seconds' => $it->interval_seconds,
                'bell_sound' => $it->bellSound ? [
                    'id' => $it->bellSound->id,
                    'nombre' => $it->bellSound->nombre,
                    'ruta_archivo' => $it->bellSound->ruta_archivo,
                ] : null,
            ];
        })->values();

        return response()->json(['id' => $sequence->id, 'nombre' => $sequence->nombre, 'items' => $items]);
    }

    public function restore($id)
    {
        try {
            $sequence = Sequence::withTrashed()->findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Attempting to restore sequence (robust)', ['id' => $id, 'deleted_at_before' => $sequence->deleted_at]);

            // First try the standard restore
            try {
                $sequence->restore();
            } catch (\Throwable $inner) {
                \Illuminate\Support\Facades\Log::warning('Standard restore() threw', ['id' => $id, 'error' => $inner->getMessage()]);
            }

            // Refresh from DB and inspect
            $sequence = Sequence::withTrashed()->find($id);
            \Illuminate\Support\Facades\Log::info('After restore() check', ['id' => $id, 'deleted_at_after' => $sequence->deleted_at]);

            // If still trashed, attempt a direct DB update to clear deleted_at
            if ($sequence && $sequence->deleted_at) {
                \Illuminate\Support\Facades\Log::warning('Sequence still trashed, forcing deleted_at = null', ['id' => $id]);
                \DB::table('sequences')->where('id', $id)->update(['deleted_at' => null, 'updated_at' => now()]);
                \Illuminate\Support\Facades\Log::info('Forced untrash via query', ['id' => $id]);
            }

            return redirect()->route('sequences.index', ['activo' => 1])->with('pilar_success', 'Secuencia restaurada correctamente.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Sequence restore failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('sequences.index')->with('pilar_success', 'Error restaurando la secuencia.');
        }
    }

    public function forceDelete($id)
    {
        $sequence = Sequence::withTrashed()->findOrFail($id);
        $sequence->forceDelete();
        return back()->with('pilar_success', 'Secuencia eliminada definitivamente.');
    }
}
