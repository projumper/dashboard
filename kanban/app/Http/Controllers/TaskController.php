<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function addTask(Request $request)
    {
        //dd($request->text);
        //

        if($p_id_nr = $request->key)
        {
            $task = new Task();

            $task->description = $p_id_nr;
            $task->json = $request;

            $task->saveOrFail();
        }else{
            return false;
        }

    }

    public function getAll()
    {
        return Task::all();
    }
}
