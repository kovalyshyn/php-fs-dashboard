<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGetawaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('Getaways', function(Blueprint $table) {
			$table->foreign('destinations')->references('id')->on('Destinations');
			$table->foreign('type_id')->references('id')->on('GetawayType');
			$table->foreign('user_id')->references('id')->on('SwitchUsers');
			$table->foreign('updated_by')->references('id')->on('SwitchUsers');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('Getaways', function(Blueprint $table) {
			$table->dropForeign('getaways_destinations_foreign');
			$table->dropForeign('getaways_type_id_foreign');
			$table->dropForeign('getaways_user_id_foreign');
			$table->dropForeign('getaways_updated_by_foreign');
		});
	}

}
