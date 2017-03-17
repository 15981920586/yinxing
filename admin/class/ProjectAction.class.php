<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class ProjectAction extends Base
{
    public function Index()
    {
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('project',$Key,'Project_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='Project_ID,Project_Title,Project_Sort,Project_Time,Project_State,Project_EnglishTitle,Project_PicPathBig,Project_PicPathSmall';
        if(!empty($Key))
        {
            $Where['Where']='Project_Title like "%'.$Key.'%"';
        }
        $Where['Order']='Project_Sort Desc,Project_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'project',$Where);
        //echo '<pre>';
//print_r($List);
       /* if(is_array($List))
        {
            foreach($List as $key => $value)
            {
                //echo '<pre>';
                //获取当前分类这个方法的名称
                $Type_Name=$this->GetProjectTypeName($value['Project_Type']);
                $List[$key]['Project_Type']=empty($Type_Name)?'未选择':$Type_Name;
               // print_r($List);
            }
        }*/

        include('./template/project/index.html');
    }
    public function Add()
    {
        //$List=$this->GetProjectType();
        //print_r($List);
        //exit();
        include('./template/project/add.html');
    }
    public function AddSave()
    {
        //print_r($_POST);
        $Pic=$this->PicUpLoad($_FILES);
       //print_r($Pic);

        $_POST['Project_Time']=time();
        //echo '<pre>';
        //print_r($_POST);

        //*$ID=(int)$_POST['Project_ID'];
        $State=$this->db->Add(DBPREFIX.'project',$_POST);
        //exit();
        if($State)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=Project');
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
        $Where['Where']='Project_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'project',$Where);
        //$List=$this->GetProjectType();
        include('./template/project/edit.html');
    }
    public function EditSave()
    {
        print_r($_POST);

       $ID=isset($_POST['Project_ID'])?$_POST['Project_ID']:'';
       $this->PicUpLoad($_FILES);
       $State= $this->db->EditSave(DBPREFIX.'project',$_POST,'Project_ID="'.$ID.'"');
       //print_r($State);
       //exit();
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=Project');
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
        $Project_Sort=$_POST['Project_Sort'];
       if(is_array($CommonID))
       {
           //print_r($CommonID);
          // print_r($Project_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['Project_Sort']=$Project_Sort[$key];
               $this->db->EditSave(DBPREFIX.'project',$TempArr,'Project_ID="'.$value.'"');
            }
       }

       echo Common::MsgShow(1,'此记录已经更新成功','./?Action=Project');
       exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='Project_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'project',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'project','Project_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'project','Project_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Project');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'project','Project_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Project');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
   }
//    public function GetProjectType()
//    {
//        $Where=array();
//        $Where['Fileds']='Type_ID,Type_Name';
//        $Where['Order']='Type_Sort Asc,Type_ID Asc';
//        $Where['Where']='Type_State="1"';
//
//        /*新闻表中的添加，其中，$List是基于 新闻分类表 的数组 包括ID和分类名称
//         页面在ProjectAction.class.php中的GetProjectType()方法*/
//        $List=$this->db->All(DBPREFIX.'projecttype',$Where);
//        return  $List;
//    }
//
   // public function GetProjectTypeName($ProjectTypeID)
//    {
//        $Where=array();
//        $Where['Fileds']='Type_ID,Type_Name';
//        $Where['Where']='Type_ID="'.$ProjectTypeID.'"';
//        $Info=$this->db->GetOne(DBPREFIX.'projecttype',$Where);
//        return $Info['Type_Name'];
//    }


    //图片上传方法调用
    private function PicUpLoad($_FILES)
    {
        //print_r($_FILES);
        //exit();
        if(!empty($_FILES['Project_PicPath']['tmp_name']))
        {
            $UpLoadFile=new UpLoadFile;
            $UpLoadFile->picFormat=array('jpg','jpeg','gif','png');
            $UpLoadFile->picSize=1*1024*1024;   //可上传1M的图片
            $UpLoadFile->picCommon='../uploadfile';
            $UpLoadFile->thumbnailStart='ON';
            $UpLoadFile->thumbnailWidth='224';
            $UpLoadFile->thumbnailHeight='140';
            $ImageInfo=$UpLoadFile->ImgSave($_FILES['Project_PicPath']);
            //echo '<hr>';
            //print_r($ImageInfo);
            if($ImageInfo['State']==0)
            {
                $_POST['Project_PicPathBig']=$ImageInfo['PicPath']['big'];
                $_POST['Project_PicPathSmall']=$ImageInfo['PicPath']['small'];
            }


            return $_POST;
        }
    }
}