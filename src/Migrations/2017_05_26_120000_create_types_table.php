<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ft_type', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title');
            $table->string('outputs');
            $table->string('layout');
            $table->string('blocks')->nullable();
            $table->string('order_by')->nullable();
            $table->boolean('default_type')->default(0);
            $table->boolean('nesting')->default(0);
            $table->boolean('children')->default(0);
            $table->boolean('ordering')->default(0);
            $table->boolean('date')->default(0);
            $table->boolean('today_future')->default(0);
            $table->boolean('today_past')->default(0);
            $table->boolean('pagination')->default(0);
            $table->integer('pagination_limit')->default(0);
            $table->boolean('access')->default(0);
            $table->boolean('rss')->default(0);
            $table->boolean('live')->default(0);
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
        Schema::dropIfExists('ft_type');
    }

}
