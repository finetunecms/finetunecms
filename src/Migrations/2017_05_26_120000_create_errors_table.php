<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateErrorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ft_node_errors', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('node_id');
            $table->integer('error');
            $table->string('link');
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
        Schema::dropIfExists('ft_node_errors');
    }

}
