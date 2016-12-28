<?php

namespace App\Http\Controllers;

use App\Events\LikesEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        //var_dump($request->all());
        //Event::fire(new LikesEvent());
        return view('web.user.index');
    }

    public function test()
    {
        return view('web.user.index');
    }
}
