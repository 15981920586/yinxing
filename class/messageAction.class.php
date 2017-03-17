<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 14:54
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class messageAction extends base
{
    public function index()
    {
        $this->smarty->assign('MessageTopPoster',$this->PublicPosterCall('9','1001','206'));
        $this->smarty->display('./message/message.html');
    }
    public function save()
    {
        $_POST['Message_Time']=time();
        //print_r($_POST);
        $this->db->Add(DBPREFIX.'message',$_POST);
        echo common::MsgShow(1,'当前信息您已经成功保存','./?act=message');
        exit();
    }
}
