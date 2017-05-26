<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntrustSetupTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the roles table
        Schema::create('ft_roles', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('site_id')->default(0);
            $table->integer('parent_id')->default(0);
            $table->string('name')->unique();
            $table->integer('usable')->default(1);
            $table->string('deleteable')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Creates the assigned_roles (Many-to-Many relation) table
        Schema::create('ft_assigned_roles', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
        });

        // Creates the permissions table
        Schema::create('ft_permissions', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('display_name');
            $table->string('usable')->default(1);
            $table->string('deleteable')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Creates the permission_role (Many-to-Many relation) table
        Schema::create('ft_permission_role', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ft_assigned_roles');
        Schema::drop('ft_permission_role');
        Schema::drop('ft_roles');
        Schema::drop('ft_permissions');
    }

}
