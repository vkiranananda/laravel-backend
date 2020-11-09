<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	if (!Schema::connection('frontend')->hasTable('user_roles')) {
	        Schema::connection('frontend')->create('user_roles', function (Blueprint $table) {
	            $table->increments('id');
	            $table->string('name')->nullable();
	            $table->text('array_data')->nullable();
                $table->integer('sort_num')->unsigned()->default(1000);
	        });
	    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('frontend')->dropIfExists('user_roles');
    }
}
