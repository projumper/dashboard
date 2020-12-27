<?php

namespace App\Http\Controllers;

use App\Models\Employee_hour;
//use http\Client\Response;
use http\Env\Response;
use Illuminate\Http\Request;
use DateTime;

class EmployeeHourController extends Controller
{
    public function addTime(Request $request)
    {

        $worklogs = json_decode($request->worklogs);

        foreach ($worklogs->worklogs as $worklog) {

            Employee_hour::where('id_jira','=',$worklog->id)->delete();

            $employeetime = new Employee_hour();
            $employeetime->task_p_id_nr = $request->key;
            $employeetime->display_name = $worklog->author->displayName;
            $employeetime->id_jira = $worklog->id;
            $employeetime->account_id_jira = $worklog->author->accountId;
            $employeetime->created = date('Y-m-d', strtotime($worklog->created));
            $employeetime->updated = date('Y-m-d', strtotime($worklog->updated));
            $employeetime->started = date('Y-m-d', strtotime($worklog->started));
            $employeetime->timespendseconds = $worklog->timeSpentSeconds;

            $employeetime->save();

        }
    }

    public function getTime(Request $request){

        if(!$request->user)
            return (['status'=>'failed', 'message'=>'missed userId']);

        $time = Employee_hour::where('account_id_jira', '557058:e33f889f-36f5-476b-a1a7-f21bb2c74915')->get();

        if($request->date){
            //$time->
        }

        return ($time);
    }
}
