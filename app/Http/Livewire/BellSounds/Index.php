<?php

namespace App\Http\Livewire\BellSounds;

use App\Models\BellSound;
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
        $query = BellSound::query();

        if ($this->showTrashed) {
            $query = BellSound::onlyTrashed();
        }

        $items = $query
            ->when($this->search, fn($q) => $q->where('nombre', 'like', "%{$this->search}%"))
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.bell-sounds.index', compact('items'));
    }

    public function delete($id)
    {
        $item = BellSound::findOrFail($id);
        $item->delete();
    }

    public function restore($id)
    {
        $item = BellSound::withTrashed()->findOrFail($id);
        $item->restore();
    }

    public function forceDelete($id)
    {
        $item = BellSound::withTrashed()->findOrFail($id);
        $item->forceDelete();
    }

    public function toggleTrashed()
    {
        $this->showTrashed = ! $this->showTrashed;
        $this->resetPage();
    }
}
