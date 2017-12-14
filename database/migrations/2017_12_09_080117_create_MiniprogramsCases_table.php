<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiniprogramsCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miniprogram_cases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('thumb');
            $table->string('codeimg');
            $table->string('modules')->unique();
            $table->integer('industry')->unique();
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
        Schema::dropIfExists('miniprogram_cases');
    }
}
