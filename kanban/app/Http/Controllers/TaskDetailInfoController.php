<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskDetailInfoController extends Controller
{
    public function getEmployeeWeekPlan(Request $request){

        $year = 2021;
        $kalenderwoche = 2;

        $timestamp_montag = date('Y-m-d',strtotime('last monday'));
        $timestamp_freitag = date('Y-m-d',strtotime('next friday'));

        $from = $timestamp_montag;
        $to = $timestamp_freitag;

        $times = DB::table('task_detail_infos')
            ->select('*')
            ->whereBetween('start_date', [$from, $to])
            ->get();

        return json_decode($times);
    }
}
