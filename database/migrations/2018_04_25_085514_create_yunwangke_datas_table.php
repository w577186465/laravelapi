<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYunwangkeDatasTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('yunwangke_datas', function (Blueprint $table) {
			$table->integer("yunwangke_id")->unique();
			$table->text("cookies")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('yunwangke_datas', function (Blueprint $table) {
			$table->dropColumn(['yunwangke_datas']);
		});
	}
}
