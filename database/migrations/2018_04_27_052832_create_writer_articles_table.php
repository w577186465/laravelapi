<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWriterArticlesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('writer_articles', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->text('content');
			$table->string('keywords')->nullable();
			$table->integer('parent')->index();
			$table->integer('input_status')->default(0); // 0 未发布 1 发布成功
			$table->timestamp('input_at')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('writer_articles');
	}
}
