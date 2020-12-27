<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'p_id_nr';

    public $incrementing = false;

    public function detail()
    {
        return $this->hasOne(TaskDetailInfo::class);
    }

    public function worklog(){
        return $this->hasOne(Worklog::class);
    }

    public function employeetime(){
        //return $this->hasOne(Employee_hour::class);
    }
}
