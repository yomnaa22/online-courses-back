<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('img');
            $table->integer('price');
            $table->integer('duration');
            $table->string('preq');
            $table->text('desc');


            $table->foreignId('trainer_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            // $table->unsignedBigInteger('trainer_id');
            // $table->foreign('trainer_id')->references('id')->on('trainers')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            // $table->unsignedBigInteger('category_id');
            // $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('courses');
    }
}
