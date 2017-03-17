<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/12
 * Time: 20:48
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}
class base extends Common
{
   public function __construct()
   {
      //当子类和父类都有construct构造函数时，子类会把父类覆盖掉，这时父类功能将不具备，此时需要对父类进行重载
      parent::__construct();//重载时用双下划线
      //把网站的基本信息（WebBaseInfo.php）包括进来，并用smarty模板标签去一一对应赋值，那个页面用到
      //网站的基本信息都需要调用一次，或者用全局的globle调用，此时只需把此文件（WebBaseInfo.php）放到公共文件类中即base中，子类继承即可，无需再
      //每次都写一次，不过这个globle是全局的，有安全风险，平时最好不用
      $WebBaseInfo=include('./common/WebBaseInfo.php');
      $this->smarty->assign('WebBaseInfo',$WebBaseInfo);
      //调用合作伙伴列表方法
      $this->smarty->assign('GetFooterPartnerList',$this->GetFooterPartnerList());
   }
   //对page翻页类 的调用和基本配置
   //$Page是定义的当前翻页类这个对象，相当于把翻页类这个对象引到了本类中进行调用
   //$TableName不同的表名，根据需要调用
   //$WhereStr条件语句参数
   //$PageCount每页显示多少条数据记录
   protected function SetPageConfig($Page,$TableName,$WhereStr,$PageCount=10)
   {
      //p是对当前页数是否存在的判断
      $p=isset($_GET['p'])?(int)$_GET['p']:1;
      //获取总记录数
      $Where=array();
      $Where['Fields']=' count(*) as c';
      $Where['Where']=$WhereStr;
      $CountList=$this->db->getOne(DBPREFIX.$TableName,$Where);

      //计算出来当前总页数   ceil只进而不舍
      $TotalPageCount=ceil($CountList['c']/$PageCount);

      //调用翻页类 ，//$Page是当前翻页类这个对象
      $Page->Home='[第一页]';
      $Page->Prev='[上一页]';
      $Page->Next='[下一页]';
      $Page->End='[最后一页]';

      $Page->PageCount=$PageCount;//PageCount每页显示多少条数据记录
      $Page->ListCount=$CountList['c'];//$Page调用属性总记录数
      $Page->CurrentPage=$p;//当前对象调用分页类中的属性并赋值给变量p，表示当前页
      $ShowPage=$Page->ShowPage('H,P,N,E');

      $this->smarty->assign('ShowPage',$ShowPage);
      $this->smarty->assign('CountList',$CountList['c']);//总记录数
      $this->smarty->assign('p',$p);//当前页
      $this->smarty->assign('TotalPageCount',$TotalPageCount);//总页数
   }
   //尾部合作伙伴列表调用
   protected function GetFooterPartnerList()
   {
      $Where=array();
      $Where['Fields']='Poster_Url,Poster_PicPathBig';
      $Where['Order']='Poster_Sort Desc,Poster_ID Desc';
      $Where['Where']='Poster_Type="3" And Poster_Class=1 And Poster_State=1';
      $Where['Limit']='10';
      $List=$this->db->All(DBPREFIX.'poster',$Where);
      return $List;
   }
   //广告的公共调用
   protected function PublicPosterCall($Wstr,$Width,$Height,$Count=1,$Class=1)
   {
      $Where=array();
      $Where['Fields']='Poster_Url,Poster_PicPathBig';
      $Where['Where']='Poster_Type="'.$Wstr.'" And Poster_Class="'.$Class.'" And Poster_State="1"';
      $Where['Order']='Poster_Sort Desc,Poster_ID Desc';
      $Where['Limit']=$Count;

      $List=$this->db->All(DBPREFIX.'poster',$Where);
      $Str='';
      if(is_array($List))
      {
         foreach($List as $key =>$value)
         {
            $Str.='<a href="'.$value['Poster_Url'].'" target="_blank"><img src="'.$value['Poster_PicPathBig'].'" width="'.$Width.'" height="'.$Height.'" /></a>';
         }
      }
      return $Str;
   }
}