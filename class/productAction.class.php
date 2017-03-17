<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 17:43
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}
class productAction extends base
{
    public function index()
    {
    //条件语句 和 导航位置
        $WStr='';$NavSeat='';
        $typeid=isset($_GET['typeid'])?abs($_GET['typeid']):'';
        if(!empty($typeid))
        {
            $WStr='Product_Type="'.$typeid.'" And ';

            $CurrentProductType=$this->GetProductTypeName($typeid);
            $NavSeat=' -> '.$CurrentProductType['Type_Name'];
        }
        $this->smarty->assign('NavSeat',$NavSeat);//新闻导航位置

       //翻页调用
        $Page=new Page;
        $this->SetPageConfig($Page,'product',$WStr.'Product_State="1"',9);

        $Where=array();
        $Where['Fields']='Product_ID,Product_Title,Product_Type,Product_Time,Product_PicPathSmall';
        $Where['Order']='Product_Sort Desc,Product_ID Desc';
        $Where['Where']=$WStr.'Product_State="1"';
        $Where['Limit']=$Page->CurrentStartSeat().',9';
        $ProductList=$this->db->All(DBPREFIX.'product',$Where);

        if(is_array($ProductList))
        {
            foreach($ProductList as $key =>$value)
            {
                $ProductType=$this->GetProductTypeName($value['Product_Type']);
                $ProductList[$key]['Product_TypeName']=!empty($ProductType['Type_Name'])?$ProductType['Type_Name']:'未分类';
            }
        }
        //print_r($ProductList);

        $this->smarty->assign('ProductList',$ProductList);
        //调用新闻类型列表
        $this->smarty->assign('GetProductTypeList',$this->GetProductTypeList());
        //调用新闻导航下广告图片位置
        $this->smarty->assign('ProductPosterSeat',$this->PublicPosterCall('5',1001,206));

        $this->smarty->display('product/productlist.html');
    }

    public function view()
    {

        $id=isset($_GET['id'])?(int)$_GET['id']:'';
        //echo $id;
        $Where=array();
        $Where['Where']='Product_State="1" And Product_ID="'.$id.'"';
        $ProductInfo=$this->db->GetOne(DBPREFIX.'product',$Where);
        if(!empty($ProductInfo['Product_Type']))
        {
            //调用GetProductTypeName（）方法中的产品信息（$ProductInfo）中的产品类型（Product_Type），并将结果附给变量$TypeInfo
            $TypeInfo=$this->GetProductTypeName($ProductInfo['Product_Type']);

            //判断要导航的名字（产品类型下的类型名字，由$TypeInfo得到）是不是空，为空的话给个空值，不为空的话加一个箭头和类型名字，即：->类型名
            //模板页对应productview页面的27行
            $ProductInfo['Product_NavTypeName']=empty($TypeInfo['Type_Name'])?'':' -> '.$TypeInfo['Type_Name'];

            //判断产品类型名字是不是为空，为空时：给个空值，不为空时：执行类型的名字，这样判断是为了和详情页面中34行的Product_TypeName
            //一一对应，类型名字不为空时给个名字（$TypeInfo['Type_Name']），然后把结果附给$ProductInfo['Product_TypeName']中的Product_TypeName
            $ProductInfo['Product_TypeName']=empty($TypeInfo['Type_Name'])?'':$TypeInfo['Type_Name'];
        }
        $this->smarty->assign('ProductInfo',$ProductInfo);

        //调用产品分类列表
        $this->smarty->assign('GetProductTypeList',$this->GetProductTypeList());

        //调用产品导航下广告图片位置
        $this->smarty->assign('ProductPosterSeat',$this->PublicPosterCall('6',1001,206));
        $this->smarty->display('product/productview.html');
    }

    private function GetProductTypeList()
    {
        $Where=array();
        $Where['Fields']='Type_ID,Type_Name';
        $Where['Order']='Type_Sort Asc,Type_ID Asc';
        $Where['Where']='Type_State="1"';
        $List=$this->db->All(DBPREFIX.'producttype',$Where);
        return $List;
    }
    private function GetProductTypeName($ProductTypeID)
    {
        $Where=array();
        $Where['Fields']='Type_Name';
        $Where['Where']='Type_State="1" And Type_ID="'.$ProductTypeID.'"';
        $List=$this->db->GetOne(DBPREFIX.'producttype',$Where);
        return $List;
    }

}