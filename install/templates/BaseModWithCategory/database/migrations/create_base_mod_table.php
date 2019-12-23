<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseModTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_mod', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            {url}$table->string('url')->nullable();
            $table->text('text')->nullable();
            {sort}$table->integer('sort_num')->unsigned()->default(1000);
            $table->integer('category_id')->unsigned()->default(0);
            $table->text('array_data')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('base_mod');
    }
}
