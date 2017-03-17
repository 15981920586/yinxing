<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 17:43
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}
class newsAction extends base
{
    public function index()
    {
    //条件语句 和 导航位置
        $WStr='';$NavSeat='';
        $typeid=isset($_GET['typeid'])?abs($_GET['typeid']):'';
        if(!empty($typeid))
        {
            $WStr='News_Type="'.$typeid.'" And ';

            $CurrentNewsType=$this->GetNewsTypeName($typeid);
            $NavSeat=' -> '.$CurrentNewsType['Type_Name'];
        }
        $this->smarty->assign('NavSeat',$NavSeat);//新闻导航位置

       //翻页调用
        $Page=new Page;
        $this->SetPageConfig($Page,'news',$WStr.'News_State="1"',12);

        $Where=array();
        $Where['Fields']='News_ID,News_Title,News_Type,News_Time';
        $Where['Order']='News_Sort Desc,News_ID Desc';
        $Where['Where']=$WStr.'News_State="1"';
        $Where['Limit']=$Page->CurrentStartSeat().',12';
        $NewsList=$this->db->All(DBPREFIX.'news',$Where);
        //echo '<pre>';
        //print_r($NewsList);

        if(is_array($NewsList))
        {
            foreach($NewsList as $key =>$value)
            {
                $NewsType=$this->GetNewsTypeName($value['News_Type']);
                $NewsList[$key]['News_TypeName']=!empty($NewsType['Type_Name'])?$NewsType['Type_Name']:'未分类';
            }
        }

        $this->smarty->assign('NewsList',$NewsList);
        //调用新闻类型列表
        $this->smarty->assign('GetNewsTypeList',$this->GetNewsTypeList());
        //调用新闻导航下广告图片位置
        $this->smarty->assign('NewsPosterSeat',$this->PublicPosterCall('5',1001,206));

        $this->smarty->display('news/newslist.html');
    }
    private function GetNewsTypeList()
    {
        $Where=array();
        $Where['Fields']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';
        $List=$this->db->All(DBPREFIX.'newstype',$Where);
        return $List;
    }
    private function GetNewsTypeName($NewsTypeID)
    {
        $Where=array();
        $Where['Fields']='Type_Name';
        $Where['Where']='Type_State="1" And Type_ID="'.$NewsTypeID.'"';
        $List=$this->db->GetOne(DBPREFIX.'newstype',$Where);
        //echo '<pre>';
        //print_r($List);
        return $List;
    }
    public function view()
    {
    $id=isset($_GET['id'])?(int)$_GET['id']:'';
   // echo $id;
    $Where=array();
    $Where['Where']='News_State="1" And News_ID="'.$id.'"';
    $NewsInfo=$this->db->GetOne(DBPREFIX.'news',$Where);
    if(!empty($NewsInfo['News_Type']))
    {
        $TypeInfo=$this->GetNewsTypeName($NewsInfo['News_Type']);
        //print_r($TypeInfo['Type_Name']);
        $NewsInfo['News_TypeName']=empty($TypeInfo['Type_Name'])?'':' - >'.$TypeInfo['Type_Name'];
    }
            $this->smarty->assign('NewsInfo',$NewsInfo);

            //浏览次数追加，每点击一次追加一个（数据库中）
            $SQL='UPDATE'.DBPREFIX.'news SET News_Hits=News_Hits+1 WHERE News_ID="'.$id.'"';
            $this->db->query($SQL);

            //调用新闻分类列表
            $this->smarty->assign('GetNewsTypeList',$this->GetNewsTypeList());
    //调用上一篇
    $this->smarty->assign('UpOneList',$this->UpOneList($id));
    //调用下一篇
    $this->smarty->assign('NextOneList',$this->NextOneList($id));

    //调用新闻导航下广告图片位置
    $this->smarty->assign('NewsPosterSeat',$this->PublicPosterCall('5',1001,206));
    $this->smarty->display('news/newsview.html');
}
    public function UpOneList($id)
    {
        $Where=array();
        $Where['Fields']='News_ID,News_Title';
        $Where['Where']='News_ID<'.$id;
        $Where['Order']='News_Sort Desc,News_ID Desc';
        $Info=$this->db->GetOne(DBPREFIX.'news',$Where);
        return  $Info;
    }
    public function NextOneList($id)
    {
        $Where=array();
        $Where['Fields']='News_ID,News_Title';
        $Where['Where']='News_ID>'.$id;
        $Where['Order']='News_Sort Asc,News_ID Asc';
        $Info=$this->db->GetOne(DBPREFIX.'news',$Where);
        return  $Info;
    }

}