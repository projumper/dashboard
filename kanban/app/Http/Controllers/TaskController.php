<?php

namespace App\Http\Controllers;

use App\Models\Employee_hour;
use App\Models\Task;
use App\Models\TaskDetailInfo;
use App\Models\Worklog;
use Faker\Provider\DateTime;
use http\Env\Response;
use Illuminate\Http\Request;
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

            if (isset($json->fields->description))
                $task->description = $json->fields->description;

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

        if (isset($p_id_nr)) {

            \Log::info('Start editing with p_id_nr='.$p_id_nr);

            $task = Task::find($p_id_nr);

            if (!isset($task->p_id_nr)) {

                \Log::info('Task is not in tasks table p_id_nr='.$p_id_nr);

                $request1 = Request::create(route('add'), 'POST');

                $request1->json(route('add'), $request->getContent());

                $response = Route::dispatch($request1);
            }

            $task = Task::find($p_id_nr);

            $task->json = $request->getContent();

            $payload = json_decode($task->json);

            $task->description = $payload->fields->description;

            try {
                $task->saveOrFail();

                $detail = new TaskDetailInfo();

                $payload = json_decode($task->json);

                $task->detail()->update([
                    'createdate_jira' => date('Y-m-d', $payload->fields->created / 1000),
                    'task_link' => 'zentrra',
                    'dealine' => $payload->fields->duedate,
                    'short_description' => $payload->fields->description,
                    'estimated_time' => floatval($payload->fields->aggregatetimeoriginalestimate),
                    'total_time' => floatval($payload->fields->timetracking->timeSpentSeconds),
                    'pm_employee_code' => 0.0,
                    'employee_code' => '',
                    'pm_employee_time_total' => 0.0,
                    'employee_time_total' => 0.0,
                    'tester_code' => '',
                    'author_code' => '',
                    'status' => $payload->fields->status->name,
                    'indeed_deadline' => date('Y-m-d', $payload->fields->customfield_10206),
                    'p_id' => '',
                    'kva_id_paid' => '',
                    'customer_task_raiting' => '',
                ]);

                $worklog = new Worklog();

                //TODO when worklog is empty
                \Log::info('get worklog p_id_nr='.$p_id_nr);

                $request2 = Request::create('api/v1/getworklog/key/'.$p_id_nr, 'GET');

                $request2->json('api/v1/getworklog/key/'.$p_id_nr);

                $response2 = Route::dispatch($request2);

                $worklogData = $response2->getContent();

                \Log::info('Here worklog from '.$p_id_nr.' Payload: '.$worklogData);

                $task->worklog()->update([
                    'worklog_json' => $worklogData,
                ]);

                //add hours
                \Log::info('add time');
                $request3 = Request::create(route('addtime'), 'POST');

                $request->request->add(['worklogs'=>$worklogData]);

                $response3 = Route::dispatch($request3);

                return response()->json(['status' => 'OK']);

            } catch (\Exception $e) {
                echo($e->getMessage());
            }

        }else {

            return response()->json(['status' => 'something went wrong']);
        }
    }

    public function all()
    {
        return view('alltasks', ['tasks' => TaskDetailInfo::all()]);

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

    public function getTasksDate(Request $request){
        $date = $request->date;
        return view ('alltasks', ['tasksdate' => $request->date]);
    }
}
