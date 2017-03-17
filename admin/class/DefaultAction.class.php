<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 14:17
 */
class DefaultAction extends Base
{
    public function  Index()
    {
        include('./template/default.html');
    }
}