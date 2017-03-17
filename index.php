<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 14:48
 */
header('Content-Type:text/html;charset=utf-8');
include_once('./common/config.inc.php');
if(!defined('VISITSTATE')){echo 'visit error';exit();}
include_once('./common/mysql.class.php');
include_once('./common/page.class.php');
include_once('./common/common.class.php');
include_once('./class/base.class.php');
include_once('./common/smarty/Smarty.class.php');

$Act=isset($_GET['act'])?trim($_GET['act']):'default';//判断前台类是否存在
$Model=isset($_GET['m'])?trim($_GET['m']):'index';

$Act=strtolower($Act).'Action';//把对应的类强制转化为小写，再进行Action拼接
$Model=strtolower($Model);//把方法也强制转化为小写

if(!empty($Act))
{
    $ClassPath='./class/'.$Act.'.class.php';
    if(file_exists($ClassPath))
    {
        include_once($ClassPath);
    }else{
        echo common::MsgShow(2,'请求有误，请不要非法操作1');
    }
}
if(!class_exists($Act))
{
    echo Common::MsgShow(2,'请求有误，请不要非法操作2');
    exit();
}
$Class=new $Act;

if(!method_exists($Act,$Model))
{
    echo Common::MsgShow(2,'请求有误，请不要非法操作3');
    exit();
}

$Class->$Model();//调用当前类中的具体方法