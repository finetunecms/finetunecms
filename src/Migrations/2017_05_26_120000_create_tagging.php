<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTagging extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('ft_tags', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->string('title');
            $table->string('tag');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('ft_node_tags', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->integer('tag_id')->unsigned();
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
        Schema::dropIfExists('ft_tags');
        Schema::dropIfExists('ft_node_tags');

    }

}
