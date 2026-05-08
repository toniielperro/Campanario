<?php

namespace App\Http\Livewire\Schedules;

use App\Models\Schedule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showTrashed = false;

    protected $queryString = ['search'];

    public function render()
    {
        $query = Schedule::with('bellSound');

        if ($this->showTrashed) {
            $query = Schedule::onlyTrashed();
        }

        $items = $query
            ->when($this->search, fn($q) => $q->whereHas('bellSound', fn($qb) => $qb->where('nombre', 'like', "%{$this->search}%")))
            ->orderBy('hora')
            ->paginate(10);

        return view('livewire.schedules.index', compact('items'));
    }

    public function toggleActive($id)
    {
        $s = Schedule::withTrashed()->findOrFail($id);
        $s->activo = !$s->activo;
        $s->save();
    }

    public function delete($id)
    {
        $s = Schedule::findOrFail($id);
        $s->delete();
    }

    public function restore($id)
    {
        $s = Schedule::withTrashed()->findOrFail($id);
        $s->restore();
    }

    public function forceDelete($id)
    {
        $s = Schedule::withTrashed()->findOrFail($id);
        $s->forceDelete();
    }

    public function toggleTrashed()
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }
}
