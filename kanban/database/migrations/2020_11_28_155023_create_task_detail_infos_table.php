<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskDetailInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_detail_infos', function (Blueprint $table) {
            //$table->id();
            //$table->id('task_id');

            $table->string('p_id_nr')->primary(); // HKD-123
            $table->string('task_p_id_nr')->unique();

            $table->date('createdate_jira')->nullable();
            $table->string('task_link')->nullable();
            $table->date('dealine')->nullable();
            $table->string('short_description')->nullable();
            $table->float('estimated_time')->nullable();
            $table->float('total_time')->nullable();
            $table->string('pm_employee_code')->nullable();
            $table->string('employee_code')->nullable();
            $table->float('pm_employee_time_total')->nullable();
            $table->float('employee_time_total')->nullable();
            $table->string('tester_code')->nullable();
            $table->string('author_code')->nullable();
            $table->string('status')->nullable();
            $table->string('indeed_deadline')->nullable(); //end_date
            $table->string('p_id')->nullable(); //HKD
            $table->string('issue_type')->nullable();
            $table->string('task_parent')->nullable();

            $table->string('kva_id_paid')->nullable();
            $table->string('customer_task_raiting')->nullable();

            $table->date('start_date')->nullable();

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
        Schema::dropIfExists('task_detail_infos');
    }
}
