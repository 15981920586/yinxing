<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/2
 * Time: 9:51
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class AdminAction extends Base
{
    //列表显示
    public function Index()
    {
        //获取
        $Key = isset($_GET['Key']) ? trim($_GET['Key']) : '';
        $p = isset($_GET['p']) ? (int)$_GET['p'] : '1';
        $ListCount = $this->AllCount('admin', $Key, 'Admin_Name');
        $Page = new Page;
        $Page->Home = '<';
        $Page->End = '>';
        $Page->PageCount = ADMINLISTCOUNT;
        $Page->CurrentPage = $p;
        $Page->ListCount = $ListCount;
        $Show = $Page->ShowPage('H,X,E');

        $Where = array();
        $Where['Fields'] = 'Admin_ID,Admin_Name,Admin_Pwd,Admin_Time,Admin_State';
        $Where['Where'] = 'Admin_Name like "%' . $Key . '%"';
        $Where['Order'] = 'Admin_ID Asc';
        $Where['Limit'] = $Page->CurrentStartSeat() . ',' . ADMINLISTCOUNT;
        $List = $this->db->All(DBPREFIX . 'admin', $Where);
//        echo '<pre>';
//        print_r($List);
        include('./template/admin/index.html');
    }

    //添加界面显示
    public function Add()
    {
        include('./template/admin/add.html');
    }

    //保存添加的内容
    public function AddSave()
    {
        if ($this->AdminNameCheck('Admin_Name') > 0)
        {
            echo Common::MsgShow(2, '您的用户名已被占用');
            exit();
        }
        unset($_POST['Admin_AffirmPwd']);
        $_POST['Admin_Pwd'] = md5($_POST['Admin_Pwd'] . PASSWORDKEY);

        //print_r($_POST['Admin_Pwd']);
        //exit();
        $_POST['Admin_Time'] = time();
        $State = $this->db->Add(DBPREFIX . 'admin', $_POST);
        if ($State) {
            echo Common::MsgShow(1, '内容添加成功。', './?Action=Admin');
            exit();
        } else {
            echo Common::MsgShow(2, '当前内容添加失败！');
            exit();
        }
    }

    //修改数据
    public function Edit()
    {
        $ID = isset($_GET['ID'])?(int)$_GET['ID']:'';
        $Where = array();
        $Where['Where'] = 'Admin_ID="' . $ID . '"';
        $Info = $this->db->GetOne(DBPREFIX . 'admin', $Where);
        //echo '<pre>';
        //print_r($Info);
        include('./template/admin/edit.html');
    }

    //修改保存数据
    public function EditSave()
    {
        $ID =isset($_POST['Admin_ID'])?(int)$_POST['Admin_ID']:'';
        unset($_POST['Admin_AffirmPwd']);
        $_POST['Admin_Pwd'] = md5($_POST['Admin_Pwd'] . PASSWORDKEY);

        //print_r($_POST['Admin_Pwd']);

        $State = $this->db->EditSave(DBPREFIX . 'admin', $_POST, 'Admin_ID="' . $ID . '"');
        if ($State) {
            echo Common::MsgShow(1, '此内容更新成功。', './?Action=Admin');
            exit();
        } else {
            echo Common::MsgShow(2, '此内容更新失败！');
            exit();
        }
    }

    //批量更新数据
    public function SortAffirm()
    {
        //echo '<pre>';
        //print_r($_POST);
        $CommonID = isset($_POST['CommonID'])?$_POST['CommonID']:'';
        $Admin_Sort =isset($_POST['Admin_Sort'])?$_POST['Admin_Sort']:'';

        //print_r($CommonID);
        //print_r($Admin_Sort);
        if (is_array($CommonID)) {
            foreach ($CommonID as $key => $value) {
                $TempArr = array();
                $TempArr['Admin_Sort'] = $Admin_Sort[$key];
                $this->db->EditSave(DBPREFIX . 'admin', $TempArr, 'Admin_ID="' . $value . '"');
            }
        }
        //exit();
        echo Common::MsgShow(1, '此记录已更新成功。', './?Action=Admin');
        exit();
    }

    //删除数据
    public function Del()
    {
        $ID = isset($_GET['ID']) ? (int)$_GET['ID'] : '';
        if (!empty($ID)) {
            $this->db->Del(DBPREFIX . 'admin', 'Admin_ID="' . $ID . '"');
        }

//        echo '<pre>';
//        print_r($_POST['Del_ID']);
        $DelArr = isset($_POST['Del_ID']) ? $_POST['Del_ID'] : '';
        if (is_array($DelArr)) {
            $DelStr = implode(',', $DelArr);
            $this->db->Del(DBPREFIX . 'admin', 'Admin_ID in (' . $DelStr . ')', false);
        }

        echo Common::MsgShow(1, '此记录已删除成功。', './?Action=Admin');
        exit();
    }

    public function AdminNameCheck($State = 'json')
    {
        //isset相当于是初始化数据,双引号是字符串
        $AdminName = isset($_POST['Admin_Name'])?trim($_POST['Admin_Name']):'';
        if (!empty($AdminName)) {
            $Where = array();
            $Where['Fields'] = 'count(*) as c';
            $Where['Where'] = 'Admin_Name="' . $AdminName . '"';
            $Info = $this->db->GetOne(DBPREFIX . 'admin', $Where);
            if ($State == 'json') {

                $Arr = array();
                if (empty($Info['c']))
                {
                    $Arr['NO'] = 0;
                } else {
                    $Arr['NO'] = 1;
                }
                echo json_encode($Arr);
                //print_r($Arr);这两个输出是冲突的
            } else {
                return $Info['c'];
            }
        }
    }
}