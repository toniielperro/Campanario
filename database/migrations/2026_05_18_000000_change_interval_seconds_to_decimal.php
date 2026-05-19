<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Convert interval_seconds to decimal to support fractional seconds.
        // NOTE: This uses the Schema change() method which requires doctrine/dbal package.
        Schema::table('sequence_items', function (Blueprint $table) {
            $table->decimal('interval_seconds', 8, 3)->default(1)->change();
        });
    }

    public function down()
    {
        Schema::table('sequence_items', function (Blueprint $table) {
            $table->integer('interval_seconds')->default(1)->change();
        });
    }
};
