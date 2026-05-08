<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchedulePlay;

class SchedulePlayController extends Controller
{
    public function index(Request $request)
    {
        $plays = SchedulePlay::with(['schedule.bellSound'])
            ->orderBy('played_at', 'desc')
            ->paginate(10);

        return view('plays.index', compact('plays'));
    }
}
