<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskDetailInfoController extends Controller
{
    public function getEmployeeWeekPlan(Request $request)
    {
        $year = 2021;
        $kalenderwoche = 2;

        $timestamp_montag = date('Y-m-d', strtotime('monday this week'));
        $timestamp_freitag = date('Y-m-d', strtotime('friday this week'));

        $from = $timestamp_montag;
        $to = $timestamp_freitag;

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

}
