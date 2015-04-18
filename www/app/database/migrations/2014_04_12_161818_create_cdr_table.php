<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCdrTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cdr', function(Blueprint $table) {
			$table->string('caller_id_name', 2044);
			$table->string('caller_id_number', 2044);
			$table->string('destination_number', 2044);
			$table->string('context', 2044);
			$table->string('start_stamp', 2044);
			$table->string('answer_stamp', 2044)->nullable();
			$table->string('end_stamp', 2044);
			$table->integer('duration')->nullable();
			$table->integer('billsec')->nullable();
			$table->string('hangup_cause', 2044);
			$table->string('uuid', 2044);
			$table->string('read_codec', 2044);
			$table->string('write_codec', 2044);
			$table->string('sip_hangup_disposition', 2044);
			$table->string('ani', 2044)->nullable();
			$table->integer('gw_id')->nullable()->index('`index_gw_id`');
			$table->integer('destination_id')->nullable()->index('`index_gw_destination_id`');
			$table->numeric('rate_user', 5, 2);
			$table->numeric('rate_agent', 5, 2);
			$table->numeric('rate_admin', 5, 2);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cdr');
	}

}
