<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BellSound;

class BellSoundController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $trashed = $request->get('trashed');
        $activoFilter = $request->get('activo');

        // For BellSound we don't have an 'activo' column; interpret:
        // activo=1 => not deleted; activo=0 => show only trashed
        if ($activoFilter !== null && $activoFilter === '0') {
            $query = BellSound::onlyTrashed();
        } else {
            $query = BellSound::query();
            if ($trashed) {
                $query = BellSound::onlyTrashed();
            }
            // activo=1 -> exclude trashed (default behavior)
        }

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%");
        }

        $items = $query->orderBy('nombre')->paginate(10)->withQueryString();

        return view('bell_sounds.index', compact('items', 'search', 'trashed', 'activoFilter'));
    }

    public function create()
    {
        return view('bell_sounds.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta_archivo' => 'required|file|mimetypes:audio/mpeg,audio/wav,audio/ogg,application/octet-stream',
        ]);

        if ($request->hasFile('ruta_archivo')) {
            $path = $request->file('ruta_archivo')->store('bell_sounds', 'public');
            $data['ruta_archivo'] = '/storage/' . $path;
        }

        BellSound::create($data);
        return redirect()->route('bell_sounds.index')->with('pilar_success', 'Sonido creado correctamente.');
    }

    public function edit($id)
    {
        $item = BellSound::withTrashed()->findOrFail($id);
        return view('bell_sounds.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = BellSound::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta_archivo' => 'nullable|file|mimetypes:audio/mpeg,audio/wav,audio/ogg,application/octet-stream',
        ]);

        if ($request->hasFile('ruta_archivo')) {
            $path = $request->file('ruta_archivo')->store('bell_sounds', 'public');
            $data['ruta_archivo'] = '/storage/' . $path;
        }

        $item->update($data);
        return redirect()->route('bell_sounds.index')->with('pilar_success','Sonido actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = BellSound::findOrFail($id);
        $item->delete();
        return back();
    }

    public function restore($id)
    {
        $item = BellSound::withTrashed()->findOrFail($id);
        $item->restore();
        return redirect()->route('bell_sounds.index', ['activo' => 1])->with('pilar_success', 'Sonido restaurado correctamente.');
    }

    public function forceDelete($id)
    {
        $item = BellSound::withTrashed()->findOrFail($id);
        $item->forceDelete();
        return back();
    }
}
