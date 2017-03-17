<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 14:13
 */
header('Content-Type:text/html;charset=utf-8');
session_start();
include_once('../common/config.inc.php');
if(!defined('VISITSTATE')){echo 'visit error';exit();}
include_once('../common/mysql.class.php');
include_once('../common/common.class.php');
include_once('../common/page.class.php');
include_once('./class/Base.class.php');
include_once('./class/UpLoadFile.class.php');

$Act=isset($_GET['Action'])?$_GET['Action']:'Default';
$Method=isset($_GET['Method'])?$_GET['Method']:'Index';
$Act.='Action';

function __autoload($ClassName)
{
    $ClassPath='./class/'.$ClassName.'.class.php';
//print_r($ClassPath);
    if(file_exists($ClassPath))
    {
        include_once($ClassPath);
    }else
    {
        echo Common::MsgShow(2,'请勿非法操作1','');
        exit();
    }
}

if(!class_exists($Act))
{
    echo Common::MsgShow(2,'请勿非法操作2');
    exit();
}
$CallClassName=new $Act;//调用类名
if($Act!='LoginAction')
{
    $CallClassName->AdminLoginCheck();
}
if(!method_exists($Act,$Method))
{
    echo Common::MsgShow(2,'请勿非法操作3');
    exit();
}
$CallClassName->$Method();