<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRanksTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('ranks', function (Blueprint $table) {
			$table->increments('id');
			$table->string('keyword');
			$table->string('site')->index();
			$table->integer('kwid')->index();
			$table->integer('rank')->index();
			$table->integer('rankchange')->default(0);
			$table->string('url');
			$table->char('hash', 32);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('ranks');
	}
}
