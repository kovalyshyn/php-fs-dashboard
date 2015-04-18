<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGetawaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Getaways', function(Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id');
			$table->string('mask', 2044);
			$table->string('ip');
			$table->integer('port');
			$table->integer('limit');
			$table->integer('delay');
			$table->integer('minutes');
			$table->integer('type_id');
			$table->string('sip_profile', 2044);
			$table->string('imei', 20);
			$table->datetimetz('created_at')->default('now()');
			$table->datetimetz('updated_at')->default('now()');
			$table->integer('destinations');
			$table->integer('user_id');
			$table->integer('parent_id');
			$table->smallInteger('active');
			$table->datetimetz('selected')->default('now()');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Getaways');
	}

}
