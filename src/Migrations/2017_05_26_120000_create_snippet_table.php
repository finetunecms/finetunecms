<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSnippetTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ft_snippets', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->integer('link_type')->unsigned();    // This connects to the node table to get the link
            $table->integer('link_internal')->unsigned();    // This connects to the node table to get the link
            $table->string('link_external');  // This is for an external Link
            $table->tinyInteger('order');
            $table->tinyInteger('publish');
            $table->string('tag');
            $table->string('title');
            $table->text('body');
            $table->string('image');
            $table->timestamps();
            $table->softDeletes();

        });
        Schema::create('ft_snippet_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id');
            $table->string('tag');
            $table->string('title');
            $table->string('dscpn');
            $table->tinyInteger('publish');
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
        Schema::dropIfExists('ft_snippets');
        Schema::dropIfExists('ft_snippet_groups');
    }

}
