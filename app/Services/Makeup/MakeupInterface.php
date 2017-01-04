<?php
namespace App\Services\Makeup;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 11:13
 */
interface MakeupInterface{
    public function figure($image_list); //插图
    public function waterfall($image_list); //瀑布流
}