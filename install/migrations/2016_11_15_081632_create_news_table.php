<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->text('text')->nullable();
            // Если используется модуль категорий
            $table->integer('category_id')->unsigned()->default(0); 
            $table->text('array_data')->nullable();
            $table->string('status', 1)->default('0');
            $table->date('publication_date', 1)->nullable();
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
        Schema::dropIfExists('news');
    }
}
