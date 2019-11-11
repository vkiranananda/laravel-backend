<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('disk');
            $table->string('path')->default('');
            $table->string('url')->default('');
            $table->string('file')->default('');
            $table->text('orig_name')->nullable();
            $table->text('sizes')->nullable();
            $table->integer('type')->default(1, 1);
            $table->string('file_type')->default('');
            $table->text('array_data')->nullable();
            //Id записи куда прикреплен
            $table->integer('imageable_id')->unsigned()->nullable();
            //Название модели для связи
            $table->string('imageable_type')->nullable();
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('media_files');
    }
}
