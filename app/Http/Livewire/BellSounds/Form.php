<?php

namespace App\Http\Livewire\BellSounds;

use App\Models\BellSound;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $modelId;
    public $nombre;
    public $ruta_archivo; // can be URL or uploaded file

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'ruta_archivo' => 'required',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->modelId = $id;
            $m = BellSound::withTrashed()->findOrFail($id);
            $this->nombre = $m->nombre;
            $this->ruta_archivo = $m->ruta_archivo;
        }
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->ruta_archivo && is_object($this->ruta_archivo)) {
            $path = $this->ruta_archivo->store('bell_sounds', 'public');
            $data['ruta_archivo'] = '/storage/' . $path;
        }

        if ($this->modelId) {
            $m = BellSound::withTrashed()->findOrFail($this->modelId);
            $m->update($data);
        } else {
            BellSound::create($data);
        }

        return redirect()->route('bell_sounds.index');
    }

    public function render()
    {
        return view('livewire.bell-sounds.form');
    }
}
