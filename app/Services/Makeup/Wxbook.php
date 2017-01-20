<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/7
 * Time: 14:28
 */

namespace App\Services\Makeup;

use App\Images;
use Illuminate\Support\Facades\Storage;

class Wxbook extends Base implements MakeupInterface
{
    private $text_width = 15;
    private $text_height = 15;
    private $englis_width = 10;
    public function figure($image_list)
    {
//        $type_space = imagettfbbox(14, 0, public_path('font/arial.ttf'),"你好的呢m");
//        $height = $type_space[3] - $type_space[5];
//        $width = $type_space[2] - $type_space[0];
//        var_dump($width);exit;
//
//        exit;
        // TODO: Implement todo() method.
        $info = $this->getDataByDate();
        $real_size = $this->realSize();
        $page = 1;
        $overinfo = $real_size;
        $arr_ret = [];
        $ret_info = [];
        foreach($info as $yk=>$yv){ //循环年
            foreach($yv as $mk=>$mv){ //循环月
                foreach($mv as $k=>$v){
                    if(empty($v['mediaList'])) {
                        $arr_ret = $this->pbNoImage($v, $overinfo, $page);
                        $overinfo = $arr_ret['overinfo'];
                        //$overinfo[1] = $overinfo[1]; //每条隔10
                        $page = $arr_ret['page'];

                    }else{
                        $arr_ret = [];
                    }

                    $ret_info[] = $arr_ret;
                }
                $page++;
                $overinfo = $real_size;
            }
            $page ++;
            $overinfo = $real_size;
        }
        echo '<pre>';
        print_r($ret_info);
        exit;
    }

    private function pbNoImage($info, $overinfo,$page){

        $real_size = $this->realSize();
        if(empty($info['content'])){
            //var_dump($overheight);exit;
            $overheight = $overinfo[1] - $this->text_height;
            $overwidth = $overinfo[0];
            $arr_new_text = [];
        }else{
            //估算文字所占高及换行情况
            $line_num = intval($overinfo[0]/$this->text_width);
            //var_dump($line_num);exit;
            $top = ($real_size[1] - $overinfo[1]) + $real_size[2];
            $left = ($real_size[0] - $overinfo[0]) + $real_size[3];

            $arr_text = $this->mb_str_split($info['content'],$line_num);

            $arr_new_text = [];
            $use_height = 0;
            $self_height = 0;
            $flag = true; //本页
//            echo '<pre>';
//            echo '<pre>';
//            print_r($arr_text);
            foreach($arr_text as $k=>$v){
                $arr_new_text[$k]['title'] = $v;
                $arr_new_text[$k]['y'] = $top;
                $arr_new_text[$k]['x'] = $left;
                $arr_new_text[$k]['page'] = $page;
                $top = $top + $this->text_height;
                $self_height = $self_height + $this->text_height;

                if($self_height>=$overinfo[1]){
                    $page++;
                    $overinfo = [$real_size[0], $real_size[1]];
                    $top = $real_size[2];
                    $left = $real_size[3];
                    $self_height = 0;
                    $flag = false;
                }
            }
            $count = count($arr_text);


            $overinfo = [$overinfo[0],$overinfo[1]-$self_height];


            //预估高度
//            $excpt_height = $count * $this->text_height;
//
//            if($excpt_height > $overinfo[1]){
//               if($count > 30){
//                   //页数不变，到底自定翻页
//
//               }
//            }


            //文字超过了最大高
//            if($overinfo[1]+$self_height > $real_size[1]){
//                $page++;
//                $overinfo = [$overinfo[0],$overinfo[1]+$self_height];
//            }
//            else{
//                $overinfo = [$overinfo[0],$overinfo[1]+$self_height];
//            }

            //var_dump($arr_new_text);
            //$text_length = count($arr_text);

            $arr_ret['text'] = $arr_new_text;
            $arr_ret['overinfo'] = $overinfo;
            $arr_ret['page'] = $page;
            return $arr_ret;

        }

        return $info;
    }

    /**
     * @param array $image_list
     * @param string $width
     * @param string $height
     */
    public function waterfall($image_list = [], $width ='', $height='')
    {
        $info = $this->getData();
    }

    /**
     * @return array
     */
    private function realSize(){
        $config = $this->getConf();
        $real_width = $config['bj_width'] - (($config['padding'][1] + $config['padding'][3])/100)*$config['bj_width'];
        $real_height = $config['bj_height'] - (($config['padding'][0] + $config['padding'][2])/100)*$config['bj_height'];
        $this->real_size = [$real_width, $real_height];
        $padding_top = (($config['padding'][1])/100)*$config['bj_width'];
        $padding_left = (($config['padding'][0])/100)*$config['bj_height'];
        return [$real_width, $real_height, $padding_top, $padding_left];
    }

    /**
     * @return array
     */
    public function getData(){
        $origin=str_replace(array("\\n","\\r"),"",Storage::get('data/sns.json'));
        $str = stripslashes($origin);

        $images = $this->getImageInfo();
        $arr_origih = array_column($images, 'origin_url');
        $arr_local = array_column($images, 'local_path');

        $new_str = str_replace($arr_origih, $arr_local, $str);

        $info = json_decode($new_str,true);
//        echo '<pre>';
//        var_dump($info);exit;
        $arr_timestamp = array_column($info, 'timestamp');
        array_multisort($info,SORT_ASC,$arr_timestamp);
        $image_text = [];
        foreach($info as $k=>$v){
            if($v['contentType'] == 3){
                $y = date('Y',$v['timestamp']);
                $m = date('m', $v['timestamp']);

                $image_text[$y][$m][] = $v;

            }
        }
        //sort($img_scales);


        //var_dump($new_str);exit;

        return $image_text;
    }

    /**
     * @return array
     */
    public function getDataByDate(){
        $data = $this->getData();

        return $data;

    }

    /**
     * @return mixed
     */
    public function getImageInfo(){
        return Images::where('status',1)->get()->toArray();
    }
}