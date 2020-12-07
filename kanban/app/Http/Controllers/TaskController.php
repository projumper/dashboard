<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskDetailInfo;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Mockery\Exception;

class TaskController extends Controller
{
    public function addTask(Request $request)
    {

        if ($p_id_nr = $request->key) {

            $task = new Task();

            $task->p_id_nr = $p_id_nr;

            #TODO
            $task->description = '';

            $task->json = $request->getContent();

            if ($task->saveorFail()) {

                $task = Task::find($p_id_nr);

                $detail = new TaskDetailInfo();

                $detail->p_id_nr = $p_id_nr;

                $task->detail()->save($detail);

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
        //dd(date('Y-m-d',1606072034951 / 1000));

        $p_id_nr = $request->key;

        //dd($p_id_nr);

        if ($p_id_nr) {

            $task = Task::find($p_id_nr);
            //dd($task);
            #TODO
            $task->description = '';

            $task->json = $request->getContent();

            try {
                $task->saveOrFail();

                $detail = new TaskDetailInfo();



                $payload = json_decode($task->json);




                $task->detail()->update([
                    'createdate_jira' => date('Y-m-d',$payload->fields->created/1000),
                    'task_link' => 'zentrra',
                    'dealine' => date('Y-m-d', $payload->fields->duedate),
                    'short_description' => '',
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

                return response()->json(['status' => 'OK']);

            } catch (Exception $e) {
                echo($e->getMessage());
            }

        }
        return response()->json(['status' => 'pi_id_nr is not set']);
    }

    public function all()
    {
        return view('alltasks', ['tasks'=>TaskDetailInfo::all()]);

    }
}
