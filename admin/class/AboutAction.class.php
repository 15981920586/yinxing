<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 17:48
 */
class AboutAction extends Base
{
    //列表显示
    public function Index()
    {
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('about',$Key,'About_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='About_ID,About_Title,About_Sort,About_Time,About_State';
        $Where['Where']='About_Title like "%'.$Key.'%"';
        $Where['Order']='About_Sort Desc,About_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->All(DBPREFIX.'about',$Where);
//        echo '<pre>';
 //       print_r($List);
        include('./template/about/index.html');

    }
    public function Add()
    {
        include('./template/about/add.html');
    }
    public function AddSave()
    {
       //print_r($_POST);
        $_POST['About_Time']=time();
        $ID=isset($_POST['About_ID'])?(int)$_POST['About_ID']:'';
        $state=$this->db->Add(DBPREFIX.'about',$_POST,'About_ID="'.$ID.'"');
        if($state)
        {
            echo  Common::MsgShow(1,'您已经添加成功','./?Action=About');
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
        $Where['Where']='About_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'about',$Where);
        include('./template/about/edit.html');
    }
    public function EditSave()
    {
        //print_r($_POST);

        $ID=$_POST['About_ID'];
        $State= $this->db->EditSave(DBPREFIX.'about',$_POST,'About_ID="'.$ID.'"');
        if($State)
        {
            echo Common::MsgShow(1,'此条内容修改成功','./?Action=About');
        }else{
            echo Common::MsgShow(2,'此条内容修改失败');
        }

    }
    public function SortAffirm()
    {
        //print_r($_POST);
        $CommonID=isset($_POST['CommonID'])?$_POST['CommonID']:'';
        $About_Sort=isset($_POST['About_Sort'])?$_POST['About_Sort']:'';
        if(is_array($CommonID))
        {
           //print_r($CommonID);
          // print_r($About_Sort);
            foreach($CommonID as $key => $value)
            {
               $TempArr=array();
               $TempArr['About_Sort']=$About_Sort[$key];
               $this->db->EditSave(DBPREFIX.'about',$TempArr,'About_ID="'.$value.'"');
            }
       }
        echo Common::MsgShow(1,'此记录已经更新成功','./?Action=About');
        exit();
    }
    public function Del()
    {
        //错误的思路
        /*$Where=array();
          $Where['Where']='About_ID="'.$ID.'"';
          $Info=$this->db->Del(DBPREFIX.'about',$Where,'Limit=1');*/

        //删除单条数据 方法一
        //  $ID=(int)$_GET['ID'];
       //  $State= $this->db->Del(DBPREFIX.'about','About_ID="'.$ID.'"',$Limit=true);

        //删除单条数据 方法二
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $State=$this->db->Del(DBPREFIX.'about','About_ID="'.$ID.'"');
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=About');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }
        //print_r(112);     当走进此方法时无论打印什么都可以显示出来
        //当走进此类时，也是无论打印什么都可以显示出来。


        //批量删除 当limit为ture时只允许删除一条，为false时可以批量删除
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
           $DelStr=implode(',',$DelArr);
           $State=$this->db->Del(DBPREFIX.'about','About_ID in('.$DelStr.')',false);
            if($State)
            {
                echo Common::MsgShow(1,'此记录已删除成功','./?Action=About');
            }else{
                echo Common::MsgShow(2,'此记录删除失败');
            }
        }

   }
}