<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3
 * Time: 9:55
 */

namespace App\Services\Makeup;


class Manger
{
    public function getType($type)
    {
        $driverMethod = 'create'.ucfirst($type).'Driver';
        return $this->$driverMethod();
    }

    private function createPhotoDriver(){
        return new Photo();
    }

    private function createWxbookDriver(){
        return new Wxbook();
    }

    private function createHcdiyDriver(){
        return new Hcdiy();
    }
}