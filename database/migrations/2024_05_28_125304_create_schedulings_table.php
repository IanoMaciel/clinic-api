<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedulings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('');

            $table->datetime('date');

            $table->foreignId('customer_id')->references('customers')->on('id');
            $table->foreignId('service_id')->references('services')->on('id');
            $table->
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedulings');
    }
}
