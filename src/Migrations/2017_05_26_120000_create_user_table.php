<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the users table
		Schema::create('ft_users', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->string('username')->unique();
            $table->string('password');
			$table->string('email')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('remember_token')->nullable();
            $table->timestamps();
			$table->softDeletes();
        });

        Schema::create('ft_assigned_sites', function(Blueprint $table)
        {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('site_id')->unsigned();
            $table->timestamps();
        });

		// Create the password reminder table
        Schema::create('ft_password_reminders', function(Blueprint $table)
		{
			$table->string('email');
			$table->string('token');
			$table->timestamps();
			$table->softDeletes();
		});

        // Create the bruteforce log table
        Schema::create('ft_failedlogins',  function(Blueprint $table)
        {
           $table->increments('id')->unsigned();
           $table->string('ip');
           $table->integer('failed_logins');
           $table->smallInteger('locked_out');
           $table->dateTime('expire_time');
           $table->dateTime('last_attempt');
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
		Schema::drop('ft_users');
		Schema::drop('ft_password_reminders');
		Schema::drop('ft_failedlogins');
	}

}