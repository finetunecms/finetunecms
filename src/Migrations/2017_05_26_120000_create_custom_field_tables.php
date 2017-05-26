<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ft_fields', function(Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->integer('type_id')->unsigned();
			$table->integer('auto_complete')->default(0);
			$table->integer('auto_focus')->default(0);
			$table->integer('checked')->default(0);
			$table->integer('disabled')->default(0);
			$table->integer('max')->default(0);
			$table->integer('min')->default(0);
			$table->integer('multiple')->default(0);
			$table->integer('step')->default(0);
			$table->string('regex_pattern')->default(0);
			$table->string('placeholder')->nullable();
			$table->string('readonly')->default(0);
			$table->string('required')->default(0);
			$table->string('name');
			$table->string('type');
			$table->text('json')->nullable();
            $table->string('class')->nullable();
            $table->string('values')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
			$table->softDeletes();
		});
		Schema::create('ft_values', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('node_id');
			$table->integer('field_id');
			$table->binary('value')->nullable();
            $table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('ft_fields');
		Schema::dropIfExists('ft_values');
	}

}
