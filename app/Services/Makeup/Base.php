<?php
namespace App\Services\Makeup;
use Illuminate\Support\Facades\Config;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 10:58
 */
class Base
{
    private $config;

    //设定
    public function setConf($config){
        $this->config = $config;
    }

    //读出
    public function getConf(){
        return $this->config;
    }

    //两竖
    protected function commonTwoFirst($padding=[], $img_list = []){
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

        $auto_pack[0]['rotate'] = 0;
        $auto_pack[0]['radius'] = 0;


        $img_width_s = $edit_height* $image_list[1]['img_scale'];
        //$real_height = 100- $config['standard']['padding'][0]-$config['standard']['padding'][2];
        $auto_pack[1]['path'] = $image_list[1]['path'];
        $auto_pack[1]['width'] = $dis_width_s;
        $auto_pack[1]['top'] = $config['standard']['padding'][0];
        $auto_pack[1]['left'] = $config['standard']['padding'][3]+$dis_width_f+1;
        $auto_pack[1]['height'] = $real_height;
        $auto_pack[1]['relative_width'] = (($img_width_s/$dis_width_s)*100)/$this->wh;

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

        $auto_pack[1]['rotate'] = 0;
        $auto_pack[1]['radius'] = 0;


        return $auto_pack;
    }

    //两横
    protected function commonTwoSecond($padding=[], $img_list = []){
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
        $auto_pack[0]['rotate'] = 0;
        $auto_pack[0]['radius'] = 0;

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
        $auto_pack[1]['rotate'] = 0;
        $auto_pack[1]['radius'] = 0;


        return $auto_pack;
    }
    /**
     * @param $img_info
     * @param array $padding
     * @return array
     */
    protected function commonOne($img_info, $padding=[]){
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
        $auto_pack['rotate'] = 0;
        $auto_pack['radius'] = 0;

        return $auto_pack;

    }
    /**
     *
     */
    protected function makeData($image_list, $standard_temp){
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
    protected function mb_str_split($str,$split_length=1,$charset="UTF-8"){
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