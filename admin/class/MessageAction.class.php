<?php
/**
 * Created by PhpStorm.
 * User: Messageistrator
 * Date: 2016/4/7
 * Time: 22:30
 */
if(!defined('VISITSTATE')){echo 'visit errot';exit();}
class MessageAction extends Base
{
    //列表显示
    public function Index()
    {
        //获取
        $Key=isset($_GET['Key'])?trim($_GET['Key']):'';
        $p=isset($_GET['p'])?(int)$_GET['p']:'1';
        $ListCount=$this->AllCount('message',$Key,'Message_Title');
        $Page=new Page;
        $Page->Home='<';
        $Page->End='>';
        $Page->PageCount=ADMINLISTCOUNT;
        $Page->CurrentPage=$p;
        $Page->ListCount=$ListCount;
        $Show=$Page->ShowPage('H,X,E');

        $Where=array();
        $Where['Fields']='Message_ID,Message_Sort,Message_Title,Message_Name,Message_Mobile,Message_QQ,Message_Time,Message_State';
        $Where['Where']='Message_Title like "%'.$Key.'%"';
        $Where['Order']='Message_Sort Desc,Message_ID Desc';
        $Where['Limit']=$Page->CurrentStartSeat().','.ADMINLISTCOUNT;
        $List=$this->db->all(DBPREFIX.'message',$Where);
//        echo '<pre>';
        //print_r($List);
        include('./template/message/index.html');
    }
    //添加界面显示
    public function Add()
    {
        include('./template/message/add.html');
    }

    //保存添加的内容
    public function AddSave()
    {
        $_POST['Message_Time']=time();
        $State=$this->db->add(DBPREFIX.'message',$_POST);
        if($State)
        {
            echo Common::MsgShow(1,'内容添加成功。','./?Action=Message');
            exit();
        }else{
            echo Common::MsgShow(2,'当前内容添加失败！');
            exit();
        }
    }

    //修改数据
    public function Edit()
    {
        $ID=(int)$_GET['ID'];
        $Where=array();
        $Where['Where']='Message_ID="'.$ID.'"';
        $Info=$this->db->getOne(DBPREFIX.'message',$Where);
        //echo '<pre>';
        //print_r($Info);
        include('./template/message/edit.html');
    }

    //修改保存数据
    public function EditSave()
    {
        $ID=(int)$_POST['Message_ID'];
        $State=$this->db->editSave(DBPREFIX.'message',$_POST,'Message_ID="'.$ID.'"');
        if($State)
        {
            echo Common::MsgShow(1,'此内容更新成功。','./?Action=Message');
            exit();
        }else{
            echo Common::MsgShow(2,'此内容更新失败！');
            exit();
        }
    }

    //批量更新数据
    public function SortAffirm()
    {
        //echo '<pre>';
        //print_r($_POST);
        $CommonID=isset($_POST['CommonID'])?$_POST['CommonID']:'';
        $Message_Sort=isset($_POST['Message_Sort'])?$_POST['Message_Sort']:'';

        //print_r($CommonID);
        //print_r($Message_Sort);
        if(is_array($CommonID))
        {
            foreach($CommonID as $key => $val)
            {
                $TempArr=array();
                $TempArr['Message_Sort']=$Message_Sort[$key];
                $this->db->editSave(DBPREFIX.'message',$TempArr,'Message_ID="'.$val.'"');
            }
        }
        //exit();
        echo Common::MsgShow(1,'此记录已更新成功。','./?Action=Message');
        exit();
    }

    //删除数据
    public function Del()
    {
        $ID=isset($_GET['ID'])?(int)$_GET['ID']:'';
        if(!empty($ID))
        {
            $this->db->del(DBPREFIX.'message','Message_ID="'.$ID.'"');
        }

//        echo '<pre>';
//        print_r($_POST['Del_ID']);
        $DelArr=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        if(is_array($DelArr))
        {
            $DelStr=implode(',',$DelArr);
            $this->db->del(DBPREFIX.'message','Message_ID in ('.$DelStr.')',false);
        }

        echo Common::MsgShow(1,'此记录已删除成功。','./?Action=Message');
        exit();
    }
    public function See()
    {

        $Where=array();
        $Where['Fileds']='Message_ID,Message_Sort,Message_Title,Message_Name,Message_Mobile,Message_QQ,Message_Time,Message_State';
        $ID=isset($_POST['ID'])?$_POST['ID']:'';
        if(!empty($ID))
        {
            $Where['Where']='Message_ID="'.$ID.'"';
        }
        $Info=$this->db->GetOne(DBPREFIX.'message',$Where);
        //print_r($Info);
        include('./template/message/see.html');
    }
}