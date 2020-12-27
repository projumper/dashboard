<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorklogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worklogs', function (Blueprint $table) {
            //$table->id();

            $table->string('p_id_nr')->primary(); // HKD-123
            $table->string('task_p_id_nr')->unique();

            $table->longText('worklog_json')->nullable();

            $table->timestamps();

            $table->foreign('task_p_id_nr')->references('p_id_nr')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('worklogs');
    }
}
