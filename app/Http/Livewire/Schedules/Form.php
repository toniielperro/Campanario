<?php

namespace App\Http\Livewire\Schedules;

use App\Models\Schedule;
use App\Models\BellSound;
use Livewire\Component;

class Form extends Component
{
    public $modelId;
    public $bell_sound_id;
    public $hora;
    public $dias_semana = [];
    public $activo = true;

    protected function rules()
    {
        return [
            'bell_sound_id' => 'required|exists:bell_sounds,id',
            'hora' => 'required',
            'dias_semana' => 'nullable|array',
            'activo' => 'boolean',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->modelId = $id;
            $m = Schedule::withTrashed()->findOrFail($id);
            $this->bell_sound_id = $m->bell_sound_id;
            $this->hora = $m->hora;
            $this->dias_semana = $m->dias_semana ?? [];
            $this->activo = $m->activo;
        }
    }

    public function save()
    {
        $data = $this->validate();
        $data['dias_semana'] = array_values($this->dias_semana ?: []);

        if ($this->modelId) {
            $m = Schedule::withTrashed()->findOrFail($this->modelId);
            $m->update($data);
        } else {
            Schedule::create($data);
        }

        return redirect()->route('schedules.index');
    }

    public function render()
    {
        $sounds = BellSound::orderBy('nombre')->get();
        return view('livewire.schedules.form', compact('sounds'));
    }
}
