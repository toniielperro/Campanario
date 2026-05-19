<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sequences', function (Blueprint $table) {
            $table->integer('repetitions')->default(1)->after('descripcion');
        });
    }

    public function down()
    {
        Schema::table('sequences', function (Blueprint $table) {
            $table->dropColumn('repetitions');
        });
    }
};
