<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('sequence_id')->nullable()->after('bell_sound_id')->constrained('sequences')->onDelete('set null');
            $table->foreignId('bell_sound_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['sequence_id']);
            $table->dropColumn('sequence_id');
            // cannot easily revert nullability reliably here
        });
    }
};
