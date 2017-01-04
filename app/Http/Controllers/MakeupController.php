<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Services\Makeup\Photo;
use Illuminate\Support\Facades\Config;
//use App\Services\Makeup\MakeFacade;
use Makeup;
class MakeupController extends Controller
{
    //
    public function index(Request $request){

        $w_h = empty($request->input('w_h'))?1:$request->input('w_h');
        $is_use_temp = empty($request->input('is_use_temp'))?0:$request->input('is_use_temp');
        $temp_id = empty($request->input('temp_id'))?0:$request->input('temp_id');

        $config = Config::get('temp');
        //$photo= new Photo();
        $photo = Makeup::getType('photo');
        $photo->setConf($config);
        $image_list = $photo->getImageList('./images/makeup');
        $ret = array_slice($image_list, 0,2);
        //var_dump($ret);exit;
       // $zz =0;
        if($is_use_temp && !empty($temp_id)){
            $arr_nums = array_map(function($v){
                $ret = [];
                if(is_array($v)){
                    return count($v);
                }
               // return $ret;
            },$config['diy'][$temp_id]);

            unset($arr_nums['background']);
            $arr_nums = array_values($arr_nums);
            $start = 0;
            $ret_data = [];
            $page = 1;

            while(count($image_list) > $start && $page<count($arr_nums)){
                $rand = $arr_nums[$page-1];
                $images = array_slice($image_list, $start, $rand);
                $data = $photo->figure($images, $page, $w_h, $is_use_temp, $temp_id);
                $start += $rand;
                $ret_data[] = $data;
                $page ++;
            }


        }else{
            $start = 0;
            $ret_data = [];
            $page = 1;
            while(count($image_list) > $start){
                $rand = rand(1, 4);
                $images = array_slice($image_list, $start, $rand);
                $data = $photo->figure($images, $page, $w_h,$is_use_temp);
                $start += $rand;
                $ret_data[] = $data;
                $page ++;
            }
        }

        $bg = isset($config['diy'][$temp_id]['background']) ?$config['diy'][$temp_id]['background']:$config['standard']['background'];
        if($request->ajax()){
            return response(json_encode(['data'=>$ret_data,'wh'=>$w_h,'bg'=>$bg]));
        }else{
            return view('web.makeup.display', ['data'=>$ret_data, 'w_h'=>$w_h,'bg'=>$bg]);
        }

    }

    public function makeupNew(){
        return view('web.makeup.display_new');
    }

    public function waterfall(Request $request){
       // var_dump('waterfall');
        $config = Config::get('temp');
        $photo = Makeup::getType('photo');
        $photo->setConf($config);
        $image_list = $photo->getImageList('./images/makeup');

        $data = $photo->waterfall($image_list);
    }

    public function test(){
        return view('web.makeup.test');
    }
}
