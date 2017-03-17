<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
class NewsAction extends Base
{
    public function Index()
    {
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('news',$Key,'News_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='News_ID,News_Title,News_Sort,News_Time,News_State,News_Content,News_Author,News_Source,News_Type';
        if(!empty($Key))
        {
            $Where['Where']='News_Title like "%'.$Key.'%"';
        }
        $Where['Order']='News_Sort Desc,News_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'news',$Where);

        if(is_array($List))
        {
            foreach($List as $key => $value)
            {
                //echo '<pre>';
                //获取当前分类这个方法的名称
                $Type_Name=$this->GetNewsTypeName($value['News_Type']);
                $List[$key]['News_Type']=empty($Type_Name)?'未选择':$Type_Name;
               //print_r($List);
            }
        }

        include('./template/news/index.html');
    }
    public function Add()
    {
       $List=$this->GetNewsType();
        include('./template/news/add.html');
    }
    public function AddSave()
    {
        print_r($_POST);
        $_POST['News_Time']=time();
        //*$ID=(int)$_POST['News_ID'];
        $state=$this->db->Add(DBPREFIX.'news',$_POST);
        if($state)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=News');
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
        $Where['Where']='News_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'news',$Where);

        $List=$this->GetNewsType();
        include('./template/news/edit.html');
    }
    public function EditSave()
    {
       //print_r($_POST);
        //exit();
       $ID=isset($_POST['News_ID'])?$_POST['News_ID']:'';
       $State= $this->db->EditSave(DBPREFIX.'news',$_POST,'News_ID="'.$ID.'"');
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=News');
        }else{
            echo Common::MsgShow(2,'此条内容修改失败');
        }

    }
    public function SortAffirm()
    {
        //print_r($_POST);
    $CommonID=$_POST['CommonID'];
    $News_Sort=$_POST['News_Sort'];
       if(is_array($CommonID))
       {
           //print_r($CommonID);
          // print_r($News_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['News_Sort']=$News_Sort[$key];
               $this->db->EditSave(DBPREFIX.'news',$TempArr,'News_ID="'.$value.'"');
            }
       }

       echo Common::MsgShow(1,'此记录已经更新成功','./?Action=News');
       exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='News_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'news',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'news','News_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'news','News_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=News');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'news','News_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=News');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
   }
    //获取新闻分类
    public function GetNewsType()
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';

        /*新闻表中的添加，其中，$List是基于 新闻分类表 的数组 包括ID和分类名称
         页面在NewsAction.class.php中的GetNewsType()方法*/
        $List=$this->db->All(DBPREFIX.'newstype',$Where);
        return  $List;
    }
    public function GetNewsTypeName($NewsTypeID)
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Where']='Type_ID="'.$NewsTypeID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'newstype',$Where);
        return $Info['Type_Name'];
    }
}