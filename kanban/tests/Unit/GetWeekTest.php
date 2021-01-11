<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;


class GetWeekTest extends TestCase
{

    public function get_this_week_test()
    {

        $this->getJson('/getthisweek')
            ->assertStatus(200)
            ->assertJson(['status' => 'OK']);
    }
}
