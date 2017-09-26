<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DateSpanSetup extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ft_type', function (Blueprint $table) {
            $table->boolean('spanning_date')->nullable();
        });

        Schema::table('ft_node', function (Blueprint $table) {
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ft_type', function (Blueprint $table) {
            $table->dropColumn('spanning_date');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['start_at', 'end_at']);
        });
    }
}
