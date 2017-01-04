<?php

namespace App\Http\Controllers;

use App\Events\LikesEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Makeup;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        //var_dump($request->all());
        //Event::fire(new LikesEvent());
        //Makeup::getType('photo')->todo([]);
        return view('web.user.index');
    }

    public function test()
    {
        return view('web.user.index');
    }
}
