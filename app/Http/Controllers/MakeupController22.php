<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Makeup\Photo;
use Illuminate\Support\Facades\Config;

class MakeupController extends Controller
{
    //
    public function index(Request $request){

        $config = Config::get('temp');
        $photo= new Photo();
        $photo->setConf($config);
        $image_list = $photo->getImageList('./images/makeup');

        $photo->getDetail($photo->todo($image_list[0]));
        return view('web.makeup.display');
    }
}
