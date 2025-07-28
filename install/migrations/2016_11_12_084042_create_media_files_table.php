<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('parent_id')->unsigned()->default(0);
            $table->string('disk', 100)->default('');
            $table->string('path', 1000)->default('');
            $table->string('file')->default('');
            $table->string('name')->nullable();
            $table->string('orig_name')->nullable();
            $table->text('sizes')->nullable();
            $table->string('type', 20)->default('');
            $table->string('extension', 10)->default('');
            $table->text('array_data')->nullable();
            $table->string('md5', 32)->default('');
            $table->string('key', 20)->default('');
            $table->integer('user_id')->unsigned();
            $table->integer('size')->unsigned()->default(0);
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
