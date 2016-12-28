<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class AutoController extends Controller
{
    //
    public function index(Request $request){

        return view('web.auto.index');
    }

    public function test(Request $request){
        $this->width = $width = $request->input('w')?$request->input('w'):559;
        $this->height = $height = $request->input('h')?$request->input('h'):794;
        $setting = $this->setting();
        $pbs = $this->get_detail($width, $height);
//        echo '<pre>';
//        print_r($pbs);
        $perv_height = 0;
        $arr_total = [];
        $page = 1;
        $arr_total = [];
//        echo '<pre>';
//        print_r($pbs);exit;
        $rand_time = 1420041600;
        foreach($pbs as $k=>$v){
            $count = count($v);
            $arr_info = $this->pb($count, $v, $page, $perv_height);
            $fill_height = $v[0]['r_height'] - $setting['padding'][0]-$setting['padding'][2];

            $perv_height = $arr_info['self_height'] + $arr_info['perv_height']+30;
           // var_dump($page);
            $page = $arr_info['page'];

            //组合数据
            if($arr_info['flag'] !='other')
            {

                $rand_time+= mt_rand(90000,900000);

                $y = date('Y',$rand_time);
                $m = date('m',$rand_time);
                $d = date('d',$rand_time);
                $t = date('H:i',$rand_time);

                //日期位置



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
//        echo '<pre>';
//        print_r($arr_total);
//        exit;
        return view('web.auto.test',['detail'=>$arr_total]);
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

    //获取显示区域
    protected function get_display($width, $height){
        $r_width = $width - $this->setting()['padding'][1] - $this->setting()['padding'][3];
        $r_height= $height - $this->setting()['padding'][0] - $this->setting()['padding'][2];

        return ['r_width'=>$r_width, 'r_height'=>$r_height];
    }
    //获取信息
    protected function get_info(){
        $info_path =  storage_path('app/data');
        $fp = opendir($info_path);
        $arr_dir = [] ;
        while($file = readdir($fp)){
            if($file!="." && $file!=".."){
                if(is_dir($info_path.'/'.$file)){
                    $arr_dir[] = $info_path.'/'.$file;
                }
            }

        }
        return $arr_dir;
    }

    //获取详细
    protected function get_detail($width, $height){
        $info = $this->get_info();
        //取第一个测试
        $pb_info = [];
        foreach ($info as $k=>$v){
            $fp = opendir($v);
            //取文字
            $info_path =  storage_path('app/data');
            //$path = $info_path.'/'.'title.txt';
            $str = Storage::get('data/title.txt');
            $arr_text = explode("\r\n",$str);
            //print_r($arr_text);exit;
            shuffle($arr_text);
            $title = $arr_text[0];

            //估算字体大致长宽,后面算

            //日期计算
            $num = strrpos($v, '/');
            $time = substr($v,$num+1);

            $y = date('Y',$time);
            $m = date('m',$time);
            $d = date('d',$time);
            $t = date('H:i',$time);


            $i = 0;
            $arr_ret = $this->get_display($width, $height);
            $arr_ret['img_width'] = 0;
            $arr_ret['img_height'] = 0;
            $arr_ret['img_scale'] = 1;
            $arr_ret['title'] = $title;
            $arr_ret['time'] = ['y'=>$y, 'm'=>$m, 'd'=>$d, 't'=>$t];
            $pb_info[$k][0] = $arr_ret;

            while($file = readdir($fp)){
                if($file!="." && $file!=".."){
                    $full_path = $v.'/'.$file;
                    // var_dump($full_path);
                    $off = strrpos( $full_path,"data/");
                    $hm = substr($full_path,$off+5);
                    $img_path = 'http://'.$_SERVER['HTTP_HOST'].'/images/'.$hm;
                    //var_dump($img_path);
                    $img_info  =getimagesize($full_path);
                    //var_dump($img_info);exit;
                    $img_width = $img_info[0];
                    $img_height = $img_info[1];

                    //$arr_ret = $this->get_display($width, $height);
                    $arr_ret['img_width'] = $img_width;
                    $arr_ret['img_path'] = $img_path;
                    $arr_ret['img_height'] = $img_height;
                    $arr_ret['img_scale'] = $img_width/$img_height; //图片比例
                    //$arr_ret['title'] = $title;
                    $pb_info[$k][$i] = $arr_ret;
                    $i++;
                }
            }
        }

        return $pb_info;
    }

    //svg长宽
    protected function svg_info(){
        //$this->svg_width = $this->width -
        $info = $this->get_display($this->width, $this->height);

    }

    //排版入口
    protected function pb($count, $data, $page=1, $perv_height=0){

       // $fill_height = $data[0]['r_height']-$this->setting()['padding'][0]-$this->setting()['padding'][2];

        if($data[0]['img_width'] == 0){ //无图片

			$arr_info = $this->pd_no_img($data, $perv_height, $page);
            $arr_info['self_height'] = $arr_info['self_height']+$perv_height;
            $arr_info['text'] = $arr_info;
            return $arr_info;
        }

        if($count%2 == 0){
            $arr_text_info = $this->pd_no_img($data, $perv_height,$page);
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
            return ['self_height'=>0,'perv_height'=>$perv_height,'page'=>$page, 'flag'=>'other'];
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
        $fill_width = $data[0]['r_width'] -(2*$text_left);

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
        //var_dump($text);exit;
        //填充区(不包括日期图标)
        $fill_width = $data[0]['r_width'] -(2*$text_left);
        $fill_height = $data[0]['r_height'];
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
        echo '<pre>';
        var_dump($arr_info_ret);exit;

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
        $fill_height = $data[0]['r_height'];
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
            $fill_width = $data[0]['r_width'] -(2*$text_left);
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
        //var_dump($text);exit;
		//填充区(不包括日期图标)
		$fill_width = $data[0]['r_width'] -(2*$text_left);
        $fill_height = $data[0]['r_height'];
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
