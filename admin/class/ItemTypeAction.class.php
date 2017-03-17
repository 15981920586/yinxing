<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/5
 * Time: 14:12
 */
class ItemTypeAction extends Base
{
    public function Index()
    {
        $Where=array();
        $Where['Fileds']='Type_ID,Type_Sort,Type_Name,Type_State';

        //print_r($_GET['Key']);
        $Key=isset($_GET['Key'])?$_GET['Key']:'';
        if(!empty($Key))
        {
            $Where['Where']='Type_Name like "%'.$Key.'%"';
        }
        $List=$this->db->All(DBPREFIX.'itemtype',$Where);
        include('./template/type/itemtype.html');
    }
    public function AddSave()
    {
       $_POST['Type_Time']=time();
       print_r($_POST);
       $this->db->Add(DBPREFIX.'itemtype',$_POST);
       echo common::MsgShow(1,'添加信息成功','./?Action=ItemType');
    }
    public function EditSave()
    {
        $CommonID=$_POST['CommonID'];
        $Type_Sort=$_POST['Type_Sort'];
        $Type_Name=$_POST['Type_Name'];
        $Type_State=$_POST['Type_State'];

        if(is_array($CommonID))
        {
            foreach($CommonID as $key => $value)
            {
                $TemperArr=array();
                $TemperArr['Type_Sort']=$Type_Sort[$key];
                $TemperArr['Type_Name']=$Type_Name[$key];
                $TemperArr['Type_State']=$Type_State[$key];

                $Where='Type_ID="'.$value.'"';

                $this->db->EditSave(DBPREFIX.'itemtype',$TemperArr,$Where);
            }
            echo Common::MsgShow(1,'当前信息更新修改成功','./?Action=ItemType');
            }
    }
    public function Del()
    {
        $Del_ID=$_POST['Del_ID'];

        $Where=implode(',',$Del_ID);

        $this->db->Del(DBPREFIX.'itemtype','Type_ID in('.$Where.')',false);

    }
}