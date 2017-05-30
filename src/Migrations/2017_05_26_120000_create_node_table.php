<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateNodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ft_node', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->integer('area_fk')->unsigned();
            $table->tinyInteger('locked')->nullable();
            $table->tinyInteger('area');
            $table->tinyInteger('parent');
            $table->tinyInteger('order')->default('99');
            $table->tinyInteger('exclude');
            $table->tinyInteger('publish');
            $table->tinyInteger('soft_publish');
            $table->tinyInteger('homepage')->default('0');
            $table->string('tag');
            $table->string('url_slug');
            $table->text('title');
            $table->string('dscpn');
            $table->string('keywords');
            $table->text('body');
            $table->string('image')->nullable();
            $table->string('redirect')->nullable();
            $table->string('meta_title');
            $table->timestamp('publish_on')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('dscpn');
            $table->index('keywords');
        });

        Schema::create('ft_node_roles', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('can_edit')->unsigned();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
        Schema::dropIfExists('ft_node');
    }

}
