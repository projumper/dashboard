<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskDetailInfoController extends Controller
{
    public function getEmployeeWeekPlan(Request $request)
    {

        //next week
        //this week
        //previrious week


        if ($request->week == 'this') {

            $timestamp_montag = date('Y-m-d', strtotime('monday this week'));
            $timestamp_freitag = date('Y-m-d', strtotime('next friday'));

            $from = $timestamp_montag;
            $to = $timestamp_freitag;
        }

        if ($request->week == 'next') {

        }

        if ($request->week == 'last') {

        }

        $timestamp_montag = date('Y-m-d', strtotime('monday this week'));
        $timestamp_freitag = date('Y-m-d', strtotime('next friday'));

        $from = $timestamp_montag;
        $to = $timestamp_freitag;


        //Todo if times is empty
        $times = DB::table('task_detail_infos')
            ->select('*')
            ->whereBetween('start_date', [$from, $to])
            ->get();

        return json_decode($times);
    }

    public function getOpenTasks(Request $request)
    {

        $today = date('Y-m-d', strtotime('now'));

        $times = DB::table('task_detail_infos')
//            ->select('status', 'task_p_id_nr', 'issue_type')
            ->where('start_date', '<', $today)
            ->where('status', '<>', 'Fertig')
            ->where('issue_type', '<>','Story')
            ->get();

        return json_decode($times);
    }

    public function getMonthData(Request $request)
    {
        //date 2021-01-00
        //status selecte for development, ...
        //label project, probono

        //return {tasks: 10, employeehours: 100h, }

        if ($request->date) {

            $dateArray = date_parse($request->date);

            $lastDay = date('t', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));
            $to = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $lastDay, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));

        }

        $sumtime = DB::table('employee_hours')
            ->select(DB::raw('sum(timespendseconds) as time'))
            ->whereBetween('updated', [$from, $to])
            ->get();

        $tasks = DB::table('task_detail_infos')
            ->select('status', 'task_p_id_nr', 'issue_type')
            ->where('status', '<>', 'Selected for Development')
            ->where('issue_type', '<>', 'Story')
            ->get();

        //return '{tasks:' . $tasks . ', sumtime:' . $sumtime . '}';
        return '{"tasksall:10", "tasksdone:100", "tasksqa:100", , "tasksinprogress:100", "backlog:100", "taskscangolive:100" "sumtime:100"}';

    }

}
