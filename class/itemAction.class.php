<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 17:43
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}
class itemAction extends base
{
    public function index()
    {
    //条件语句 和 导航位置
        $WStr='';$NavSeat='';
        $typeid=isset($_GET['typeid'])?abs($_GET['typeid']):'';
        if(!empty($typeid))
        {
            $WStr='Item_Type="'.$typeid.'" And ';

            $CurrentItemType=$this->GetItemTypeName($typeid);
            $NavSeat=' -> '.$CurrentItemType['Type_Name'];
        }
        $this->smarty->assign('NavSeat',$NavSeat);//新闻导航位置
       //翻页调用
        $Page=new Page;
        $this->SetPageConfig($Page,'item',$WStr.'Item_State="1"',9);
        $Where=array();
        $Where['Fields']='Item_ID,Item_Title,Item_Type,Item_Time';
        $Where['Order']='Item_Sort Desc,Item_ID Desc';
        $Where['Where']=$WStr.'Item_State="1"';
        $Where['Limit']=$Page->CurrentStartSeat().',9';
        $ItemList=$this->db->All(DBPREFIX.'item',$Where);
        //print_r($ItemList);
        if(is_array($ItemList))
        {
            foreach($ItemList as $key =>$value)
            {
                $ItemType=$this->GetItemTypeName($value['Item_Type']);
                $ItemList[$key]['Item_TypeName']=!empty($ItemType['Type_Name'])?$ItemType['Type_Name']:'未分类';
            }
        }
        $this->smarty->assign('ItemList',$ItemList);
        //调用新闻类型列表
        $this->smarty->assign('GetItemTypeList',$this->GetItemTypeList());
        //调用新闻导航下广告图片位置
        $this->smarty->assign('ItemPosterSeat',$this->PublicPosterCall('7',1001,206));
        $this->smarty->display('item/itemlist.html');
    }
    private function GetItemTypeList()
    {
        $Where=array();
        $Where['Fields']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';
        $List=$this->db->All(DBPREFIX.'itemtype',$Where);
        return $List;
    }
    private function GetItemTypeName($ItemTypeID)
    {
        $Where=array();
        $Where['Fields']='Type_Name';
        $Where['Where']='Type_State="1" And Type_ID="'.$ItemTypeID.'"';
        $List=$this->db->GetOne(DBPREFIX.'itemtype',$Where);
        return $List;
    }
    public function view()
    {
    $id=isset($_GET['id'])?(int)$_GET['id']:'';
    //echo $id;
    $Where=array();
    $Where['Where']='Item_State="1" And Item_ID="'.$id.'"';
    $ItemInfo=$this->db->GetOne(DBPREFIX.'item',$Where);
    if(!empty($ItemInfo['Item_Type']))
    {
        $TypeInfo=$this->GetItemTypeName($ItemInfo['Item_Type']);
        //print_r($TypeInfo['Type_Name']);
        $ItemInfo['Item_TypeName']=empty($TypeInfo['Type_Name'])?'':' - >'.$TypeInfo['Type_Name'];
    }
            $this->smarty->assign('ItemInfo',$ItemInfo);

            //浏览次数追加每点击一次追加一个（数据库中）
            $SQL='UPDATE'.DBPREFIX.'item SET Item_Hits=Item_Hits+1 WHERE Item_ID="'.$id.'"';
            $this->db->query($SQL);

            //调用新闻分类列表
            $this->smarty->assign('GetItemTypeList',$this->GetItemTypeList());
    //调用上一篇
    $this->smarty->assign('UpOneList',$this->UpOneList($id));
    //调用下一篇
    $this->smarty->assign('NextOneList',$this->NextOneList($id));


    //调用新闻导航下广告图片位置
    $this->smarty->assign('ItemPosterSeat',$this->PublicPosterCall('7',1001,206));
    $this->smarty->display('item/itemview.html');
}
    public function UpOneList($id)
    {
        $Where=array();
        $Where['Fields']='Item_ID,Item_Title';
        $Where['Where']='Item_ID<'.$id;
        $Where['Order']='Item_Sort Desc,Item_ID Desc';
        $Info=$this->db->GetOne(DBPREFIX.'item',$Where);
        return  $Info;
    }
    public function NextOneList($id)
    {
        $Where=array();
        $Where['Fields']='Item_ID,Item_Title';
        $Where['Where']='Item_ID>'.$id;
        $Where['Order']='Item_Sort Asc,Item_ID Asc';
        $Info=$this->db->GetOne(DBPREFIX.'item',$Where);
        return  $Info;
    }

}