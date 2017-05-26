<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Blocks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        Schema::create('ft_blocks', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->string('name');
            $table->text('content');
            $table->integer('image');
            $table->string('title');
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
		Schema::dropIfExists('ft_blocks');
	}

}
