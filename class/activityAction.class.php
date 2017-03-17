<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 17:43
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}
class activityAction extends base
{
    public function index()
    {
    //条件语句 和 导航位置
        $WStr='';$NavSeat='';
        $typeid=isset($_GET['typeid'])?abs($_GET['typeid']):'';
        if(!empty($typeid))
        {
            $WStr='Activity_Type="'.$typeid.'" And ';

            $CurrentActivityType=$this->GetActivityTypeName($typeid);
            $NavSeat=' -> '.$CurrentActivityType['Type_Name'];
        }
        $this->smarty->assign('NavSeat',$NavSeat);//新闻导航位置
       //翻页调用
        $Page=new Page;
        $this->SetPageConfig($Page,'activity',$WStr.'Activity_State="1"',9);
        $Where=array();
        $Where['Fields']='Activity_ID,Activity_Title,Activity_Type,Activity_Time';
        $Where['Order']='Activity_Sort Desc,Activity_ID Desc';
        $Where['Where']=$WStr.'Activity_State="1"';
        $Where['Limit']=$Page->CurrentStartSeat().',9';
        $ActivityList=$this->db->All(DBPREFIX.'activity',$Where);
        //print_r($ActivityList);
        if(is_array($ActivityList))
        {
            foreach($ActivityList as $key =>$value)
            {
                $ActivityType=$this->GetActivityTypeName($value['Activity_Type']);
                $ActivityList[$key]['Activity_TypeName']=!empty($ActivityType['Type_Name'])?$ActivityType['Type_Name']:'未分类';
            }
        }
        $this->smarty->assign('ActivityList',$ActivityList);
        //调用新闻类型列表
        $this->smarty->assign('GetActivityTypeList',$this->GetActivityTypeList());
        //调用新闻导航下广告图片位置
        $this->smarty->assign('ActivityPosterSeat',$this->PublicPosterCall('7',1001,206));
        $this->smarty->display('activity/activitylist.html');
    }
    private function GetActivityTypeList()
    {
        $Where=array();
        $Where['Fields']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';
        $List=$this->db->All(DBPREFIX.'activitytype',$Where);
        return $List;
    }
    private function GetActivityTypeName($ActivityTypeID)
    {
        $Where=array();
        $Where['Fields']='Type_Name';
        $Where['Where']='Type_State="1" And Type_ID="'.$ActivityTypeID.'"';
        $List=$this->db->GetOne(DBPREFIX.'activitytype',$Where);
        return $List;
    }
    public function view()
    {
    $id=isset($_GET['id'])?(int)$_GET['id']:'';
    //echo $id;
    $Where=array();
    $Where['Where']='Activity_State="1" And Activity_ID="'.$id.'"';
    $ActivityInfo=$this->db->GetOne(DBPREFIX.'activity',$Where);
    if(!empty($ActivityInfo['Activity_Type']))
    {
        $TypeInfo=$this->GetActivityTypeName($ActivityInfo['Activity_Type']);
        //print_r($TypeInfo['Type_Name']);
        $ActivityInfo['Activity_TypeName']=empty($TypeInfo['Type_Name'])?'':' - >'.$TypeInfo['Type_Name'];
    }
            $this->smarty->assign('ActivityInfo',$ActivityInfo);

            //浏览次数追加每点击一次追加一个（数据库中）
            $SQL='UPDATE'.DBPREFIX.'activity SET Activity_Hits=Activity_Hits+1 WHERE Activity_ID="'.$id.'"';
            $this->db->query($SQL);

            //调用新闻分类列表
            $this->smarty->assign('GetActivityTypeList',$this->GetActivityTypeList());
    //调用上一篇
    $this->smarty->assign('UpOneList',$this->UpOneList($id));
    //调用下一篇
    $this->smarty->assign('NextOneList',$this->NextOneList($id));


    //调用新闻导航下广告图片位置
    $this->smarty->assign('ActivityPosterSeat',$this->PublicPosterCall('7',1001,206));
    $this->smarty->display('activity/activityview.html');
}
    public function UpOneList($id)
    {
        $Where=array();
        $Where['Fields']='Activity_ID,Activity_Title';
        $Where['Where']='Activity_ID<'.$id;
        $Where['Order']='Activity_Sort Desc,Activity_ID Desc';
        $Info=$this->db->GetOne(DBPREFIX.'activity',$Where);
        return  $Info;
    }
    public function NextOneList($id)
    {
        $Where=array();
        $Where['Fields']='Activity_ID,Activity_Title';
        $Where['Where']='Activity_ID>'.$id;
        $Where['Order']='Activity_Sort Asc,Activity_ID Asc';
        $Info=$this->db->GetOne(DBPREFIX.'activity',$Where);
        return  $Info;
    }

}