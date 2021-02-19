<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskDetailInfoController extends Controller
{
    public function getEmployeeWeekPlan(Request $request)
    {
        $from = Carbon::parse($request->date)->startOfWeek()->format('Y-m-d');
        $to = Carbon::parse($request->date)->endOfWeek()->format('Y-m-d');

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
            ->where('status', '<>', 'Done')
            ->where('status', '<>', 'Backlog')
            ->where('issue_type', '<>', 'Story')
            ->where('issue_type', '<>', 'Epic')
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
//            ->select('status', 'task_p_id_nr', 'issue_type')
            ->where('status', '<>', 'Selected for Development')
            ->where('issue_type', '<>', 'Story')
            ->get();

        //return '{tasks:' . $tasks . ', sumtime:' . $sumtime . '}';
        return new JsonResponse([
            'taskall'        => rand(0, 100),
            'backlog'        => rand(0, 100),
            'tasksqa'        => rand(0, 100),
            'taskscangolive' => rand(0, 100),
            'tasksdone'      => rand(0, 100),
            'sumtime'        => rand(0, 100)
        ]);
    }

}
