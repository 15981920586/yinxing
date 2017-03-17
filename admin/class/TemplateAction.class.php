<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:43
 */
class TemplateAction extends Base
{
    function Top()
    {
        include('./template/top.html');
    }
    function Left()
    {
        include('./template/left.html');
    }
    function Main()
    {
        include('./template/main.html');
    }

}