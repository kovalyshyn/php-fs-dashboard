<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDestinationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Destinations', function(Blueprint $table) {
			$table->string('global_prefix', 2044)->unique('`unique_global_prefix`');
			$table->string('local_prefix', 2044)->nullable();
			$table->string('agent_prefix', 2044)->nullable();
			$table->string('name', 2044);
			$table->integer('id', true);
			$table->integer('show_getaways');
			$table->string('ussd_balance');
			$table->string('ussd_balance_pattern');
			$table->numeric('rate_user', 5, 2);
			$table->numeric('rate_agent', 5, 2);
			$table->numeric('rate_admin', 5, 2);
			$table->integer('number_length')->default(10)->nullable();
			$table->smallInteger('active')->default('1::smallint');
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
		Schema::drop('Destinations');
	}

}
