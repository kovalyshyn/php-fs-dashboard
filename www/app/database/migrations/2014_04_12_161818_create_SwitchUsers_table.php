 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSwitchUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SwitchUsers', function(Blueprint $table) {
			$table->integer('id', true);
			$table->string('email', 2044)->index('`index_email`');
			$table->string('password', 2044);
			$table->string('name', 2044);
			$table->integer('type')->default(2);
			$table->datetimetz('created_at')->default('now()');
			$table->datetimetz('updated_at')->default('now()');
			$table->integer('parent_id')->default(0)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('SwitchUsers');
	}

}
