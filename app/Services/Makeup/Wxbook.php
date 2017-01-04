<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3
 * Time: 9:25
 */

namespace App\Services\Makeup;

use Illuminate\Support\Facades\Storage;

class Wxbook extends Base implements MakeupInterface
{
    public function figure($image_list)
    {
        // TODO: Implement todo() method.
        echo 'todo';
        exit;
    }

    public function waterfall($image_list)
    {
        // TODO: Implement waterfall() method.
    }

    /**
     * 测试用
     */
    public function getData(){
        $str = Storage::get('data/sns.json');
        $info = json_decode($str, true);

//        echo '<pre>';
//        print_r($info);exit;
//        echo '<pre>';
//        var_dump($info);exit;
        //数据详情
        $wx_info = [];
        foreach($info as $k=>$v){
            $time = $v['timestamp'];
            $y = date('Y',$time);
            $m = date('m',$time);
            $d = date('d',$time);
            $t = date('H:i',$time);
            if(!empty($v['mediaList']) || $v['contentType'] ==0){
                foreach($v['mediaList'] as $mk=>$mv){
                    $wx_info[$k][$mk]['img_path'] = $mv['url'];
                    $wx_info[$k][$mk]['img_width'] = $mv['width'];
                    $wx_info[$k][$mk]['img_height'] = $mv['height'];
                    $wx_info[$k][$mk]['img_scale'] = $mv['width']/$mv['height'];
                    $wx_info[$k]['title'] = $v['content'];




                    $wx_info[$k][$mk]['time']['y'] = $y;
                    $wx_info[$k][$mk]['time']['m'] = $m;
                    $wx_info[$k][$mk]['time']['d'] = $d;
                    $wx_info[$k][$mk]['time']['t'] = $t;
                }
            }else{
                $wx_info[$k][0]['img_path'] = '';
                $wx_info[$k][0]['img_width'] = 0;
                $wx_info[$k][0]['img_height'] = 0;
                $wx_info[$k][0]['img_scale'] = 1;
                $wx_info[$k][0]['time']['y'] = $y;
                $wx_info[$k][0]['time']['m'] = $m;
                $wx_info[$k][0]['time']['d'] = $d;
                $wx_info[$k][0]['time']['t'] = $t;
                $wx_info[$k]['title'] = $v['content'];
            }
        }
        echo '<pre>';
        print_r($wx_info);exit;
    }
}