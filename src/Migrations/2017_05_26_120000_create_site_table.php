<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSiteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ft_site', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('domain')->unique();
            $table->string('title');
            $table->string('dscpn');
            $table->string('keywords');
            $table->string('theme');
            $table->string('company');
            $table->string('person');
            $table->string('email');
            $table->string('street');
            $table->string('town');
            $table->string('postcode');
            $table->string('tel');
            $table->string('region');
            $table->string('tag');
            $table->string('key');
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
        Schema::dropIfExists('ft_site');
	}

}
