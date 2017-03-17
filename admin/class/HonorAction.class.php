<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class HonorAction extends Base
{
    public function Index()
    {
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('honor',$Key,'Honor_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='Honor_ID,Honor_Title,Honor_Sort,Honor_Content,Honor_Time,Honor_State,Honor_PicPathBig,Honor_PicPathSmall';
        if(!empty($Key))
        {
            $Where['Where']='Honor_Title like "%'.$Key.'%"';
        }
        $Where['Order']='Honor_Sort Asc,Honor_ID Asc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'honor',$Where);
        //echo '<pre>';
//print_r($List);
        /*if(is_array($List))
        {
            foreach($List as $key => $value)
            {
                //echo '<pre>';
                //获取当前分类这个方法的名称
                $Type_Name=$this->GetHonorTypeName($value['Honor_Type']);
                $List[$key]['Honor_Type']=empty($Type_Name)?'未选择':$Type_Name;
               // print_r($List);
            }
        }*/

        include('./template/honor/index.html');
    }
    public function Add()
    {
        //$List=$this->GetHonorType();
        //print_r($List);
        //exit();
        include('./template/honor/add.html');
    }
    public function AddSave()
    {
        //print_r($_POST);
        $Pic=$this->PicUpLoad($_FILES);
       //print_r($Pic);

        $_POST['Honor_Time']=time();
        //echo '<pre>';
        //print_r($_POST);

        //*$ID=(int)$_POST['Honor_ID'];
        $State=$this->db->Add(DBPREFIX.'honor',$_POST);
        //exit();
        if($State)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=Honor');
            exit();
        }else{
                echo Common::MsgShow(2,'此条内容添加失败');
                exit();
            }
    }
    public function Edit()
    {

        $ID=(int)$_GET['ID'];
        $Where=array();
        $Where['Where']='Honor_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'honor',$Where);
        //$List=$this->GetHonorType();
        include('./template/honor/edit.html');
    }
    public function EditSave()
    {
        //print_r($_POST);

       $ID=isset($_POST['Honor_ID'])?$_POST['Honor_ID']:'';
       $this->PicUpLoad($_FILES);
       $State= $this->db->EditSave(DBPREFIX.'honor',$_POST,'Honor_ID="'.$ID.'"');
       //print_r($State);
       //exit();
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=Honor');
            exit();
        }else{
            echo Common::MsgShow(2,'此条内容修改失败');
            exit();
        }

    }
    public function SortAffirm()
    {
        //print_r($_POST);
    $CommonID=$_POST['CommonID'];
    $Honor_Sort=$_POST['Honor_Sort'];
       if(is_array($CommonID))
       {
           //print_r($CommonID);
          // print_r($Honor_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['Honor_Sort']=$Honor_Sort[$key];
               $this->db->EditSave(DBPREFIX.'honor',$TempArr,'Honor_ID="'.$value.'"');
            }
       }

       echo Common::MsgShow(1,'此记录已经更新成功','./?Action=Honor');
       exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='Honor_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'honor',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'honor','Honor_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'honor','Honor_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Honor');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'honor','Honor_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Honor');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
   }


    //图片上传方法调用
    private function PicUpLoad($_FILES)
    {
        //print_r($_FILES);
        //exit();
        if(!empty($_FILES['Honor_PicPath']['tmp_name']))
        {
            $UpLoadFile=new UpLoadFile;
            $UpLoadFile->picFormat=array('jpg','jpeg','gif','png');
            $UpLoadFile->picSize=1*1024*1024;   //可上传1M的图片
            $UpLoadFile->picCommon='../uploadfile';
            $UpLoadFile->thumbnailStart='ON';
            $UpLoadFile->thumbnailWidth='224';
            $UpLoadFile->thumbnailHeight='140';
            $ImageInfo=$UpLoadFile->ImgSave($_FILES['Honor_PicPath']);
            //echo '<hr>';
            //print_r($ImageInfo);
            if($ImageInfo['State']==0)
            {
                $_POST['Honor_PicPathBig']=$ImageInfo['PicPath']['big'];
                $_POST['Honor_PicPathSmall']=$ImageInfo['PicPath']['small'];
            }


            return $_POST;
        }
    }
}