<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWordTasksTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('word_tasks', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name'); // 云网客或seo
			$table->string('task_type'); // 云网客或seo
			$table->integer('pid'); // 所属项目
			$table->integer('state');
			$table->string('site')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('word_tasks');
	}
}
