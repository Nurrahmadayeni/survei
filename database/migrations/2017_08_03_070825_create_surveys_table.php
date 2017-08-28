<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('created_by', 20)->nullable();
            $table->string('unit', 10)->nullable();
            $table->text('title')->nullable();
            $table->integer('is_subject',false, true)->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('student', false, true)->nullable();
            $table->integer('lecture', false, true)->nullable();
            $table->integer('employee', false, true)->nullable();
            $table->string('academic_year',5)->nullable();
            $table->string('semester',5)->nullable();
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
        Schema::dropIfExists('surveys');
    }
}
