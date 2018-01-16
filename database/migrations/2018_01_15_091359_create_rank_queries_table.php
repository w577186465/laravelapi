<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRankQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rank_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // 云网客或seo
            $table->string('task_type'); // 云网客或seo
            $table->integer('pid'); // 所属项目
            $table->integer('state');
            $table->string('site');
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
        Schema::dropIfExists('rank_queries');
    }
}
