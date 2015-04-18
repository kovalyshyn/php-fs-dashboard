<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNumberListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('NumberList', function(Blueprint $table) {
			$table->integer('id', true);
			$table->integer('type_id');
			$table->string('caller_id_number', 2044);
			$table->boolean('blacklist');
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
		Schema::drop('NumberList');
	}

}
