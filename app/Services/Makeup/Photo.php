<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 11:08
 */

namespace App\Services\Makeup;

use Config;
use App\Services\Makeup\Type\Waterfall;

class Photo extends Base implements MakeupInterface
{
    private $is_use_temp;
    private $template_id;
    private $image_list;
    private $w_h;

    /**
     * @param int $pages 页数
     * @param bool $is_use_temp 是否使用模板
     * @param int $temp_id 模板id
     */
    public function figure($image_list ,$pages=1, $w_h = 1, $is_use_temp=false, $temp_id=0)
    {
        // TODO: Implement todo() method.
        $this->is_use_temp = $is_use_temp;
        $this->template_id = $temp_id;
        $this->image_list = $image_list;
        $this->page = $pages;
        $this->wh = $w_h;
        $ret = $this->packDo();
        return $ret;
    }

    /**
     * @param $image_list
     * @param int $page 页数
     * @param int $w_h 版面宽高
     * @param int $is_font 是否有文字
     */
    public function waterfall($image_list , $page =1, $w_h = 1, $is_font = 0)
    {
        // TODO: Implement waterfall() method.
        $this->image_list = $image_list;
        $this->page = $page;
        $this->is_font = $is_font;
        $this->wh = $w_h;
        $WaterFall = new Waterfall();
        $config = $this->getConf();
        $padding = $config['waterfall']['padding'];
        $WaterFall->makeData($image_list, $w_h, $is_font, $padding);
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

    //图片属性
    public function getDetail(){
        $detail = $this->getImageList('./images/makeup');
        return $detail;
    }

    private function packDo(){
        $count = count($this->image_list);
        switch ($count){
            case 1:
                $arr = $this->pbOnePage();
                break;
            case 2:
                $arr = $this->pbTwoPage();
                break;
            case 3:
                $arr = $this->pbThreePage();
                break;
            default:  $arr = $this->pbOnePage();
                break;
        }
        return $arr;
    }


    /**
     * one makeup
     */
    private function pbOnePage(){
        $image_list = $this->image_list;

        $config = $this->getConf();


        if($this->is_use_temp)
        {
            //var_dump($config['standard']);exit;
            //使用标准模板
            if(empty($this->template_id)){
                $data = $config['standard']['nunOne'];
            }else{
                $data = $config['diy'][$this->template_id][$this->page];
            }

            $data = $this->makeData($image_list, $data);
            return $data;
        }
        else
        {
            //一张图片的都用标准模版
            $data = $config['standard']['nunOne'];
           // $data = $this->makeData($image_list, $data);
            //var_dump($config['standard']['padding']);exit;
            $data = $this->commonOne($image_list[0], $config['standard']['padding']);
            //var_dump($data);
            return [$data];
        }

        return $data;

    }

    /**
     * two makeup
     */
    private function pbTwoPage($padding=[], $img_list = []){

        if(!empty($img_list)){
            $image_list = $img_list;
        }else{
            $image_list = $this->image_list;
        }


        if($padding){
            $config['standard']['padding'] = $padding;
        }else{
            $config = $this->getConf();
        }

        if($this->is_use_temp)
        {

            if(empty($this->template_id)){
                $data = $config['standard']['nunTwo'];
            }else{
                $data = $config['diy'][$this->template_id][$this->page];
            }
            $data = $this->makeData($image_list, $data);
            return $data;
        }
        else
        {
            //两张排法
            $img_scales = array_column($image_list, 'img_scale');
            //计算可编辑区域
            $edit_width = (100- $config['standard']['padding'][1]-$config['standard']['padding'][3]);
            $edit_height = (100- $config['standard']['padding'][0]-$config['standard']['padding'][2])/$this->wh;
            
            //可编辑区比例
            $edit_scale = $edit_width/$edit_height;

            //横排比例
            //var_dump($img_scales);
            $limit_over = 20; 
            $sum_x = array_sum($img_scales);
            $first = $img_scales[0];
            $second = $img_scales[1];
            //竖排比例
            $sum_y = ($first*$second)/($first+$second);
            $auto_pack = [];
            if(abs($edit_scale - $sum_x) < abs($edit_scale - $sum_y)){
            $first_scale = $first/($first+$second);
                $second_scale = $second/($first+$second);
                $first_width = $edit_width * $first_scale;
                $first_height = $edit_height;
                $second_width = $edit_width *$second_scale;

                $p_left = $config['standard']['padding'][3];
                $p_right = $config['standard']['padding'][1] +$second_width+1;
                $p_top = $config['standard']['padding'][0];
                $p_bottom = $config['standard']['padding'][2];
                $padding = [$p_top, $p_right, $p_bottom, $p_left];
                $ret_one = $this->commonOne($image_list[0],  $padding);

                $top = $config['standard']['padding'][0];
                $left = $config['standard']['padding'][3]+$ret_one['width']+1;
                $right = $config['standard']['padding'][1];
                $bottom = $config['standard']['padding'][2];
                $ret_two = $this->commonOne($image_list[1], [$top, $right, $bottom, $left]);

                return array_merge([$ret_one], [$ret_two]);
            }else{
                $first_scale = $second/($first+$second);//运算一下
                $second_scale = $first/($first+$second);

                $real_height =   (100- $config['standard']['padding'][0]-$config['standard']['padding'][2]);
                $first_height = $real_height * $first_scale;


                $second_height = $real_height *$second_scale;

                //第一张排法
                $p_left = $config['standard']['padding'][3];
                $p_right = $config['standard']['padding'][1];
                $p_top = $config['standard']['padding'][0];
                $p_bottom = $config['standard']['padding'][2] + $second_height;
                $padding = [$p_top, $p_right, $p_bottom, $p_left];
                $ret_one = $this->commonOne($image_list[0],  $padding);
                $top = $config['standard']['padding'][0] + $ret_one['height']+1;
                $left = $config['standard']['padding'][3];
                $right = $config['standard']['padding'][1];
                $bottom = $config['standard']['padding'][2];

                //$image_list = array_values($image_list);
                $ret_two = $this->commonOne( $image_list[1], [$top, $right, $bottom, $left]);
                return array_merge([$ret_one], [$ret_two]);
            }

        }
       // return $auto_pack;
    }
    /**
     * three makeup
     */
    private function pbThreePage(){
        $image_list = $this->image_list;
        $config = $this->getConf();
        if($this->is_use_temp)
        {


            if(empty($this->template_id)){
                $data = $config['standard']['nunThree'];
            }else{

                $data = $config['diy'][$this->template_id][$this->page];
            }
            $data = $this->makeData($image_list, $data);
            return $data;
        }
        else
        {
            //图片比例从小到大
            //三张张排法
            $img_scales = array_column($image_list, 'img_scale');
            //sort($img_scales);
            array_multisort($img_scales,SORT_ASC,$image_list);

            $first = $image_list[0]['img_scale'];
            $second = $image_list[1]['img_scale'];
            $third = $image_list[2]['img_scale'];

            $new_scale_x = $second + $first;

            $new_scale_y = ($second * $third)/($second + $third);

            $total_scale_w = $second + $first + $third;

            $total_scale_x = $first + $new_scale_y; //左右右 do1

            $total_scale_y = ($first*$new_scale_y)/($first+$new_scale_y);//上下下 do2


            $total_scale_z  = ($third*$new_scale_x)/($third+$new_scale_x);//上左右,最长的在上面 do3


            //计算可编辑区域
            $edit_width = (100 - $config['standard']['padding'][1]-$config['standard']['padding'][3]);
            $edit_height = (100 - $config['standard']['padding'][0]-$config['standard']['padding'][2])/$this->wh;

            //$height = 100; //整个画布高设为100
           // $edit_height =  ($height - $config['standard']['padding'][0]-$config['standard']['padding'][2]);
            //$edit_width = ($height*$this->wh - $config['standard']['padding'][1]-$config['standard']['padding'][3]);

            //可编辑区比例
            $edit_scale = $edit_width/$edit_height;
            $do1 = abs($total_scale_x - $edit_scale); //0
            $do2 = abs($total_scale_y - $edit_scale); //1
            $do3 = abs($total_scale_z - $edit_scale); //2
            $do4 = abs($total_scale_w - $edit_scale); //3

            //$min = min();
            $arr = [$do1, $do2, $do3, $do4];
            asort($arr);
            $sort_keys = array_keys($arr);

            $allow = $sort_keys[0];


            if($allow == 0){ //方式一，对应$total_scale_x
                $first_scale = $first/($first+$new_scale_y);
                $second_scale = $new_scale_y/($first+$new_scale_y);
                $first_width = $edit_width * $first_scale;
                $first_height = $edit_height;
                $second_width = $edit_width *$second_scale;

                //第一张排法
                $key = 0;
                $p_left = $config['standard']['padding'][3];
                $p_right = $config['standard']['padding'][1] +$second_width+1;
                $p_top = $config['standard']['padding'][0];
                $p_bottom = $config['standard']['padding'][2];
                $padding = [$p_top, $p_right, $p_bottom, $p_left];
                $ret_one = $this->commonOne($image_list[0],  $padding);
                $top = $config['standard']['padding'][0];
                $left = $ret_one['width']+$config['standard']['padding'][3]+1;
                $right = $config['standard']['padding'][1];
                $bottom = $config['standard']['padding'][2];
                unset($image_list[$key]);
                $image_list = array_values($image_list);
                $flag = $total_scale_x - $edit_scale;  //是多了还是少了
                $ret_two = $this->commonTwoSecond([$top, $right, $bottom, $left], $image_list, $flag);
                $data = array_merge([$ret_one], $ret_two);

            }elseif($allow == 1){//方式二，对应$total_scale_y
                $key = 1;
                $first_scale = $new_scale_x/($third+$new_scale_x);
                $second_scale = $third/($third+$new_scale_x);
                $first_height = $edit_height * $first_scale;

                $second_height = $edit_height *$second_scale;

                //第一张排法
                $p_left = $config['standard']['padding'][3];
                $p_right = $config['standard']['padding'][1];
                $p_top = $config['standard']['padding'][0];
                $p_bottom = $config['standard']['padding'][2] + $second_height;
                $padding = [$p_top, $p_right, $p_bottom, $p_left];
                $ret_one = $this->commonOne($image_list[$key],  $padding);
                $top = $config['standard']['padding'][0] + $ret_one['height']+1;
                $left = $config['standard']['padding'][3];
                $right = $config['standard']['padding'][1]+1;
                $bottom = $config['standard']['padding'][2];
                unset($image_list[$key]);
                $image_list = array_values($image_list);
                $ret_two = $this->commonTwoSecond( [$top, $right, $bottom, $left], $image_list);
                $data = array_merge([$ret_one], $ret_two);


            }elseif($allow == 2){//方式三，对应$total_scale_z
                $key = 2;
                $first_scale = $new_scale_x/($third+$new_scale_x);//运算一下
                $second_scale = $third/($third+$new_scale_x);
                $first_height = $edit_height * $first_scale;
            
                $second_height = $edit_height *$second_scale;

                //第一张排法
                $p_left = $config['standard']['padding'][3];
                $p_right = $config['standard']['padding'][1];
                $p_top = $config['standard']['padding'][0];
                $p_bottom = $config['standard']['padding'][2] + $second_height;
                $padding = [$p_top, $p_right, $p_bottom, $p_left];
                $ret_one = $this->commonOne($image_list[$key],  $padding);
                $top = $config['standard']['padding'][0] + $ret_one['height']+1;
                $left = $config['standard']['padding'][3];
                $right = $config['standard']['padding'][1]+1;
                $bottom = $config['standard']['padding'][2];
                unset($image_list[$key]);
                $image_list = array_values($image_list);
                $ret_two = $this->commonTwoFirst( [$top, $right, $bottom, $left], $image_list);
                $data = array_merge([$ret_one], $ret_two);
            }else{
                $key = 3;
                $first_scale = $first/($first+$second+$third);
                $second_scale = ($second+$third)/($first+$second+$third);
                $first_width = $edit_width * $first_scale;
                $first_height = $edit_height;
                $second_width = $edit_width *$second_scale;

                //第一张排法
                $key = 0;
                $p_left = $config['standard']['padding'][3];
                $p_right = $config['standard']['padding'][1] +$second_width+1;
                $p_top = $config['standard']['padding'][0];
                $p_bottom = $config['standard']['padding'][2];
                $padding = [$p_top, $p_right, $p_bottom, $p_left];
                $ret_one = $this->commonOne($image_list[0],  $padding);
                $top = $config['standard']['padding'][0];
                $left = $first_width+$config['standard']['padding'][3]+1;
                $right = $config['standard']['padding'][1];
                $bottom = $config['standard']['padding'][2];
                unset($image_list[$key]);
                $image_list = array_values($image_list);
                $ret_two = $this->commonTwoFirst([$top, $right, $bottom, $left], $image_list);
                $data = array_merge([$ret_one], $ret_two);

            }
        }
        return $data;
    }


}