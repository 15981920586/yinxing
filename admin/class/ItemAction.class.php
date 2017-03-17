<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
class ItemAction extends Base
{
    public function Index()
    {
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('item',$Key,'Item_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='Item_ID,Item_Title,Item_Sort,Item_Time,Item_State,Item_Author,Item_Source,Item_Type';
        if(!empty($Key))
        {
            $Where['Where']='Item_Title like "%'.$Key.'%"';
        }
        $Where['Order']='Item_Sort Desc,Item_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'item',$Where);

        if(is_array($List))
        {
            foreach($List as $key => $value)
            {
                //echo '<pre>';
                //获取当前分类这个方法的名称
                $Type_Name=$this->GetItemTypeName($value['Item_Type']);
                $List[$key]['Item_Type']=empty($Type_Name)?'未选择':$Type_Name;
               // print_r($List);
            }
        }

        include('./template/item/index.html');
    }
    public function Add()
    {
       $List=$this->GetItemType();
        include('./template/item/add.html');
    }
    public function AddSave()
    {
        //print_r($_POST);
        $_POST['Item_Time']=time();
        //*$ID=(int)$_POST['Item_ID'];
        $state=$this->db->Add(DBPREFIX.'item',$_POST);
        if($state)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=Item');
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
        $Where['Where']='Item_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'item',$Where);
        $List=$this->GetItemType();
        include('./template/item/edit.html');
    }
    public function EditSave()
    {
        //print_r($_POST);
       $ID=$_POST['Item_ID'];
       $State= $this->db->EditSave(DBPREFIX.'item',$_POST,'Item_ID="'.$ID.'"');
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=Item');
        }else{
            echo Common::MsgShow(2,'此条内容修改失败');
        }

    }
    public function SortAffirm()
    {
        //print_r($_POST);
    $CommonID=$_POST['CommonID'];
    $Item_Sort=$_POST['Item_Sort'];
       if(is_array($CommonID))
       {
           //print_r($CommonID);
          // print_r($Item_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['Item_Sort']=$Item_Sort[$key];
               $this->db->EditSave(DBPREFIX.'item',$TempArr,'Item_ID="'.$value.'"');
            }
       }

       echo Common::MsgShow(1,'此记录已经更新成功','./?Action=Item');
       exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='Item_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'item',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'item','Item_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'item','Item_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Item');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'item','Item_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=Item');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
   }
    public function GetItemType()
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';

        /*新闻表中的添加，其中，$List是基于 新闻分类表 的数组 包括ID和分类名称
         页面在ItemAction.class.php中的GetItemType()方法*/
        $List=$this->db->All(DBPREFIX.'itemtype',$Where);
        return  $List;
    }
    public function GetItemTypeName($ItemTypeID)
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Name';
        $Where['Where']='Type_ID="'.$ItemTypeID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'itemtype',$Where);
        return $Info['Type_Name'];
    }
}