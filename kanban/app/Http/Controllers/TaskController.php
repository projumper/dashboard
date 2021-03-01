<?php

namespace App\Http\Controllers;

use App\Models\Employee_hour;
use App\Models\Task;
use App\Models\TaskDetailInfo;
use App\Models\Worklog;
use Faker\Provider\DateTime;
use http\Env\Response;
use http\Exception\BadQueryStringException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;
use Illuminate\Support\Facades\Route;


class TaskController extends Controller
{
    public function addTask(Request $request)
    {

        if ($p_id_nr = $request->key) {

            $task = new Task();

            if(Task::find($p_id_nr)){
                return response()->json(['status' => 'OK']);
            }

            $task->p_id_nr = $p_id_nr;

            $json = $task->json = $request->getContent();

            $json = json_decode($json);

            //dd($json->fields->description);

            if (isset($json->fields->summary))
                $task->description = $json->fields->summary;

            if (isset($json->fields->duedate))
                $task->deadline = $json->fields->duedate;


            if ($task->saveorFail()) {

                $task = Task::find($p_id_nr);

                $detail = new TaskDetailInfo();

                $worklog = new Worklog();

                //$employeetime = new Employee_hour();

                $detail->p_id_nr = $p_id_nr;

                $worklog->p_id_nr = $p_id_nr;

                //$employeetime->p_id_nr = $p_id_nr;

                $task->detail()->save($detail);

                $task->worklog()->save($worklog);

                \Log::info('Task added');

                //$task->employeetime()->save($employeetime);

                return response()->json(['status' => 'OK']);
            } else {
                return response()->json(['status' => '$task->saveOrFail()']);
            }

        } else {

            return response()->json(['status' => '$p_id_nr = $request->key']);
        }

    }

    public function editTask(Request $request)
    {
        //return true;
        $p_id_nr = $request->key;

        //dd($p_id_nr);

        \Log::info('EDIT-TASK with p_id_nr='.$p_id_nr);

        if(!isset($request->key))
            \Log::info($request->errorMessages);

        if (isset($request->key)) {

            \Log::info('Start editing with p_id_nr='.$p_id_nr);

            $task = Task::find($p_id_nr);

            if (!isset($task->p_id_nr)) {

                \Log::info('Task is not in tasks table p_id_nr='.$p_id_nr);

                $request1 = Request::create(route('add'), 'POST');

                $request1->json(route('add'), $request->getContent());

                Route::dispatch($request1);
            }

            $task = Task::find($p_id_nr);

            $task->json = $request->getContent();

            $payload = json_decode($task->json);

            $task->description = $payload->fields->summary;

            try {
                $task->saveOrFail();

                $detail = new TaskDetailInfo();

                \Log::info('hier');

                if(isset($payload->fields->customfield_11008->accountId)){

                    $tester_code  =  $payload->fields->customfield_11008->accountId;

                }else{
                    $tester_code = '';
                }

                \Log::info(strtotime($payload->fields->created));

                if(isset($payload->fields->timetracking->timeSpentSeconds)) {
                    $timespendseconds = floatval($payload->fields->timetracking->timeSpentSeconds);
                }else{
                    \Log::info($p_id_nr. ' has no timespendseconds');
                    $timespendseconds = floatval(0);
                }

                if(isset($payload->fields->assignee->accountId)){
                    $aaccountId = $payload->fields->assignee->accountId;
                }else{
                    \Log::info($p_id_nr. ' has no Asignee');
                    $aaccountId = '';
                }

                if(isset($payload->fields->creator->accountId)){
                    $creator_id = $payload->fields->creator->accountId;
                }else{
                    $creator_id = '';
                }

                if(isset($payload->fields->customfield_11010)){
                    $start_date = $payload->fields->customfield_11010;
                }else{
                    $start_date = '1981-12-12';
                }

                if($payload->fields->customfield_10206 == ''){
                    $indeed_deadline = $payload->fields->duedate;
                }else{
                    $indeed_deadline = $payload->fields->customfield_10206;
                }


                \Log::info('createdate_jira '. date('Y-m-d', strtotime($payload->fields->created)));
                \Log::info('task_link ' . 'https://zentralweb.atlassian.net/browse/'.$p_id_nr);
                \Log::info('dealine '. $payload->fields->duedate);
                \Log::info('short_description '. $payload->fields->summary);
                \Log::info('estimated_time '. floatval($payload->fields->aggregatetimeoriginalestimate));
                \Log::info('total_time '. $timespendseconds);
                \Log::info('pm_employee_code '.  $creator_id);
                \Log::info('employee_code '. $aaccountId);
                \Log::info('pm_employee_time_total ' . 0.0);
                \Log::info('employee_time_total ' . 0.0);
                \Log::info('tester_code '. $tester_code);
                \Log::info('author_code '.$creator_id);
                \Log::info('status ' .$payload->fields->status->name);
                \Log::info('indeed_deadline '. $indeed_deadline);  //date('Y-m-d', strtotime($payload->fields->customfield_10206)));
                \Log::info('p_id '. $payload->fields->project->key);
                //fields►issuetype►name
                \Log::info('kva_id_paid '. '');
                \Log::info('customer_task_raiting '. '');
                \Log::info('start_date '. $start_date);

                try {
                $task->detail()->update([
                    'createdate_jira' => date('Y-m-d', strtotime($payload->fields->created)),
                    'task_link' => 'https://zentralweb.atlassian.net/browse/'.$p_id_nr,
                    'dealine' => $payload->fields->duedate,
                    'short_description' => $payload->fields->summary,
                    'estimated_time' => floatval($payload->fields->aggregatetimeoriginalestimate),
                    'total_time' => $timespendseconds,
                    'pm_employee_code' =>  $creator_id,
                    'employee_code' => $aaccountId,
                    'pm_employee_time_total' => 0.0,
                    'employee_time_total' => 0.0,
                    'tester_code' => $tester_code,
                    'author_code' =>$creator_id,
                    'status' => $payload->fields->status->name,
                    'indeed_deadline' => $indeed_deadline, //date('Y-m-d', strtotime($payload->fields->customfield_10206)),
                    'p_id' => $payload->fields->project->key,
                    'issue_type' => $payload->fields->issuetype->name,
                    'kva_id_paid' => '',
                    'customer_task_raiting' => '',
                    'start_date' => $start_date
                ]);
                } catch (\Exception $exception){
                    \Log::info( '---------------> Fuck Error in update JSON <----------------------');
                    \Log::info( $exception->getMessage());
                    return response()->json(['status' => 'Kacke in den Task Details']);
                }

                \Log::info('get worklog p_id_nr='.$p_id_nr);

                $worklog = new Worklog();

                //TODO when worklog is empty
                \Log::info('get worklog p_id_nr='.$p_id_nr);

                $request2 = Request::create('api/v1/getworklog/key/'.$p_id_nr, 'GET');

                $request2->json('api/v1/getworklog/key/'.$p_id_nr);

                $response2 = Route::dispatch($request2);

                $worklogData = $response2->getContent();

                \Log::info('Here worklog from '.$p_id_nr.' Payload: '.$worklogData);

                $worklogDataObj = json_decode($worklogData);

                if(count($worklogDataObj->worklogs)>0) {

                    $task->worklog()->update([
                        'worklog_json' => $worklogData,
                    ]);

                    \Log::info('add time');
                    $request3 = Request::create(route('addtime'), 'POST');

                    $request->request->add(['worklogs' => $worklogData]);

                    Route::dispatch($request3);
                }

                return response()->json(['status' => 'OK']);

            } catch (\Exception $e) {
                \Log::Info('--------------->Fuck Error <-----------------');
                \Log::Info(($e->getMessage()));
                return response()->json(['status' => 'Kacke in add Time']);
            }

        }else {

            return response()->json(['status' => 'something went wrong in Taskkontroller edit Task first IF']);
        }
    }

