<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 14:14
 */

if(!defined('VISITSTATE')){echo 'visit error';exit();}
class Base extends Common
{
    public function AdminLoginCheck()
    {
        if(empty($_SESSION['AdminID']) || empty($_SESSION['AdminName']))
        {
            session_unset();
            session_destroy();
            echo Common::MsgShow(1,'','./?Action=Login');
            exit();
        }
        $Where=array();
        $Where['Fields']='count(*) as c';
        $Where['Where']='Admin_Name="'.$_SESSION['AdminName'].'"';
        $Info=$this->db->getOne(DBPREFIX.'admin',$Where);
        if(empty($Info['c']))
        {
            session_unset();
            session_destroy();
            echo Common::MsgShow(1,'您当前无权管理此功能','./?Action=Login');
            exit();
        }
        if(time()-$_SESSION['AdminTime']>ADMINTIMECHECK)
        {
            session_unset();
            session_destroy();
            echo Common::MsgShow(1,'您当前已连接超时，请重新登录','./?Action=Login');
            exit();
        }else
        {
            $_SESSION['AdminTime']=time();
        }
    }
    //获取当前表中的总记录数
    protected function AllCount($TableName,$WStr,$Field)
    {
        //$Wstr表示客户查询的内容，即字符串
        $Where=array();
        $Where['Fields']='count(*) as c';
        if(!empty($WStr))
        {
            $Where['Where']=$Field.' like "%'.$WStr.'%"';
        }
        //$SQL='select count(*) as c from web_about;';
        $Info=$this->db->GetOne(DBPREFIX.$TableName,$Where);
        return $Info['c'];
    }

}