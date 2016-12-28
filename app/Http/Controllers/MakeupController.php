<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Makeup\Photo;
use Illuminate\Support\Facades\Config;

class MakeupController extends Controller
{
    //
    public function index(Request $request){

        $w_h = empty($request->input('w_h'))?1:$request->input('w_h');
        $is_use_temp = empty($request->input('is_use_temp'))?0:$request->input('is_use_temp');
        $config = Config::get('temp');
        $photo= new Photo();
        $photo->setConf($config);
        $image_list = $photo->getImageList('./images/makeup');
        $ret = array_slice($image_list, 0,2);
        //var_dump($ret);exit;
        $zz =0;
        $start = 0;
        //var_dump($rand);exit;
        //echo '<pre>';
        $ret_data = [];
        $page = 1;
        while(count($image_list) > $start){
        	 $rand = rand(1, 4);
        	 $images = array_slice($image_list, $start, $rand);
			 $data = $photo->todo($images, $page, $w_h,$is_use_temp);
             $start += $rand;
             $ret_data[] = $data;
			 //var_dump($data);
        	// print_r($images);
            $page ++;

        }
       // echo '<pre>';
       // print_r($ret_data);
       // exit;
        //$photo->getDetail($photo->todo($image_list[0]));
        if($request->ajax()){
            return response(json_encode(['data'=>$ret_data,'wh'=>$w_h]));
        }else{
            return view('web.makeup.display', ['data'=>$ret_data, 'w_h'=>$w_h]);
        }

    }

    public function makeupNew(){
        return view('web.makeup.display_new');
    }

    public function test(){
        return view('web.makeup.test');
    }
}
