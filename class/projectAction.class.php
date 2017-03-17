<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 14:54
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class projectAction extends base
{
    public function index()
    {
        $id=isset($_GET['id'])?(int)$_GET['id']:'';//多个id用三目
        $Where=array();
        $Where['Fields']='Project_Content,Project_Title';
        $Where['Where']='Project_State="1" And Project_ID="'.$id.'"';
        $Info=$this->db->GetOne(DBPREFIX.'project',$Where);
        $this->smarty->assign('ProjectInfo',$Info);
//print_r($Info);

        $this->smarty->assign('ProjectTopPoster',$this->PublicPosterCall('4','1001','206'));
        $this->smarty->display('./project/project.html');
    }
}
