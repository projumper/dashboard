<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_hours', function (Blueprint $table) {

            $table->id();

            $table->integer('issueid_jira')->nullable();

            $table->integer('id_jira');

            //$table->string('p_id_nr')->primary(); // HKD-123
            $table->string('task_p_id_nr');

            $table->string('account_id_jira')->nullable();

            $table->string('display_name')->nullable();

            $table->datetime('created')->nullable();

            $table->datetime('updated')->nullable();

            $table->datetime('started')->nullable();

            $table->integer('timespendseconds')->nullable();

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
        Schema::dropIfExists('employee_hours');
    }
}
