<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class PosterAction extends Base
{
    public function Index()
    {
        //echo '123';
        //exit;
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';//当前页
        $ListCount=$this->AllCount('poster',$Key,'Poster_Title');//从数据库查出总数据条数
        $Page=new Page;//对类的实例化
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;//每页显示数据条数
        $Page->CurrentPage=$p;//当前页
        $Page->ListCount=$ListCount;//从数据库查出总数据条数
        $Show=$Page->ShowPage('H,X,E');
        //print_r($Show);
        $Where=array();
        /*$Where['Fields']='Poster_ID,Poster_Sort,Poster_Type,
      Poster_Title,Poster_Url,Poster_Time,Poster_State';*/
        //$Where['Fields']='';表示显示所有字段
        if(!empty($Key))
        {
            $Where['Where']='Poster_Title like "%'.$Key.'%"';
        }
        $Where['Order']='Poster_Sort Desc,Poster_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'poster',$Where);
//        if(empty($List))
//        {
//            $List='';
//        }
        //echo '<pre>';
       //print_r($List);
        if(is_array($List))
        {
            foreach($List as $key => $value)
            {
                //echo '<pre>';
                //获取当前分类这个方法的名称
                $Type_Name=$this->GetPosterTypeName($value['Poster_Type']);
                $List[$key]['Poster_Type']=empty($Type_Name)?'未选择':$Type_Name;
               // print_r($List);
            }
        }

        include('./template/poster/index.html');
    }
    public function Add()
    {
        $List=$this->GetPosterType();
        include('./template/poster/add.html');
    }
    public function AddSave()
    {
        //print_r($_POST);
        $Pic=$this->PicUpLoad($_FILES);
       //print_r($Pic);

        $_POST['Poster_Time']=time();
        //echo '<pre>';
        //print_r($_POST);

        //*$ID=(int)$_POST['Poster_ID'];
        $state=$this->db->Add(DBPREFIX.'poster',$_POST);
        //exit();
        if($state)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=Poster');
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
        $Where['Where']='Poster_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'poster',$Where);
        $List=$this->GetPosterType();
        include('./template/poster/edit.html');
    }
    public function EditSave()
    {
        //print_r($_POST);

       $ID=$_POST['Poster_ID'];
       $this->PicUpLoad($_FILES);
       $State= $this->db->EditSave(DBPREFIX.'poster',$_POST,'Poster_ID="'.$ID.'"');
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=Poster');
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
    $Poster_Sort=$_POST['Poster_Sort'];
       if(is_array($CommonID))
       {
           //print_r($CommonID);
          // print_r($Poster_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['Poster_Sort']=$Poster_Sort[$key];
               $this->db->EditSave(DBPREFIX.'poster',$TempArr,'Poster_ID="'.$value.'"');
            }
       }

       echo Common::MsgShow(1,'此记录已经更新成功','./?Action=Poster');
       exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='Poster_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'poster',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'poster','Poster_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        //$ID=(int)$_GET['ID'];

        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        //echo $ID;

        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'poster','Poster_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Poster');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';

        //print_r($DelArr);

        //exit();
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'poster','Poster_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Poster');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
   }
    public function GetPosterType()
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';

        /*新闻表中的添加，其中，$List是基于 新闻分类表 的数组 包括ID和分类名称
         页面在PosterAction.class.php中的GetPosterType()方法*/
        $List=$this->db->All(DBPREFIX.'postertype',$Where);
        return  $List;
    }
    public function GetPosterTypeName($PosterTypeID)
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Where']='Type_ID="'.$PosterTypeID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'postertype',$Where);
        return $Info['Type_Name'];
    }

    //图片上传方法调用
    private function PicUpLoad($_FILES)
    {
        //print_r($_FILES);
        //exit();
        if(!empty($_FILES['Poster_PicPath']['tmp_name']))
        {
            $UpLoadFile=new UpLoadFile;
            $UpLoadFile->picFormat=array('jpg','jpeg','gif','png');
            $UpLoadFile->picSize=500*1024;   //可上传1M的图片
            $UpLoadFile->picCommon='../uploadfile';
            $UpLoadFile->thumbnailStart='OFF';
            //$UpLoadFile->thumbnailWidth='224';
            //$UpLoadFile->thumbnailHeight='140';
            $ImageInfo=$UpLoadFile->ImgSave($_FILES['Poster_PicPath']);
            //echo '<hr>';
            //print_r($ImageInfo);
            if($ImageInfo['State']==0)
            {
                $_POST['Poster_PicPathBig']=$ImageInfo['PicPath']['big'];
                //$_POST['Poster_PicPathSmall']=$ImageInfo['PicPath']['small'];
            }
             return $_POST;
        }
    }
}