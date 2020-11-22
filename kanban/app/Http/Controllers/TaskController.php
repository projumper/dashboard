<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function addTask(Request $request)
    {
        //dd($request->text);

        if($p_id_nr = $request->p_id_nr)
        {
            $task = new Task();

            $task->description = $p_id_nr;

            $task->saveOrFail();
        }

    }

    public function getAll()
    {
        return Task::all();
    }
}
