<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVotesToWordTasksTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('word_tasks', function (Blueprint $table) {
			$table->boolean('important')->default(0);
			$table->integer('task_data_id')->default(0); // 自定义任务数据id
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('word_tasks', function (Blueprint $table) {
			$table->dropColumn(['important', 'task_data_id']);
		});
	}
}
