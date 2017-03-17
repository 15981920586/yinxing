<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 14:54
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class aboutAction extends base
{
    public function index()
    {
        $id=isset($_GET['id'])?(int)$_GET['id']:'112';
        $Where=array();
        $Where['Fields']='About_Content,About_Title';
        $Where['Where']='About_State="1" And About_ID="'.$id.'"';
        $Info=$this->db->GetOne(DBPREFIX.'about',$Where);
        $this->smarty->assign('AboutInfo',$Info);
//print_r($Info);

        $this->smarty->assign('AboutTopPoster',$this->PublicPosterCall('4','1001','206'));
        $this->smarty->display('./about/about.html');
    }
}
