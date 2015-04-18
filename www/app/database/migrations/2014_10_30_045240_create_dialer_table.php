<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Dialer', function(Blueprint $table) {
			$table->integer('id', true);
			$table->integer('concurrent_calls');
			$table->integer('total_calls');
			$table->integer('durations');
			$table->string('destination_srv', 2044);
			$table->string('source_num', 2044);
			$table->mediumText('destination_num_list');
			$table->boolean('wait_answer');
			$table->boolean('done');
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
		Schema::drop('Dialer');
	}

}
