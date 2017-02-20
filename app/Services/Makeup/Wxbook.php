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
        // TODO: Implement todo() method.
        $info = $this->getDataByDate();
        $real_size = $this->realSize();
        $page = 1;
        $overinfo = $real_size;
        $arr_ret = [];
        $ret_info = [];
//        echo '<pre>';
//        print_r($info);exit;
        foreach($info as $yk=>$yv){ //循环年
            foreach($yv as $mk=>$mv){ //循环月
                foreach($mv as $k=>$v){
                    if(!empty($v['mediaList'])){
                        if(count($v['mediaList'])%2 === 0) {
                            $arr_ret[] = $this->pbNoImage($v, $overinfo, $page);

                            $overinfo = $arr_ret['overinfo'];
                            //$overinfo[1] = $overinfo[1]; //每条隔10
                            $page = $arr_ret['page'];
                            $arr_ret[] = $this->evenImage($v, $overinfo, $page);
                        }else{
                            $arr_ret = $this->oddImage($v, $overinfo, $page);
                        }
                        $overinfo = $arr_ret['overinfo'];
                        //$overinfo[1] = $overinfo[1]; //每条隔10
                        $page = $arr_ret['page'];
                    }else{
                        $arr_ret = $this->pbNoImage($v, $overinfo, $page);
                        $overinfo = $arr_ret['overinfo'];
                        //$overinfo[1] = $overinfo[1]; //每条隔10
                        $page = $arr_ret['page'];
                    }
                    $ret_info[] = $arr_ret;
                }
                $page++;
                $overinfo = $real_size;
            }
            $page ++;
            $overinfo = $real_size;
        }
        //return $ret_info;
        echo '<pre>';
        print_r($ret_info);
        exit;
    }

    private function  evenImage($info, $overinfo, $page){
        //return 'aaa';
        //var_dump($info);exit;
        $real_size = $this->realSize();
       // echo '<pre>';
        //var_dump($info);exit;
        foreach($info['mediaList'] as $k=>$v){
            $info['mediaList'][$k]['img_scale'] = $info['mediaList'][$k]['width']/$info['mediaList'][$k]['height'];
        }

        $data = $info['mediaList'];
        foreach ($data as $k=>$item) {
            $arr[] = $item['img_scale'];
        }
        array_multisort($arr,SORT_DESC,$data);

        $count = count($data);

        $mid = $count/2;
        $rand = range(0, $mid-1);
        $self_height = $prev_height = 0;
        $arr_info = [];
        $img_left = $real_size[3];
        $img_top = ($real_size[1] - $overinfo[1]) + $real_size[2];
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

            //计算图片长宽
            $sum_scale = $left['img_scale'] + $right['img_scale'];
            $height = intval($overinfo[0]/$sum_scale); //两张正常排布所需高度

            $left_width = intval($height * $left['img_scale']);
            $right_width = ceil($height * $right['img_scale']);
            $self_height = $self_height + $height;

            if($self_height > $overinfo[1]){
                $page++;
                $overinfo = [$real_size[0], $real_size[1]]; //剩余的长宽
                $self_height = 0;
                $img_top = $real_size[2];
                $prev_height = 0;
            }
            $arr_info['img'][] = ['page'=>$page,'img_path'=> $left['url'],'width'=>$left_width, 'height'=>$height ,'left'=>$img_left, 'top'=>$img_top+$prev_height];
            $arr_info['img'][] = ['page'=>$page,'img_path'=> $left['url'],'width'=>$left_width, 'height'=>$height ,'left'=>$img_left + $left_width, 'top'=>$img_top+$prev_height];
            $prev_height = $prev_height + $height;
            unset($rand[$num]);
        }
        $overinfo = [$overinfo[0],$overinfo[1]-$self_height];
        $arr_info['overinfo'] = $overinfo;
        $arr_info['page'] = $page;
