<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ScheduleException;

class ScheduleExceptionsIndex extends Component
{
    use WithPagination;
    public $search = '';
    public $showTrashed = false;

    protected $updatesQueryString = ['search', 'showTrashed'];

    public function toggleTrashed(){ $this->showTrashed = !$this->showTrashed; $this->resetPage(); }

    public function render()
    {
        $query = ScheduleException::query();
        if($this->showTrashed) $query = ScheduleException::withTrashed();
        if($this->search){
            $s = $this->search;
            // if looks like a date YYYY-MM-DD, search fechas_especificas
            if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)){
                $query = $query->whereJsonContains('fechas_especificas', $s);
            } else {
                $query = $query->where('nombre','like','%'.$s.'%');
            }
        }
        $items = $query->orderBy('start_time')->paginate(10);
        return view('livewire.schedule-exceptions-index', compact('items'));
    }
}
