<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('post_name')->nullable();
            $table->text('post_text')->nullable();
            $table->integer('category_id')->unsigned()->default(0);
            $table->text('array_data')->nullable();
            $table->string('mod', 30);
            $table->integer('sort_num')->unsigned()->default(1000);
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
        Schema::dropIfExists('categories');
    }
}
