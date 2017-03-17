<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/5
 * Time: 14:12
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class PosterTypeAction extends Base
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
        $List=$this->db->All(DBPREFIX.'postertype',$Where);
        include('./template/type/postertype.html');
    }
    public function AddSave()
    {
       $_POST['Type_Time']=time();
       //print_r($_POST);
       $this->db->Add(DBPREFIX.'postertype',$_POST);
       echo common::MsgShow(1,'添加信息成功','./?Action=PosterType');
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

                $this->db->EditSave(DBPREFIX.'postertype',$TemperArr,$Where);
            }
            echo Common::MsgShow(1,'当前信息更新修改成功','./?Action=PosterType');
            }
    }
    public function Del()
    {

        $Del_ID=isset($_POST['Del_ID'])?$_POST['Del_ID']:'';
        $Where=implode(',',$Del_ID);

        $this->db->Del(DBPREFIX.'postertype','Type_ID in('.$Where.')',false);
        echo common::MsgShow(1,'删除成功','./?Action=PosterType');
    }
}