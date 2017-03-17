<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class ProductAction extends Base
{
    public function Index()
    {
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('product',$Key,'Product_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='Product_ID,Product_Title,Product_Sort,Product_Source,Product_Time,Product_State,Product_Author,Product_Number,Product_PicPathBig,Product_PicPathSmall,Product_Type';
        if(!empty($Key))
        {
            $Where['Where']='Product_Title like "%'.$Key.'%"';
        }
        $Where['Order']='Product_Sort Desc,Product_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'product',$Where);
        //echo '<pre>';
//print_r($List);
        if(is_array($List))
        {
            foreach($List as $key => $value)
            {
                //echo '<pre>';
                //获取当前分类这个方法的名称
                $Type_Name=$this->GetProductTypeName($value['Product_Type']);
                $List[$key]['Product_Type']=empty($Type_Name)?'未选择':$Type_Name;
               // print_r($List);
            }
        }

        include('./template/product/index.html');
    }
    public function Add()
    {
        $List=$this->GetProductType();
        //print_r($List);
        //exit();
        include('./template/product/add.html');
    }
    public function AddSave()
    {
        //print_r($_POST);
        $Pic=$this->PicUpLoad($_FILES);
       //print_r($Pic);

        $_POST['Product_Time']=time();
        //echo '<pre>';
        //print_r($_POST);

        //*$ID=(int)$_POST['Product_ID'];
        $State=$this->db->Add(DBPREFIX.'product',$_POST);
        //exit();
        if($State)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=Product');
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
        $Where['Where']='Product_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'product',$Where);
         $List=$this->GetProductType();
        print_r($List);
        echo '1111';
        include('./template/product/edit.html');
    }
    public function EditSave()
    {
        //print_r($_POST);

       $ID=isset($_POST['Product_ID'])?$_POST['Product_ID']:'';
       $this->PicUpLoad($_FILES);
       $State= $this->db->EditSave(DBPREFIX.'product',$_POST,'Product_ID="'.$ID.'"');
       //print_r($State);
       //exit();
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=Product');
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
    $Product_Sort=$_POST['Product_Sort'];
       if(is_array($CommonID))
       {
           //print_r($CommonID);
          // print_r($Product_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['Product_Sort']=$Product_Sort[$key];
               $this->db->EditSave(DBPREFIX.'product',$TempArr,'Product_ID="'.$value.'"');
            }
       }

       echo Common::MsgShow(1,'此记录已经更新成功','./?Action=Product');
       exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='Product_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'product',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'product','Product_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'product','Product_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Product');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'product','Product_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Product');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
   }
    public function GetProductType()
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';

        /*新闻表中的添加，其中，$List是基于 新闻分类表 的数组 包括ID和分类名称
         页面在ProductAction.class.php中的GetProductType()方法*/
        $List=$this->db->All(DBPREFIX.'producttype',$Where);
        return  $List;
    }
    public function GetProductTypeName($ProductTypeID)
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Where']='Type_ID="'.$ProductTypeID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'producttype',$Where);
        return $Info['Type_Name'];
    }



    //图片上传方法调用
    private function PicUpLoad($_FILES)
    {
        //print_r($_FILES);
        //exit();
        if(!empty($_FILES['Product_PicPath']['tmp_name']))
        {
            $UpLoadFile=new UpLoadFile;
            $UpLoadFile->picFormat=array('jpg','jpeg','gif','png');
            $UpLoadFile->picSize=1*1024*1024;   //可上传1M的图片
            $UpLoadFile->picCommon='../uploadfile';
            $UpLoadFile->thumbnailStart='ON';
            $UpLoadFile->thumbnailWidth='224';
            $UpLoadFile->thumbnailHeight='140';
            $ImageInfo=$UpLoadFile->ImgSave($_FILES['Product_PicPath']);
            //echo '<hr>';
            //print_r($ImageInfo);
            if($ImageInfo['State']==0)
            {
                $_POST['Product_PicPathBig']=$ImageInfo['PicPath']['big'];
                $_POST['Product_PicPathSmall']=$ImageInfo['PicPath']['small'];
            }
            return $_POST;
        }
    }
}