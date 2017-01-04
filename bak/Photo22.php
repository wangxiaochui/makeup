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
        if(!$is_use_temp){
            $ret = $this->packNoTemp();
        }else{
            $ret = $this->packTemp();
        }
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
                    $arr_dir[$i]['path'] = 'http://learn.com/images/makeup/'.$file;
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

        $w_h = $this->w_h;
        //if(abs($w_h - $image_list['img_scale']) >$config['standard']['limit'])
        if(true)
        {
            //var_dump($config['standard']);exit;
            //使用标准模板
            $data = $config['standard']['nunOne'];
        }
        else
        {
            //
        }

    }

    private function pbTwoPage(){
        $image_list = $this->image_list;
        $config = $this->getConf();

        $data = $config['standard']['nunTwo'];
    }

    private function pbThreePage(){
        $image_list = $this->image_list;
        $config = $this->getConf();

        $data = $config['standard']['nunThree'];
    }
}