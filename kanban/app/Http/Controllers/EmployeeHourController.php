<?php

namespace App\Http\Controllers;

use App\Models\Employee_hour;

//use http\Client\Response;
use http\Env\Response;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;


class EmployeeHourController extends Controller
{
    public function addTime(Request $request)
    {

        $worklogs = json_decode($request->worklogs);

        foreach ($worklogs->worklogs as $worklog) {

            Employee_hour::where('id_jira', '=', $worklog->id)->delete();

            $employeetime = new Employee_hour();
            $employeetime->task_p_id_nr = $request->key;
            $employeetime->display_name = $worklog->author->displayName;
            $employeetime->id_jira = $worklog->id;
            $employeetime->account_id_jira = $worklog->author->accountId;
            $employeetime->created = date('Y-m-d', strtotime($worklog->created));
            $employeetime->updated = date('Y-m-d', strtotime($worklog->updated));
            $employeetime->started = date('Y-m-d', strtotime($worklog->started));
            $employeetime->timespendseconds = $worklog->timeSpentSeconds;

            try {

                $employeetime->save();

            } catch (\Exception $exception) {
                \Log::info('Fuck Error');
                \Log::info($exception);
            }
        }
    }

    public function getTime(Request $request)
    {

        if (!$request->user) {
            return (['status' => 'failed', 'message' => 'missed userId']);
        }

        /*
        $date = '2020-12-12'; //day
        $date1 = '00-10-2020'; // month
        $date2 = '00-00-2020'; //year
        */


        $dateArray = date_parse($request->date);

        //dd($dateArray);

        if ($dateArray['day'] == '00' && $dateArray['month'] == '00') {
            $to = date('Y-m-d', mktime(0, 0, 0, 12, 31, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, 1, 1, $dateArray['year']));

        } elseif ($dateArray['day'] == '00') {

            $lastDay = date('t', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));
            $to = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $lastDay, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));


        } else {

            $to = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $dateArray['day'], $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $dateArray['day'], $dateArray['year']));

        }

        $time = DB::table('employee_hours')
            ->join('task_detail_infos', 'task_detail_infos.p_id_nr', '=', 'employee_hours.task_p_id_nr')
            ->select('task_detail_infos.status', 'task_detail_infos.task_link', 'display_name', 'created', 'updated', 'started', 'timespendseconds', 'dealine', 'issueid_jira', 'id_jira')
            ->where('account_id_jira', $request->user)
            ->whereBetween('created', [$from, $to])
            ->get();

        return json_decode($time);
    }

    public function getEmployeeTime(Request $request)
    {

        $dateArray = date_parse($request->date);

        if ($dateArray['day'] == '00' && $dateArray['month'] == '00') {
            $to = date('Y-m-d', mktime(0, 0, 0, 12, 31, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, 1, 1, $dateArray['year']));

        } elseif ($dateArray['day'] == '00') {

            $lastDay = date('t', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));
            $to = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $lastDay, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));


        } else {

            $to = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $dateArray['day'], $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $dateArray['day'], $dateArray['year']));

        }

        $time = DB::table('employee_hours')
            ->select(DB::raw('sum(timespendseconds) as time, account_id_jira, display_name'))

            ->whereBetween('updated', [$from, $to])
            ->groupBy('account_id_jira')
            ->groupBy('display_name')
            ->get();

        \Log::info($time);

        return json_decode($time);

    }


}
