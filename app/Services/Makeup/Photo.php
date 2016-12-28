<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 11:08
 */

namespace App\Services\Makeup;


use Illuminate\Support\Facades\Config;

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
    public function todo($image_list ,$pages=1, $w_h = 1, $is_use_temp=false, $temp_id=0)
    {
        // TODO: Implement todo() method.
        $this->is_use_temp = $is_use_temp;
        $this->template_id = $temp_id;
        $this->image_list = $image_list;
        $this->wh = $w_h;
        $ret = $this->packDo();
        return $ret;
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

    //no temp
    public function packNoTemp(){
        
    }

    public function packTemp(){
        
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
    *rand temp
    *智能排版
    */
    private function autoTemp(){
        $image_list = $this->image_list;

    }

    /**
     * one makeup
     */
    private function pbOnePage(){
        $image_list = $this->image_list;

        $config = $this->getConf();

        $w_h = $this->w_h;
        //if(abs($w_h - $image_list['img_scale']) >$config['standard']['limit'])

        if($this->is_use_temp)
        {
            //var_dump($config['standard']);exit;
            //使用标准模板
            $data = $config['standard']['nunOne'];
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
    private function pbTwoPage($is_repeat = false, $padding=[], $img_list = []){

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
            $data = $config['standard']['nunTwo'];
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
//                echo '<pre>';
//                print_r($ret_one);
//                print_r($ret_two);exit;
                return array_merge([$ret_one], [$ret_two]);
            }


            //哪一种更合适
            /*if(abs($edit_scale - $sum_x) < abs($edit_scale - $sum_y) ||$is_repeat){
                //echo 1;
                //横排
                //高固定
                $i_height = $edit_height;
                //第一张相片宽
                $f_width = $i_height*$first;
                //第二张相片宽
                $s_width = $i_height*$second;

                //分摊切割比例
                $sy = ($f_width + $s_width) - $edit_width;

                $bl_f = $f_width/($s_width+$f_width);
                $bl_s = $s_width/($s_width+$f_width);
                //第一张相片显示宽

                $dis_width_f = $f_width - (($sy*$bl_f));
                //第二张显示宽
                $dis_width_s = $s_width - (($sy*$bl_s));
                // var_dump($dis_width_f);
                // var_dump($dis_width_s);
                $img_width_f = $edit_height* $image_list[0]['img_scale'];

                $real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
                $auto_pack[0]['path'] = $image_list[0]['path'];
                $auto_pack[0]['width'] = $dis_width_f;
                $auto_pack[0]['top'] = $config['standard']['padding'][0];
                $auto_pack[0]['left'] = $config['standard']['padding'][3];
                $auto_pack[0]['height'] = $real_height;
                $auto_pack[0]['relative_width'] = (($img_width_f/$dis_width_f)*100)/$this->wh;


                //如果对比长小于100
                if($auto_pack[0]['relative_width'] < 100){
                    //$real_relativ_w =
                    $auto_pack[0]['relative_height'] = (100/$auto_pack[0]['relative_width'])*100;
                    $auto_pack[0]['relative_width'] = 100;
                    $auto_pack[0]['relative_cut_left'] = 0;
                    $auto_pack[0]['relative_cut_top'] = ($auto_pack[0]['relative_height'] - 100)/2;
                }else{
                    $auto_pack[0]['relative_height'] = 100;
                    $auto_pack[0]['relative_cut_left'] = ($auto_pack[0]['relative_width'] - 100)/2;
                    $auto_pack[0]['relative_cut_top'] = 0;
                }


                $img_width_s = $edit_height* $image_list[1]['img_scale'];   
                //$real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
                $auto_pack[1]['path'] = $image_list[1]['path'];
                $auto_pack[1]['width'] = $dis_width_s;
                $auto_pack[1]['top'] = $config['standard']['padding'][0];
                $auto_pack[1]['left'] = $config['standard']['padding'][3]+$dis_width_f+1;
                $auto_pack[1]['height'] = $real_height;
                $auto_pack[1]['relative_width'] = (($img_width_s/$dis_width_s)*100)/$this->wh;

                //如果对比长小于0
                if($auto_pack[1]['relative_width'] < 100){

                    $auto_pack[1]['relative_height'] = (100/$auto_pack[1]['relative_width'])*100;
                    $auto_pack[1]['relative_width'] = 100;
                    $auto_pack[1]['relative_cut_left'] = 0;
                    $auto_pack[1]['relative_cut_top'] = ($auto_pack[1]['relative_height'] - 100)/2;
                }else{
                    $auto_pack[1]['relative_height'] = 100;
                    $auto_pack[1]['relative_cut_left'] = ($auto_pack[1]['relative_width'] - 100)/2;
                    $auto_pack[1]['relative_cut_top'] = 0;
                }


            }else{
                //echo 2;
                //竖排
                //echo 1;

                $i_width= $edit_width;
                //第一张相片高
                $f_height = $i_width/$first;
                //第二张相片高
                $s_height  = $i_width/$second;

                //分摊切割比例
                $sy = ($f_height + $s_height) - $edit_height;
                $bl_f = $f_height/($f_height+$s_height);
                $bl_s = $s_height/($f_height+$s_height);
                //第一张相片显示宽
                $dis_height_f = $f_height- (($sy*$bl_f));
                //第二张显示宽
                $dis_height_s = $s_height - (($sy*$bl_s));
                // var_dump($dis_width_f);
                // var_dump($dis_width_s);
                $img_height_f = $edit_width/ $image_list[0]['img_scale'];

                $real_width = 100- $config['standard']['padding'][1]-$config['standard']['padding'][3];
                $auto_pack[0]['path'] = $image_list[0]['path'];
                $auto_pack[0]['width'] =  $real_width;
                $auto_pack[0]['top'] = $config['standard']['padding'][0];
                $auto_pack[0]['left'] = $config['standard']['padding'][3];
                $auto_pack[0]['height'] = $dis_height_f;

                $auto_pack[0]['relative_height'] = (($img_height_f/$dis_height_f)*100)*$this->wh;

                //如果对比长小于100
                if($auto_pack[0]['relative_height'] < 100){

                    $auto_pack[0]['relative_width'] = (100/$auto_pack[0]['relative_height'])*100;
                    $auto_pack[0]['relative_height'] = 100;
                    $auto_pack[0]['relative_cut_top'] = 0;
                    $auto_pack[0]['relative_cut_left'] = ($auto_pack[0]['relative_width'] - 100)/2;
                }else{
                    $auto_pack[0]['relative_width'] = 100;
                    $auto_pack[0]['relative_cut_left'] = 0;
                    $auto_pack[0]['relative_cut_top'] = ($auto_pack[0]['relative_height'] - 100)/2;
                }


                $img_height_s = $edit_width/ $image_list[1]['img_scale'];   
                //$real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
                $auto_pack[1]['path'] = $image_list[1]['path'];
                $auto_pack[1]['width'] = $real_width;
                $auto_pack[1]['top'] = $config['standard']['padding'][0] +$dis_height_f +1;
                $auto_pack[1]['left'] = $config['standard']['padding'][3];
                $auto_pack[1]['height'] = $dis_height_s;
                //$auto_pack[1]['relative_width'] = 100;
                $auto_pack[1]['relative_height'] = (($img_height_s/$dis_height_s)*100)/$this->wh;

                //如果对比长小于100
                if($auto_pack[1]['relative_height'] < 100){

                    $auto_pack[1]['relative_width'] = (100/$auto_pack[1]['relative_height'])*100;
                    $auto_pack[1]['relative_height'] = 100;
                    $auto_pack[1]['relative_cut_top'] = 0;
                    $auto_pack[1]['relative_cut_left'] = ($auto_pack[1]['relative_width'] - 100)/2;
                }else{
                    $auto_pack[1]['relative_width'] = 100;
                    $auto_pack[1]['relative_cut_left'] = 0;
                    $auto_pack[1]['relative_cut_top'] = ($auto_pack[1]['relative_height'] - 100)/2;
                }

                //尽量不做大幅度头尾切
                if($auto_pack[0]['relative_cut_top']>30 ||$auto_pack[1]['relative_cut_top']>30){
                    return $this->pbTwoPage(true);
                }
               
            }*/
            
            
            
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
            $data = $config['standard']['nunThree'];
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
//        echo '<pre>';
//        var_dump($data);exit;
        return $data;
    }

    //两竖
    private function commonTwoFirst($padding=[], $img_list = [],$flag = 1){
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

        //两张排法
        $img_scales = array_column($image_list, 'img_scale');
        //计算可编辑区域
        $edit_width = (100- $config['standard']['padding'][1]-$config['standard']['padding'][3]);
        $edit_height = (100- $config['standard']['padding'][0]-$config['standard']['padding'][2])/$this->wh;

        //可编辑区比例
        $edit_scale = $edit_width/$edit_height;

        //横排比例（两竖的那种）
        $sum_x = array_sum($img_scales);
        $auto_pack = [];

        $first = $img_scales[0];
        $second = $img_scales[1];

        $i_height = $edit_height;
        //第一张相片宽
        $f_width = $i_height*$first;
        //第二张相片宽
        $s_width = $i_height*$second;

        //分摊切割比例
        $sy = ($f_width + $s_width) - $edit_width;

        $bl_f = $f_width/($s_width+$f_width);
        $bl_s = $s_width/($s_width+$f_width);
        //第一张相片显示宽

        $dis_width_f = $f_width - (($sy*$bl_f));
        //第二张显示宽
        $dis_width_s = $s_width - (($sy*$bl_s));
        // var_dump($dis_width_f);
        // var_dump($dis_width_s);
        $img_width_f = $edit_height* $image_list[0]['img_scale'];

        $real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
        $auto_pack[0]['path'] = $image_list[0]['path'];
        $auto_pack[0]['width'] = $dis_width_f;
        $auto_pack[0]['top'] = $config['standard']['padding'][0];
        $auto_pack[0]['left'] = $config['standard']['padding'][3];
        $auto_pack[0]['height'] = $real_height;
        //$auto_pack[0]['relative_width'] = (($img_width_f/$dis_width_f)*100)/$this->wh;

        if($sy < 0 ){  //
            $relative_width = 100;
            $relative_height= ($dis_width_f/$img_width_f)*100;
            //var_dump($relative_width);exit;
            $top = ($relative_height - 100)/2;
            $left = 0;

        }else{
            $relative_height = 100;
            $relative_width = ($img_width_f/$dis_width_f)*100;
            $left = 0;
            $top = ($relative_height - 100)/2;
        }

        $auto_pack[0]['relative_width'] = $relative_width;
        $auto_pack[0]['relative_height'] = $relative_height;
        $auto_pack[0]['relative_cut_top'] = $top;
        $auto_pack[0]['relative_cut_left'] = $left;

        //如果对比长小于100
        // if($auto_pack[0]['relative_width'] < 100){
        //     //$real_relativ_w =
        //     $auto_pack[0]['relative_height'] = (100/$auto_pack[0]['relative_width'])*100;
        //     $auto_pack[0]['relative_width'] = 100;
        //     $auto_pack[0]['relative_cut_left'] = 0;
        //     $auto_pack[0]['relative_cut_top'] = ($auto_pack[0]['relative_height'] - 100)/2;
        // }else{
        //     $auto_pack[0]['relative_height'] = 100;
        //     $auto_pack[0]['relative_cut_left'] = ($auto_pack[0]['relative_width'] - 100)/2;
        //     $auto_pack[0]['relative_cut_top'] = 0;
        // }


        $img_width_s = $edit_height* $image_list[1]['img_scale'];
        //$real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
        $auto_pack[1]['path'] = $image_list[1]['path'];
        $auto_pack[1]['width'] = $dis_width_s;
        $auto_pack[1]['top'] = $config['standard']['padding'][0];
        $auto_pack[1]['left'] = $config['standard']['padding'][3]+$dis_width_f+1;
        $auto_pack[1]['height'] = $real_height;
        $auto_pack[1]['relative_width'] = (($img_width_s/$dis_width_s)*100)/$this->wh;

        //如果对比长小于0
        // if($auto_pack[1]['relative_width'] < 100){

        //     $auto_pack[1]['relative_height'] = (100/$auto_pack[1]['relative_width'])*100;
        //     $auto_pack[1]['relative_width'] = 100;
        //     $auto_pack[1]['relative_cut_left'] = 0;
        //     $auto_pack[1]['relative_cut_top'] = ($auto_pack[1]['relative_height'] - 100)/2;
        // }else{
        //     $auto_pack[1]['relative_height'] = 100;
        //     $auto_pack[1]['relative_cut_left'] = ($auto_pack[1]['relative_width'] - 100)/2;
        //     $auto_pack[1]['relative_cut_top'] = 0;
        // }
        if($sy < 0 ){  //
            $relative_width = 100;
            $relative_height= ($dis_width_s/$img_width_s)*100;
            //var_dump($relative_width);exit;
            $top = ($relative_height - 100)/2;
            $left = 0;

        }else{
            $relative_height = 100;
            $relative_width = ($img_width_s/$dis_width_s)*100;
            $left = 0;
            $top = ($relative_height - 100)/2;
        }

        $auto_pack[1]['relative_width'] = $relative_width;
        $auto_pack[1]['relative_height'] = $relative_height;
        $auto_pack[1]['relative_cut_top'] = $top;
        $auto_pack[1]['relative_cut_left'] = $left;


        return $auto_pack;
    }

    //两横
    private function commonTwoSecond($padding=[], $img_list = [], $flag = 1){
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


        //两张排法
        $img_scales = array_column($image_list, 'img_scale');
        //计算可编辑区域
        $edit_width = (100- $config['standard']['padding'][1]-$config['standard']['padding'][3]);
        $edit_height = (100- $config['standard']['padding'][0]-$config['standard']['padding'][2])/$this->wh;

        //可编辑区比例
        $edit_scale = $edit_width/$edit_height;

        $first = $img_scales[0];
        $second = $img_scales[1];

        //竖排比例(两横)
        $sum_y = ($first*$second)/($first+$second);
        $auto_pack = [];


        $i_width= $edit_width;
        //第一张相片高
        $f_height = $i_width/$first;
        //第二张相片高
        $s_height  = $i_width/$second;

        //分摊切割比例
        $sy = ($f_height + $s_height) - $edit_height;

        $bl_f = $f_height/($f_height+$s_height);
        $bl_s = $s_height/($f_height+$s_height);
        //第一张相片显示宽
        $dis_height_f = $f_height- (($sy*$bl_f));
        //第二张显示宽
        $dis_height_s = $s_height - (($sy*$bl_s));


        $img_height_f = $edit_width/ $image_list[0]['img_scale'];

        $real_width = 100- $config['standard']['padding'][1]-$config['standard']['padding'][3];
        $auto_pack[0]['path'] = $image_list[0]['path'];
        $auto_pack[0]['width'] =  $real_width;
        $auto_pack[0]['top'] = $config['standard']['padding'][0];
        $auto_pack[0]['left'] = $config['standard']['padding'][3];
        $auto_pack[0]['height'] = $dis_height_f*$this->wh-0.5;

        if($sy < 0 ){  //
            $relative_height = 100;
            $relative_width = ($dis_height_f/$img_height_f)*100;
            //var_dump($relative_width);exit;
            $top = 0;
            $left = ($relative_width - 100)/2;

        }else{
            $relative_width = 100;
            $relative_height = ($img_height_f/$dis_height_f)*100;
            $left = 0;
            $top = ($relative_height - 100)/2;
        }

        $auto_pack[0]['relative_width'] = $relative_width;
        $auto_pack[0]['relative_height'] = $relative_height;
        $auto_pack[0]['relative_cut_top'] = $top;
        $auto_pack[0]['relative_cut_left'] = $left;



//        $relative_width = 100;
//        $relative_height = (($img_height_f/$dis_height_f)*100);
//        var_dump($flag);exit;


//        $auto_pack[0]['relative_height'] = (($img_height_f/$dis_height_f)*100);
//
//        //如果对比长小于100
//        if($auto_pack[0]['relative_height'] < 100){
//
//            $auto_pack[0]['relative_width'] = (100/$auto_pack[0]['relative_height'])*100;
//            $auto_pack[0]['relative_height'] = 100;
//            $auto_pack[0]['relative_cut_top'] = 0;
//            $auto_pack[0]['relative_cut_left'] = ($auto_pack[0]['relative_width'] - 100)/2;
//        }else{
//            $auto_pack[0]['relative_width'] = 100;
//            $auto_pack[0]['relative_cut_left'] = 0;
//            $auto_pack[0]['relative_cut_top'] = ($auto_pack[0]['relative_height'] - 100)/2;
//        }


        $img_height_s = $edit_width/ $image_list[1]['img_scale'];
        //$real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
        $auto_pack[1]['path'] = $image_list[1]['path'];
        $auto_pack[1]['width'] = $real_width;
        $auto_pack[1]['top'] = $config['standard']['padding'][0] +($dis_height_f*$this->wh)+1;
        $auto_pack[1]['left'] = $config['standard']['padding'][3];
        $auto_pack[1]['height'] = $dis_height_s*$this->wh-0.5;

        if($sy < 0 ){  //
            $relative_height = 100;
            $relative_width = ($dis_height_s/$img_height_s)*100;;
            //var_dump($relative_width);exit;
            $top = 0;
            $left = ($relative_width - 100)/2;

        }else{
            $relative_width = 100;
            $relative_height = ($img_height_s/$dis_height_s)*100;
            $left = 0;
            $top = ($relative_height - 100)/2;
        }

        $auto_pack[1]['relative_width'] = $relative_width;
        $auto_pack[1]['relative_height'] = $relative_height;
        $auto_pack[1]['relative_cut_top'] = $top;
        $auto_pack[1]['relative_cut_left'] = $left;


        //$auto_pack[1]['relative_width'] = 100;
//        $auto_pack[1]['relative_height'] = (($img_height_s/$dis_height_s)*100);
//
//        //如果对比长小于100
//        if($auto_pack[1]['relative_height'] < 100){
//
//            $auto_pack[1]['relative_width'] = (100/$auto_pack[1]['relative_height'])*100;
//            $auto_pack[1]['relative_height'] = 100;
//            $auto_pack[1]['relative_cut_top'] = 0;
//            $auto_pack[1]['relative_cut_left'] = ($auto_pack[1]['relative_width'] - 100)/2;
//        }else{
//            $auto_pack[1]['relative_width'] = 100;
//            $auto_pack[1]['relative_cut_left'] = 0;
//            $auto_pack[1]['relative_cut_top'] = ($auto_pack[1]['relative_height'] - 100)/2;
//        }


        return $auto_pack;
    }
    /**
     * @param $img_info
     * @param array $padding
     * @return array
     */
    private function commonOne($img_info, $padding=[]){
        $img_scale = $img_info['img_scale'];
        $edit_width = (100 - $padding[1]-$padding[3]);
        $edit_height = (100 - $padding[0]-$padding[2])/$this->wh;

        $edit_scale = $edit_width/$edit_height;

        $auto_pack = [];
        if($img_scale > $edit_scale){ //横向切
            $img_width = $edit_height * $img_info['img_scale'];
            $relative_width = (($img_width/$edit_width)*100);
            $relative_height = 100;
            $relative_cut_left = ($relative_width - 100)/2;
            $relative_cut_top = 0;

        }else{
            $img_height = $edit_width / $img_info['img_scale'];
            $relative_height = (($img_height/$edit_height)*100);
            $relative_width = 100;
            $relative_cut_left = 0;
            $relative_cut_top = ($relative_height - 100)/2;;
        }
        $auto_pack['path'] = $img_info['path'];
        $auto_pack['width'] = $edit_width;
        $auto_pack['top'] = $padding[0];
        $auto_pack['left'] = $padding[3];
        $auto_pack['height'] =100 - $padding[0]-$padding[2];
        $auto_pack['relative_width'] = $relative_width;
        $auto_pack['relative_height'] = $relative_height;
        $auto_pack['relative_cut_left'] = $relative_cut_left;
        $auto_pack['relative_cut_top'] = $relative_cut_top;

        return $auto_pack;

    }
    /**
    *
    */
    private function makeData($image_list, $standard_temp){
        //var_dump($image_list);exit;
        foreach($standard_temp as $sk=>$sv){
            //规定的比例
            //var_dump($sv);exit;
            $cut_top = 0; 
            $cut_right = 0;
            $cut_bottom = 0;
            $cut_left = 0;
            $relative_width = 0;
             $relative_height = 0;
            $tmp_scale = ($sv['width']*$this->wh)/$sv['height'];
            if($tmp_scale > $image_list[$sk]['img_scale']){
                 $img_width = $sv['width']; //所占整个区域长度的百分比
                 $img_height = $img_width /($image_list[$sk]['img_scale']) ;
                 $relative_width = 100;
                 $relative_height = ($img_height/$sv['height'])*100*$this->wh;
                 //上下隐藏部分
                 $cut_top = $cut_bottom = ($img_height - $sv['height'])/2;
                 $relative_cut_left = 0;
                 $relative_cut_top = ($relative_height - 100)/2;
            }else{
                $img_height = $sv['height'];
                $img_width = $img_height * $image_list[$sk]['img_scale'] ;
                $cut_left = $cut_right = ($img_width - $sv['width'])/2;
                $relative_height = 100 ;
                $relative_width = (($img_width/$sv['width'])*100)/$this->wh;
                $relative_cut_top = 0;
                $relative_cut_left = ($relative_width - 100)/2;
            }

            //相对整个画布的百分比
            $standard_temp[$sk]['path'] =  $image_list[$sk]['path']; 
            $standard_temp[$sk]['img_width'] = $img_width; 
            $standard_temp[$sk]['img_height'] = $img_height;
            $standard_temp[$sk]['cut_top'] = $cut_top;
            $standard_temp[$sk]['cut_right'] = $cut_right;
            $standard_temp[$sk]['cut_bottom'] = $cut_bottom;
            $standard_temp[$sk]['cut_left'] = $cut_left;
            //图片相对遮罩的比例,ddd
            $standard_temp[$sk]['relative_width'] = $relative_width; 
            $standard_temp[$sk]['relative_height'] = $relative_height;

            $standard_temp[$sk]['relative_cut_left'] = $relative_cut_left; 
            $standard_temp[$sk]['relative_cut_top'] = $relative_cut_top;

            //形状变化

            
           
        }

        return $standard_temp;
    }
}