<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 14:17
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class LoginAction extends Base
{
    public function Index()
    {
        include('./template/login.html');
    }

    //当忘记姓名和密码时，可以在登录验证页面，对密码进行打印和截断操作，这样我们可以随意的编写用户名和密码
    //在这种情况下，即使不知道密码也可以把32位字符串打印出来，并保存数据库 密码位置，
    //用户名则是我们随意编写的用户名，把用户名和密码保存数据库后，即可重新用刚设置的用户名和密码登录
    //注意：数据库保存的密码是加密的密码，自己的密码最好记好
    public function LoginCheck()
    {
        $AdminName=trim($_POST['Admin_Name']);
        $AdminPwd=trim($_POST['Admin_Pwd']);

        $AdminPwd=md5($AdminPwd.PASSWORDKEY);

        //echo $AdminPwd;exit();打印截断密码方法一
        //print_r($AdminPwd);exit();//打印截断密码方法二

        $Where=array();
        $Where['Where']='Admin_Name="'.$AdminName.'" And Admin_Pwd="'.$AdminPwd.'" And Admin_State="0"';
        $Where['Fields']='Admin_ID,Admin_Name';
        $Info=$this->db->getOne('web_admin',$Where);
        if(empty($Info))
        {
            echo Common::MsgShow(2,'您的用户名或密码输入有误。');
            exit();
        }
        $_SESSION['AdminID']=$Info['Admin_ID'];
        $_SESSION['AdminName']=$Info['Admin_Name'];
        $_SESSION['AdminTime']=time();
        echo Common::MsgShow(1,$AdminName.'您好，欢迎你的登录。','./');
        exit();
    }
    public function Quit()
    {
        session_unset();
        session_destroy();
        echo common::MsgShow(1,'您已经安全退出','./?Action=Login');
        exit();
    }
}