//        echo '<pre>';
//        print_r($arr_info);
        return $arr_info;

    }
    private function oddImage($info, $overinfo, $page){
        $real_size = $this->realSize();

        //图文排，左图右文
//            var_dump($info['mediaList']);
        $ret = $this->imageText($info, $overinfo, $page);
        array_shift($info['mediaList']);
        $arr_even = [] ;
        $overinfo = $ret['overinfo'];
        $page = $ret['page'];
        if(count($info['mediaList'])>0){
            $arr_even = $this->evenImage($info, $ret['overinfo'], $page);
            $overinfo = $arr_even['overinfo'];
            $page = $arr_even['page'];
        }
        //return
        return [$ret,$arr_even,'overinfo'=>$overinfo,'page'=>$page];

    }

    private function imageText($info, $overinfo, $page){
        $real_size = $this->realSize();
        $min_height_scale = 0.2; //最小高度点20%
        $min_height = $real_size[0] * $min_height_scale;
        $over_new = $overinfo;
        //取第一张，和文字呈左右形
        $f_img_info = $info['mediaList'][0];
        $f_img_scale = $f_img_info['width']/$f_img_info['height'];

        //图一半，文一半
        $f_img_width = $overinfo[0]/2;
        $f_img_height = $f_img_width/$f_img_scale;
        //如果算出来图片高度小于限制的最小高度
        if($f_img_height < $min_height){
            $f_img_height = $min_height;
            $f_img_width = $f_img_height*$f_img_scale;
        }
//        var_dump($f_img_width.'aaa');
//        var_dump($overinfo[0].'bbb');
        //文字所占长
        $f_text_width = $overinfo[0] - $f_img_width;



        //本身已经超出了预留限制，作翻页处理
        $img_left = $real_size[3];
        $img_top = ($real_size[1] - $overinfo[1]) + $real_size[2];
        $is_fy = false;
        if($f_img_height > $overinfo[1]){
            $page++;
            $is_fy = true;
            $overinfo = [$real_size[0],$real_size[1]-$f_img_height];
            $img_detail = ['page'=>$page,'img_path'=> $f_img_info['url'],'width'=>$f_img_width, 'height'=>$f_img_height ,'left'=>$img_left, 'top'=>$real_size[2]];
            //

        }else{
            $overinfo = [$real_size[0],$overinfo[1]-$f_img_height];
            $img_detail = ['page'=>$page,'img_path'=> $f_img_info['url'],'width'=>$f_img_width, 'height'=>$f_img_height ,'left'=>$img_left, 'top'=>$img_top];
        }

        //文字排布
        if(empty($info['content'])){
            $text_detail = '';
        }else{
            $line_num = intval($f_text_width/$this->text_width);
            $arr_text = $this->mb_str_split($info['content'],$line_num);


            $top = ($real_size[1] - $over_new[1]) + $real_size[2];
            $left = ($real_size[0] - $over_new[0]) + $real_size[3]+$f_img_width;
            $self_height = 0;
            foreach($arr_text as $k=>$v){

                $arr_new_text[$k]['title'] = $v;
                $arr_new_text[$k]['y'] = $top;
                $arr_new_text[$k]['x'] = $left;
                $arr_new_text[$k]['page'] = $page;
                $top = $top + $this->text_height;
                $self_height = $self_height + $this->text_height;

                if($self_height>=$over_new[1] && !$is_fy){
                    $page++;
                    $over_new = [$real_size[0], $real_size[1]]; //剩余的长宽
                    $top = $real_size[2];
                    $left = $real_size[3];
                    $self_height = 0;
                    $flag = false;
                }
            }
            return ['img'=>$img_detail, 'text'=>$arr_new_text, 'page'=>$page, 'overinfo'=>$overinfo];
        }
       // var_dump($info['content']);exit;
//        echo '<pre>';
//        var_dump($info);exit;
    }

    /**
     * 无图片情形
     * @param $info
     * @param $overinfo
     * @param $page
     * @return mixed
     */
    private function pbNoImage($info, $overinfo,$page){
        $real_size = $this->realSize();
        if(empty($info['content'])){
            $arr_ret['text'] = '';
            $arr_ret['overinfo'] = $overinfo;
            $arr_ret['page'] = $page;
            return $arr_ret;
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
            foreach($arr_text as $k=>$v){
                $arr_new_text[$k]['title'] = $v;
                $arr_new_text[$k]['y'] = $top;
                $arr_new_text[$k]['x'] = $left;
                $arr_new_text[$k]['page'] = $page;
                $top = $top + $this->text_height;
                $self_height = $self_height + $this->text_height;

                if($self_height>=$overinfo[1]){
                    $page++;
                    $overinfo = [$real_size[0], $real_size[1]]; //剩余的长宽
                    $top = $real_size[2];
                    $left = $real_size[3];
                    $self_height = 0;
                    $flag = false;
                }
            }
            $count = count($arr_text);


            $overinfo = [$overinfo[0],$overinfo[1]-$self_height];



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
        echo '<pre>';
        print_r($image_text);exit;
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