    public function getTasksDate(Request $request)
    {
        $dateArray = date_parse($request->date);

        if($dateArray['day'] == '00' && $dateArray['month'] == '00')
        {
            $to =   date('Y-m-d', mktime(0, 0, 0, 12, 31, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, 1, 1, $dateArray['year']));

        }elseif($dateArray['day'] == '00'){

            $lastDay = date('t', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));
            $to =   date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $lastDay, $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], 1, $dateArray['year']));

        }else {

            $to =   date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $dateArray['day'], $dateArray['year']));
            $from = date('Y-m-d', mktime(0, 0, 0, $dateArray['month'], $dateArray['day'], $dateArray['year']));

        }

        $time = DB::table('task_detail_infos')
            ->select('task_p_id_nr','task_link','dealine', 'short_description', 'estimated_time','total_time', 'pm_employee_code', 'pm_employee_time_total', 'employee_time_total', 'tester_code', 'author_code', 'status','indeed_deadline', 'p_id', 'kva_id_paid', 'customer_task_raiting')
            ->whereBetween('dealine', [$from, $to])
            //->orWhere('status', 'Backlog')
            ->get();

        return json_decode($time);

    }

    public function getWorklog(Request $request)
    {
        $p_id_nr = $request->key;

        if ($p_id_nr) {

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => env('JIRA_AUTHORIZATION', false)
            ])->get(env('JIRA_URL', false) . $p_id_nr . '/worklog');

            return $response->json();
        }

    }


    public function getTaskData(Request $request)
    {
        $p_id_nr = $request->key;

        if ($p_id_nr) {

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => env('JIRA_AUTHORIZATION', false)
            ])->get(env('JIRA_URL', false) . $p_id_nr );     //rest/api/2/issue/{issueIdOrKey}

            return $response->json();
        }

    }

    public function deleteTaskData(Request $request){
        $p_id_nr = $request->key;

        if ($p_id_nr) {

            try {
                $task = Task::where('p_id_nr', $p_id_nr)->delete();
            }catch (\Exception $exception)
            {
                $exception->getMessage();
            }

            return response()->json(['status' => 'Ok']);

        }
    }
}
