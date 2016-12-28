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
}