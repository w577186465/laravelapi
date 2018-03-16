<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('sites', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->index();
			$table->string('domain')->index();
			$table->string('admin_url')->default(null);
			$table->string('admin_username')->default(null);
			$table->string('admin_password')->default(null);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('sites');
	}
}
