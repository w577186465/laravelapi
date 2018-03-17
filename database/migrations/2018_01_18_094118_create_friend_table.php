<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('friends', function (Blueprint $table) {
			$table->increments('id');
			$table->string('home_url');
			$table->string('page_url');
			$table->string('secret')->index();
			$table->integer('site_id')->index();
			$table->integer('syncstatus')->index()->default(0); // 0未更新 1更新成功
			$table->integer('status'); // 项目状态 0未验证 1验证成功
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('friends');
	}
}
