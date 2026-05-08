<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

use App\Http\Controllers\MonitorController;

Route::get('/dashboard', [MonitorController::class, 'dashboard'])->name('dashboard');

// Bell sounds (controllers)
use App\Http\Controllers\BellSoundController;
Route::get('bell-sounds', [BellSoundController::class, 'index'])->name('bell_sounds.index');
Route::get('bell-sounds/create', [BellSoundController::class, 'create'])->name('bell_sounds.create');
Route::post('bell-sounds', [BellSoundController::class, 'store'])->name('bell_sounds.store');
Route::get('bell-sounds/{id}/edit', [BellSoundController::class, 'edit'])->name('bell_sounds.edit');
Route::post('bell-sounds/{id}', [BellSoundController::class, 'update'])->name('bell_sounds.update');
Route::delete('bell-sounds/{id}', [BellSoundController::class, 'destroy'])->name('bell_sounds.destroy');
Route::post('bell-sounds/{id}/restore', [BellSoundController::class, 'restore'])->name('bell_sounds.restore');
Route::delete('bell-sounds/{id}/force', [BellSoundController::class, 'forceDelete'])->name('bell_sounds.forceDelete');

// Schedules (controllers)
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchedulePlayController;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\ScheduleExceptionController;
Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::get('schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
Route::post('schedules', [ScheduleController::class, 'store'])->name('schedules.store');
Route::get('schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
Route::post('schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
Route::post('schedules/{id}/restore', [ScheduleController::class, 'restore'])->name('schedules.restore');
Route::delete('schedules/{id}/force', [ScheduleController::class, 'forceDelete'])->name('schedules.forceDelete');

// Schedule Exceptions (Calendario de Excepciones)
Route::get('exceptions', [ScheduleExceptionController::class, 'index'])->name('exceptions.index');
Route::get('exceptions/create', [ScheduleExceptionController::class, 'create'])->name('exceptions.create');
Route::post('exceptions', [ScheduleExceptionController::class, 'store'])->name('exceptions.store');
Route::get('exceptions/{id}/edit', [ScheduleExceptionController::class, 'edit'])->name('exceptions.edit');
Route::post('exceptions/{id}', [ScheduleExceptionController::class, 'update'])->name('exceptions.update');
Route::delete('exceptions/{id}', [ScheduleExceptionController::class, 'destroy'])->name('exceptions.destroy');
Route::post('exceptions/{id}/restore', [ScheduleExceptionController::class, 'restore'])->name('exceptions.restore');
Route::delete('exceptions/{id}/force', [ScheduleExceptionController::class, 'forceDelete'])->name('exceptions.forceDelete');

// Poll endpoint for dashboard monitor
Route::get('/_monitor/poll', [MonitorController::class, 'poll']);

// Endpoint to fetch active schedules (for client-side validation)
Route::get('/_monitor/active-schedules', [MonitorController::class, 'activeSchedules']);

// Record that a schedule has been played (called from client after successful play)
// Record that a schedule has been played (called from client after successful play)
Route::post('/_monitor/record-play', [MonitorController::class, 'recordPlay']);

// Play history admin page
Route::get('/plays', [SchedulePlayController::class, 'index'])->name('plays.index');

// Sequences (complex repiques)
Route::get('sequences', [SequenceController::class, 'index'])->name('sequences.index');
Route::get('sequences/create', [SequenceController::class, 'create'])->name('sequences.create');
Route::post('sequences', [SequenceController::class, 'store'])->name('sequences.store');
Route::get('sequences/{sequence}/edit', [SequenceController::class, 'edit'])->name('sequences.edit');
Route::post('sequences/{sequence}', [SequenceController::class, 'update'])->name('sequences.update');
Route::delete('sequences/{sequence}', [SequenceController::class, 'destroy'])->name('sequences.destroy');
Route::get('sequences/{sequence}/preview', [SequenceController::class, 'preview'])->name('sequences.preview');
Route::post('sequences/{id}/restore', [SequenceController::class, 'restore'])->name('sequences.restore');
Route::delete('sequences/{id}/force', [SequenceController::class, 'forceDelete'])->name('sequences.forceDelete');
