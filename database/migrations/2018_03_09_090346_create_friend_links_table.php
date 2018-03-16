<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriendLinksTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('friend_links', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('friend_id');
			$table->string('name');
			$table->string('link');
			$table->boolean('spider_show')->default(1);
			$table->boolean('synced')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('friend_links');
	}
}
