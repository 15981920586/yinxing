<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 14:54
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class contactAction extends base
{
    public function index()
    {
        $id=isset($_GET['id'])?(int)$_GET['id']:'115';//这个ID是在 后台 关于我们 中获取 联系我们的ID值
        //下面都是关于我们的内容和表，因为内容都一样，所以直接复制关于我们的
        $Where=array();
        $Where['Fields']='About_Content,About_Title';
        $Where['Where']='About_State="1" And About_ID="'.$id.'"';
        $Info=$this->db->GetOne(DBPREFIX.'about',$Where);
        $this->smarty->assign('AboutInfo',$Info);
//print_r($Info);

        $this->smarty->assign('AboutTopPoster',$this->PublicPosterCall('4','1001','206'));
        $this->smarty->display('./contact/contact.html');//对关于我们 类文件 修改了 在哪里呈现（联系我们）
    }
}
