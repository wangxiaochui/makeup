<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/20
 * Time: 8:42
 */

namespace App\Services\Makeup;


class Hcdiy extends Base
{
    private $temps;
    private $img_list;
    public function pack($img_list , $temps){
        $this->temps = $temps;
            $this->img_list = $img_list;
        //所需照片数
        $this->getImageNum($temps);
    }

    private function getImageNum($temps = null){
        $temp_datas = !empty($temps)?$temps:$this->temps;
       // echo '<pre>';
        $num = 0;
        foreach($temp_datas as $k=>$v){
            //print_r(json_decode($v->stage,true));
            $arr_stage = json_decode($v->stage,true);
            //var_dump(array_column($arr_stage , 'fromTag'));
            foreach($arr_stage as $ak=>$av){
                if($av['fromTag'] == 'photo'){
                    $num++;
                }
            }
        }
        var_dump($num);exit;
        return $num;
    }

    //测试用
    public function getImageList($path){
        $info_path =  $path;
        $fp = opendir($info_path);
        $arr_dir = [] ;
        $i = 0;
        while($file = readdir($fp)){
            if($file!="." && $file!=".."){
                if(!is_dir($info_path.'/'.$file)){
                    $arr_dir[$i]['path'] = 'http://'.$_SERVER['HTTP_HOST'].'/images/makeup/'.$file;
                    $img_info  =getimagesize($info_path.'/'.$file);
                    $img_width = $img_info[0];
                    $img_height = $img_info[1];
                    $arr_dir[$i]['img_width'] = $img_width;
                    $arr_dir[$i]['img_height'] = $img_height;
                    $arr_dir[$i]['img_scale'] = $img_width/$img_height;
                    $i ++;
                }
            }

        }
        return $arr_dir;
    }
}