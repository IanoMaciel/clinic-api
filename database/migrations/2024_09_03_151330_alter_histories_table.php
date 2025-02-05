<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHistoriesTable extends Migration  {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('histories', function (Blueprint $table) {
            $table->date('date_attachment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('histories', function (Blueprint $table) {
            $table->dropColumn('date_attachment');
        });
    }
}
