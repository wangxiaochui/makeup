<?php
namespace App\Services\Makeup\Type;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3
 * Time: 13:17
 */
use Config;
class Waterfall
{
    public function __construct()
    {
        //$this->config = Config::get('temp');
    }

    public function makeData($image_list, $w_h = 1, $is_font = 0, $padding = [])
    {

    }

    /**
     * @param $image_list
     * @param int $w_h
     * @param int $is_font
     * @param array $padding
     */
    public function makeDataold($image_list, $w_h = 1, $is_font = 0, $padding = [])
    {
        $this->padding = $padding;
        $this->wh = $w_h;
        $eidt_size = $this->editSize();

        //定宽高(1排1~3张,2~3排)
        $row_width  = ($eidt_size['width'] - 5)/3;
       // var_dump($row_width);
        $edit_height = $eidt_size['height'];

        if($w_h >1){
            $cols = 3; //最大排
        }else{
            $cols = 2;
        }

        $total_height = 0;
        $i = 0;
        $allow = [];
        $pb_over = [];
        foreach($image_list as $k=>$v){
            $img_height = $row_width / $v['img_scale'];

            $total_height += $img_height;
            //var_dump($img_height);
            $i++ ;
            if($total_height > $edit_height){

                $over = $total_height - $edit_height;
                if($i == 1){
                    $allow[] = 1;
                    $i = 0;
                    $pb_over[] = $over;
                    continue;
                }
                if($over > 15){
                    $allow[] = $i-1;
                    $total_height = $img_height;
                    $i = 1;
                    $pb_over[] = 0;

                }else{
                    $total_height = 0;
                    $allow[] = $i;
                    $i = 0;
                    $pb_over[] = $over;
                }

            }
        }
//        echo '<pre>';
//        var_dump($pb_over);
//        var_dump(array_sum($allow));
//        var_dump(count($image_list));
        if(count($image_list) > array_sum($allow)){
            $allow[] = count($image_list) - array_sum($allow);
        }

        //var_dump($pb_over);
        //var_dump(count($allow)) ;exit;
        $start = 0;
        $row = 0;
//        while(count($image_list) > $start){
//            $rand = $allow[$row];
//            //$over = $pb_over[$row];
//            $images = array_slice($image_list, $start, $rand);
//            $start += $rand;
//            $row ++;
//        }

        $start = 0;
        foreach($allow as $k=>$v){
            $images = array_slice($image_list, $start, $v);
            echo '<pre>';
            var_dump($images);
            $start += $v;
        }


    }

    private function editSize(){
        $width  = 100;
        $height = 100 * $this->wh;

        $edit_width = $width - $this->padding[1] - $this->padding[3];
        $edit_height = $height - $this->padding[0] - $this->padding[2];

        return ['width' => $edit_width, 'height' => $edit_height ];
    }
}