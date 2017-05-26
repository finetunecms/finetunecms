<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMediaTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ft_media', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->string('filename');
            $table->string('extension');
            $table->string('path');
            $table->string('external');
            $table->string('thumb');
            $table->string('original');
            $table->string('title')->nullable();
            $table->string('mime');
            $table->string('type');
            $table->integer('height');
            $table->integer('width');
            $table->integer('parent');
            $table->integer('version');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('filename');
            $table->index('external');
            $table->index('thumb');
        });
        Schema::create('ft_media_folders', function(Blueprint $table){
            $table->increments('id');
            $table->integer('media_id')->unsigned();
            $table->integer('folders_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('ft_folders', function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->integer('site_id')->unsigned();
            $table->string('title');
            $table->string('tag');
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
        Schema::dropIfExists('ft_media');
        Schema::dropIfExists('ft_media_folders');
        Schema::dropIfExists('ft_folders');
	}

}
