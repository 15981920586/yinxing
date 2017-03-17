<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/13
 * Time: 17:35
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}

class honorAction extends base
{
    public function index()
    {

        $Page=new Page;
        $this->SetPageConfig($Page,'honor','Honor_State="1"',9);

        $Where=array();
        $Where['Fields']='Honor_ID,Honor_Title,Honor_PicPathSmall';
        $Where['Where']='Honor_State="1"';
        $Where['Order']='Honor_Sort Desc,Honor_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().',9';
        $HonorList=$this->db->All(DBPREFIX.'honor',$Where);
        $this->smarty->assign('HonorList',$HonorList);


        $this->smarty->assign('HonorPosterSeat',$this->PublicPosterCall('12',1000,206));

        $this->smarty->display('honor/honorlist.html');
    }
    //创建企业荣誉的详情页方法
    public function view()
    {
        $id=isset($_GET['id'])?(int)$_GET['id']:'';
        //echo $id;
        $Where=array();
        $Where['Fields']='Honor_Title,Honor_Content,Honor_PicPathBig';
        $Where['Where']='Honor_State="1" And Honor_ID="'.$id.'"';

        $HonorInfo=$this->db->GetOne(DBPREFIX.'honor',$Where);
        $this->smarty->assign('HonorInfo',$HonorInfo);
        $this->smarty->assign('HonorPosterSeat',$this->PublicPosterCall('12',1000,206));
        $this->smarty->display('honor/honorview.html');

    }


}