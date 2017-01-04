<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3
 * Time: 10:09
 */

namespace App\Services\Makeup;


use Illuminate\Support\Facades\Facade;

class MakeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'makeup';
    }
}