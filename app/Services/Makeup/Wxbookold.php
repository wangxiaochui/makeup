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

    public function waterfall($image_list = [], $width ='', $height='')
    {
        $this->r_width = $width - $this->setting()['padding'][1] - $this->setting()['padding'][3];
        $this->r_height= $height - $this->setting()['padding'][0] - $this->setting()['padding'][2];
        // TODO: Implement waterfall() method.
        $setting = $this->setting();

        $pbs = $this->getData();
//        echo '<pre>';
//        print_r($pbs);exit;
        $perv_height = 0;
        $arr_total = [];
        $page = 1;
        $arr_total = [];
//        echo '<pre>';
//        print_r($pbs);exit;
        foreach($pbs as $k=>$v){
            $count = count($v);
            //var_dump($page);
            $arr_info = $this->pb($count, $v, $page, $perv_height);
            if(!$arr_info)
                continue;
            $fill_height = $this->r_height - $setting['padding'][0]-$setting['padding'][2];

            $perv_height = $arr_info['self_height'] + $arr_info['perv_height']+30;
            // var_dump($page);
            $page = $arr_info['page'];
            echo '<pre>';

            foreach($pbs as $k=>$v){
                $count = count($v);
                $arr_info = $this->pb($count, $v, $page, $perv_height);
                $fill_height = $this->r_height - $setting['padding'][0]-$setting['padding'][2];

                $perv_height = $arr_info['self_height'] + $arr_info['perv_height']+30;
                // var_dump($page);
                $page = $arr_info['page'];

                //var_dump($arr_info['flag']);
                //组合数据

                $y = $v[0]['time']['y'] ;
                $m = $v[0]['time']['m'] ;
                $d = $v[0]['time']['d'] ;
                $t = $v[0]['time']['t'] ;

                if($arr_info['flag'] == 'text'){
                    $arr_total[$arr_info['page']][$k]['text'] = $arr_info['text'];
                    $day_pos =$arr_total[$arr_info['page']][$k]['text'][0]['y'] + 2;
                    $rect_pos =$arr_total[$arr_info['page']][$k]['text'][0]['y'] + 5;
                    $time_post = $arr_total[$arr_info['page']][$k]['text'][0]['y'] + 17;

                    $arr_total[$arr_info['page']][$k]['time'] = [$y, $m, $d, $t,$day_pos,$rect_pos,$time_post];
                }elseif($arr_info['flag'] == 'img_text' ||$arr_info['flag'] == 'text_img_od' ){
                    if($arr_info['flag'] == 'text_img_od'){
                        //echo '<pre>';
                        // var_dump($arr_info['img']);
                    }
                    $arr_total[$arr_info['text']['page']][$k]['text'] = $arr_info['text'];
                    $day_pos =$arr_total[$arr_info['text']['page']][$k]['text'][0]['y'] + 2;
                    $rect_pos =$arr_total[$arr_info['text']['page']][$k]['text'][0]['y'] + 5;
                    $time_post = $arr_total[$arr_info['text']['page']][$k]['text'][0]['y'] + 19     ;

                    $arr_total[$arr_info['text']['page']][$k]['time'] = [$y, $m, $d, $t,$day_pos,$rect_pos,$time_post];

                    $arr_total[$arr_info['text']['page']][$k]['text'] = $arr_info['text'];
                    //$arr_total[$arr_info['text']['page']][$k]['time'] = [$y, $m, $d, $t];
                    //$arr_total[$arr_info['text']['page']][$k]['text'] = $arr_info['text'];
                    foreach($arr_info['img'] as $ik=>$iv){
//                        echo '<pre>';
//                        var_dump($iv);
                        $arr_total[$iv['page']][$k]['img'][$ik] = $iv;
                    }
                }


            }

        }
        return $arr_total;

    }
    //一般设置
    protected function setting(){
        $arr_setting['padding'] = [50,50,50,50];
        $arr_setting['font_size'] = 14;
        $arr_setting['ico_width'] = 40;
        $arr_setting['svg_width'] = 83; //svg宽度
        $arr_setting['svg_top'] = 0; //svg宽度
        $arr_setting['svg_main_left'] = 50; //svg宽度
        $arr_setting['font_height'] = 16; //svg宽度
        $arr_setting['min_img_height']= 50; //图片最小高度
        $arr_setting['img_margin'] = 10;
        $arr_setting['per_margin'] = 30; //每条之前间距
        return $arr_setting;

    }

    /**
     * 测试用
     */
    public function getData(){
        $str = Storage::get('data/sns.json');
        $info = json_decode($str, true);
        $arr_timestamp = array_column($info, 'timestamp');
        //sort($img_scales);
        array_multisort($info,SORT_ASC,$arr_timestamp);

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
            if(!empty($v['mediaList']) && $v['contentType'] == 3){
                foreach($v['mediaList'] as $mk=>$mv){
                    $wx_info[$k][$mk]['img_path'] = $mv['url'];
                    $wx_info[$k][$mk]['img_width'] = $mv['width'];
                    $wx_info[$k][$mk]['img_height'] = $mv['height'];
                    $wx_info[$k][$mk]['img_scale'] = $mv['width']/$mv['height'];
                    $wx_info[$k][$mk]['title'] = str_replace("\n","",$v['content']);




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
                $wx_info[$k][0]['title'] = $v['content'];
            }
        }
        return $wx_info;
    }

    //排版入口
    protected function pb($count, $data, $page=1, $perv_height=0){

        // $fill_height = $data[0]['r_height']-$this->setting()['padding'][0]-$this->setting()['padding'][2];
        if($data[0]['img_width'] == 0){ //无图片

            $arr_info = $this->pd_no_img($data, $perv_height, $page);

            if(!$arr_info)
                return false;
            $arr_info['self_height'] = $arr_info['self_height']+$perv_height;
            $arr_info['text'] = $arr_info;
            return $arr_info;
        }

        if($count%2 == 0){
            //var_dump($page);
            $arr_text_info = $this->pd_no_img($data, $perv_height,$page);
           // var_dump($arr_text_info);exit;
            if(!$arr_text_info)
                $arr_text_info = [
                    'perv_height' => $perv_height,
                    'self_height' => 0,
                    'page' => $page
                ];
            $perv_height = $arr_text_info['perv_height'] + $arr_text_info['self_height'];
            $arr_info = $this->pd_odd($data, $perv_height,$arr_text_info['page']);
            $arr_info['text'] = $arr_text_info;
//                echo '<pre>';
//                print_r($arr_info);exit;
            return $arr_info;
        }else{

            $arr_info = $this->pd_even($data, $perv_height,$page);
            //$arr_info['self_height'] = $arr_info['self_height']+$perv_height;
            return $arr_info;
           // return ['self_height'=>0,'perv_height'=>$perv_height,'page'=>$page, 'flag'=>'other'];
        }
        $left = [];
        //返回剩余情况
        return $left;
    }
    //奇数图片----英文弄反了。
    protected function pd_even($data, $perv_height, $page){
        //取图片比例
        $min_height = 150; //图片最小高150
        $setting = $this->setting();
        $text_left = $setting['svg_main_left'];
        $fill_width = $this->r_width -(2*$text_left);

        //var_dump($arr);exit;
        $first_img = $data[0];
        //var_dump($first_img);exit;
        //计算每一张图实际尺寸,先按一半一半来算
        $f_img_width = ($fill_width-$setting['img_margin'])/2; //减掉中间间隙的部分
        $f_img_height = intval($f_img_width/$data[0]['img_scale']);

        if($f_img_height<$min_height){
            $f_img_height = $min_height;
            $f_img_width = intval($min_height*$data[0]['img_scale']);
        }

        //排文字
        //文字实际可用长宽
        $text_fill_width = $fill_width-$f_img_width-$setting['img_margin'];
        $text_fill_height = $f_img_width;

        $text_left = $setting['svg_main_left'];
        $text = $data[0]['title'];
        if(empty($text)){
            $text = "[无心情]";
        }
        //var_dump($text);exit;
        //填充区(不包括日期图标)
        $fill_width = $this->r_width -(2*$text_left);
        $fill_height = $this->r_height;
        $arr_text = $this->mb_str_split(trim($text));
        $max_width = 0;
        $arr_info = [];
        $line = 1;   //行数
        $all_width = 0;
        $text_width = 14;
        $text_height = 22;
        $count =count($arr_text);
        $new_all_width = 0 ;
        $ln = ceil($count*$text_width/$text_fill_width);
        $new_hs = 0;
        $hs = 0;
        //估算所占高度
        $gs_height = $ln*$text_height;
        //var_dump($gs_height);exit;

        $padding_top = 0;
        //如果估算高度大于图片高度，将图片包围
        if($gs_height>$f_img_height){
            $gs_height = $gs_height - (($gs_height-$f_img_height)/2);
        }else{
            $padding_top = ($f_img_height-$gs_height)/2;
            $gs_height = $f_img_height;
        }
        // var_dump($gs_height+$perv_height);
        if($gs_height+$perv_height > $fill_height){
            $page++; //翻页
            $perv_height = 0; //重置已占高度
        }
        if($count == 0){
            $self_height = $text_height;
        }
        $self_height = $text_height;
        for($i=0 ; $i<$count; $i++){
            if($arr_text[$i] == ''){
                continue;
            }
//			if(preg_match ("/^[a-z]/i", $arr_text[$i])){
//				//var_dump($arr_text[$i]);
//				$type_space = imagettfbbox(14, 0, public_path('font/times.ttf'),$arr_text[$i]);
//				$text_width =  abs($type_space[5] - $type_space[1]);
//				//$text_height = abs($type_space[4] - $type_space[0]);
//			}
            // $max_width = $max_width ;
            $all_width = $all_width + $text_width;

            //var_dump($max_width);
            //多行文字处理

            if(empty($new_hs)){
                $hs = ceil($all_width/$text_fill_width);
                $self_height = $hs*$text_height;

            }

            //var_dump($self_height);
            //文字超出图片部分
            if($self_height>$f_img_height+$text_width){
                $x = $max_width+$text_left+$setting['img_margin'];
                //$text_fill_width = $fill_width;
                $new_all_width = $new_all_width+$text_width;
                $text_fill_width = $fill_width;
                $new_hs = ceil($new_all_width/$fill_width);
                $self_height = $self_height+$new_hs*$text_height;
                $y = (  ($hs+$new_hs-1)*$text_height)+$perv_height;

            }else{
                $x = $max_width+$text_left+$f_img_width+$setting['img_margin'];
                $self_height = $f_img_height;
                $y = (  ($hs+$new_hs)*$text_height)+$perv_height+$padding_top;
            }


            if($hs + $new_hs > $line){
                $line = $hs + $new_hs;
                $max_width = 0 ;

            }
            if($self_height>$f_img_height){
                $x = $max_width+$text_left+$setting['img_margin'];
            }else{
                $x = $max_width+$text_left+$f_img_width+$setting['img_margin'];
            }


            $arr_info[$i] = ['text'=>$arr_text[$i],'x'=>$x,'y'=>$y];
            $max_width = $max_width+$text_width ;
        }

        $f_img =  ['page'=>$page,'img_path'=> $data[0]['img_path'],'width'=>$f_img_width, 'height'=>$f_img_height ,'left'=> $setting['padding'][3] + $text_left, 'top'=>$perv_height+$setting['padding'][0]];
        unset($data[0]);
        $text_page = $page;
        // $perv_height = $perv_height + $self_height;
        if(count($data)>1){
            $self_height = $perv_height+$self_height;

            $img_info = $this->pd_odd($data, $perv_height+$self_height+10, $page);
            $page = $img_info['page'];

            $self_height = $img_info['self_height'];
            $perv_height = $img_info['perv_height'];

            //if($self_height + )

            array_push($img_info['img'],$f_img);

        }else{
            $img_info['img'] = [$f_img];
        }


        $arr_info_ret['self_height'] = $self_height;  //这条说说所占用的高度
        $arr_info_ret['perv_height'] = $perv_height;
        $arr_info_ret['page'] = $page;
        $arr_info_ret['flag'] = 'text_img_od';
        $arr_info_ret['text'] = $arr_info;
        $arr_info_ret['text']['page'] = $text_page;

        $arr_info_ret['img'] = $img_info['img'];
        //$arr_info_ret['img']['page'] = $page;
        return $arr_info_ret;


    }

    //偶数图片 ----英文弄反了。
    protected function pd_odd($data, $perv_height, $page){
        //根据比例排序
        foreach ($data as $k=>$item) {
            $arr[] = $item['img_scale'];
        }

        array_multisort($arr,SORT_DESC,$data);

        $count = count($data);

        $mid = $count/2;
        $rand = range(0, $mid-1);
        $setting = $this->setting();
        $fill_height = $this->r_height;
        $text_left = $setting['svg_main_left'];
        $img_left = $setting['padding'][3] + $text_left;
        $arr_info = [];
        $self_height = 0;
        for($i=0;$i<$mid;$i++){
            $num = array_rand($rand);
            // var_dump($num);
            $start = $num;  //第一张
            $end = $count-1-$num;//第二张
            //   echo '<pre>';
//            var_dump($data[$start]);
//            var_dump($data[$end]);
            $left = $data[$start];
            $right = $data[$end];
            $fill_width = $this->r_width -(2*$text_left);
            $img_real_width = $fill_width-$setting['img_margin'];

            //计算图片长宽
            $sun_scale = $left['img_scale'] + $right['img_scale'];

            $height = intval($img_real_width/$sun_scale);

            if($perv_height+$height > $fill_height){
                $page++;
                $perv_height=0;
                $self_height = 0;
            }

            $left_width = intval($height * $left['img_scale']);
            $right_width = ceil($height * $right['img_scale']);

            $arr_info['img'][] = ['page'=>$page,'img_path'=> $left['img_path'],'width'=>$left_width, 'height'=>$height ,'left'=>$img_left, 'top'=>$perv_height+$setting['padding'][0]+$self_height];
            $arr_info['img'][] = ['page'=>$page,'img_path'=> $right['img_path'],'width'=>$right_width, 'height'=>$height,'left'=>$img_left+$left_width+$setting['img_margin'], 'top'=>$perv_height+$setting['padding'][0]+$self_height];
            $perv_height = $perv_height + 10;
            $self_height = $self_height+$height;
            unset($rand[$num]);
        }

        $arr_info['self_height'] = $self_height;
        $arr_info['perv_height'] = $perv_height;
        $arr_info['page'] = $page;
        $arr_info['flag'] = 'img_text';
        return $arr_info;
    }

    //纯文字
    protected function pd_no_img($data, $perv_height=0, $page){
        $setting = $this->setting();
        $time = $data[0]['time'];
        //var_dump($data);exit;
        $text_left = $setting['svg_main_left'];
        $text = $data[0]['title'];

        if(!empty($text))
        {
            //var_dump($text);exit;
            //填充区(不包括日期图标)
            $fill_width = $this->r_width -(2*$text_left);
            $fill_height = $this->r_height;
            $arr_text = $this->mb_str_split(trim($text));
            $max_width = 0;
            $arr_info = [];
            $line = 1;   //行数
            $all_width = 0;
            $text_width = 14;
            $text_height = 22;
            $count =count($arr_text);
            $ln = ceil($count*$text_width/$fill_width);

            //估算所占高度
            $gs_height = $ln*$text_height;
            if($gs_height+$perv_height > $fill_height){
                $page++; //翻页
                $perv_height = 0; //重置已占高度
            }

            for($i=0 ; $i<$count; $i++){
                if($arr_text[$i] == ''){
                    continue;
                }
//			if(preg_match ("/^[a-z]/i", $arr_text[$i])){
//				//var_dump($arr_text[$i]);
//				$type_space = imagettfbbox(14, 0, public_path('font/times.ttf'),$arr_text[$i]);
//				$text_width =  abs($type_space[5] - $type_space[1]);
//				//$text_height = abs($type_space[4] - $type_space[0]);
//			}
                $max_width = $max_width ;
                $all_width = $all_width + $text_width;

                //var_dump($max_width);
                //多行文字处理
                $hs = ceil($all_width/$fill_width);
                $self_height = $hs*$text_height;

                $y = ($hs*$text_height)+$perv_height;
                if($hs > $line){
                    $line = $hs;
                    $max_width = 0 ;
                }


                $arr_info[$i] = ['text'=>$arr_text[$i],'x'=>$max_width+$text_left,'y'=>$y];
                $max_width = $max_width+$text_width ;
            }

            $arr_info['self_height'] = $self_height;  //这条说说所占用的高度
            $arr_info['perv_height'] = $perv_height;
            $arr_info['page'] = $page;
            $arr_info['flag'] = 'text';
            //var_dump($arr_info);
            return $arr_info;
        }
        return false;

    }

    private function mb_str_split($str,$split_length=1,$charset="UTF-8"){
        if(func_num_args()==1){
            return preg_split('/(?<!^)(?!$)/u', $str);
        }
        if($split_length<1)return false;
        $len = mb_strlen($str, $charset);
        $arr = array();
        for($i=0;$i<$len;$i+=$split_length){
            $s = mb_substr($str, $i, $split_length, $charset);
            $arr[] = $s;
        }
        return $arr;
    }
}