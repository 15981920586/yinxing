<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/11
 * Time: 15:23
 */
if(!defined('VISITSTATE')){echo 'error visit';exit();}
class defaultAction extends base
{
    public function index()
    {
        //注：顺序没有先后
        //一、把网站的基本信息（WebBaseInfo.php）包括进来，并用smarty模板标签去一一对应赋值
        $WebBaseInfo=include('./common/WebBaseInfo.php');
        $this->smarty->assign('WebBaseInfo',$WebBaseInfo);
        //print_r($WebBaseInfo);//打印是为了调试信息时排错
        //二、smarty模板引擎 调用 新闻列表方法，然后用smarty标签赋值给静态页中，替代了静态页面中php语句
        $NewsList=$this->GetNewsList(5);//调用GetNewsList()方法，并赋值给$NewsList
        $this->smarty->assign('NewsList',$NewsList);//把$NewsList赋值给NewsList
        //三、用smarty模板引擎调用 活动列表方法
        $ActivityList=$this->GetActivityList(5);
        $this->smarty->assign('ActivityList',$ActivityList);
        //四、用smarty模板引擎调用 项目列表方法
        $ItemList=$this->GetItemList(5);
        $this->smarty->assign('ItemList',$ItemList);
        //五、用smarty模板引擎调用 产品列表内容方法
        $ProductList=$this->GetProductList(3);
        $this->smarty->assign('ProductList',$ProductList);//把$ProductList赋值给ProductList,让ProductList进行smarty调用
        //六、用smarty模板引擎调用 底部合作伙伴方法
        $this->smarty->assign('GetFooterPartnerList',$this->GetFooterPartnerList());
        //七、首页导航下生成flash的 滚动图片调用方法
        $this->CreateFlashFile();
        //八、调用 银杏项目内容方法
        $this->smarty->assign('GetYinXingProjectList',$this->GetYinXingProjectList());
        //九、首页 招贤纳士广告图片，调用base中的公共广告调用方法PublicPosterCall，没有在本页面中继续写此方法，本页中其他的方法都可以用PublicPosterCall()方法调用，都可以不写的。
        $HomePosterZXNS=$this->PublicPosterCall(2,295,70);//首页广告招贤纳士
        $this->smarty->assign('HomePosterZXNS',$HomePosterZXNS);//变量$HomePosterZXNS赋值给smarty中的HomePosterZXNS标签，以便在静态html模板页中调用
        //十、用smarty模板引擎调用 关于我们内容列表的方法,assign是设置变量
        $this->smarty->assign('GetAboutContent',$this->GetAboutContent(112));//关于我们的内容中内容部分对应的具体ID

        $this->smarty->display('index.html');//指向具体模板位置并对其输出
    }

    //对新闻模块的调用（即从数据库中调出来）
    private function GetNewsList($Count=5)
    {
        //echo '12';
        $Where=array();
        $Where['Fields']='News_ID,News_Title';
        $Where['Order']='News_Sort Desc,News_ID Desc';
        $Where['Where']='News_Type="1"';
        $Where['Limit']=$Count;
        $List=$this->db->All(DBPREFIX.'news',$Where);
       // print_r($List);
       //exit();
        return $List;//结果返回给该方法
    }
    //对活动模块的调用（即从数据库中调出来）
    private function GetActivityList($Count=5)
    {
        $Where=array();
        $Where['Fields']='Activity_ID,Activity_Title';
        $Where['Where']='Activity_State="1"';
        $Where['Order']='Activity_Sort Desc,Activity_ID Desc';
        $Where['Limit']=$Count;
        $List=$this->db->All(DBPREFIX.'activity',$Where);
        return $List;
    }
    //对项目模块的调用（即从数据库中调出来）
    private function GetItemList($Count=5)
    {
        $Where=array();
        $Where['Fields']='Item_ID,Item_Title';
        $Where['Order']='Item_Sort Desc,Item_ID Desc';
        $Where['Where']='Item_State="1"';
        $Where['Limit']=$Count;
        $List=$this->db->All(DBPREFIX.'item',$Where);
        return $List;
    }
    //产品列表调用（即从数据库中调出来）
    private function GetProductList($Count=3)
    {
        $Where=array();
        $Where['Fields']='Product_ID,Product_PicPathSmall,Product_Title,Product_Time';
        $Where['Order']='Product_Sort Desc,Product_ID Desc';
        $Where['Where']='Product_State="1"';
        $Where['Limit']=$Count;
        $List=$this->db->All(DBPREFIX.'product',$Where);
        return $List;
    }
    //首页导航下滚动广告数据调用（即从数据库中调出来）
    private function GetHomeScrollPoster()
    {
        $Where=array();
        $Where['Fields']='Poster_Url,Poster_PicPathBig';
        $Where['Where']='Poster_Type="1" And Poster_Class="1" And Poster_State="1"';//Poster_Class是数据库字段：表图片或者数字类型，数据库定义状态为1是显示
        $Where['Order']='Poster_Sort Desc,Poster_ID Desc';
        $Where['Limit']="5";

        $List=$this->db->All(DBPREFIX.'poster',$Where);
        return $List;
    }
    //Data.XML简介：
    //Data.XML的每个文件实际上就是后台数据库表的一张表的说明。
    //它被Web程序和界面显示层XSLT共同调用，即其具备了数据和显示双重功能。因为是数据和显示都共享Data.XML，
    //所以，在开发的时候只要维护这一份XML，就可以了。这样做也使得开发程序变得更加便捷。

    //生成flash文件并调取数据库数据【$List=$this->GetHomeScrollPoster();】，以保存到data.xml文件中的方式，之后再让flash去调用数据库信息并显示到页面上
    //即生成Flash调用的data.xml文件,xml文件类似js效果
    private function CreateFlashFile()
    {
        $List=$this->GetHomeScrollPoster();
        $TempPath='';
        if(is_array($List))
        {
            foreach($List as  $value)
            {
                $TempPath.='<item image="'.$value['Poster_PicPathBig'].'" link="'.$value['Poster_Url'].'" textBlend="no"><![CDATA[]]></item>';
            }
        }
        $Str='<?xml version="1.0"?>
        <Banner
            bannerWidth="693"
            bannerHeight="354"
            textSize="14"
            textColor=""
            textAreaWidth=""
            textLineSpacing="0"
            textLetterSpacing="-0.5"
            textMarginLeft="12"
            textMarginBottom="3"
            transitionType="1"
            transitionDelayTimeFixed="8"
            transitionDelayTimePerWord=".5"
            transitionSpeed="8"
            transitionBlur="yes"
            transitionRandomizeOrder="no"
            showTimerClock="yes"
            showBackButton="yes"
            showNumberButtons="yes"
            showNumberButtonsAlways="Yes"
            showNumberButtonsHorizontal="yes"
            showNumberButtonsAscending="yes"
            autoPlay="yes">
            '.$TempPath.'
        </Banner>';
        return file_put_contents('./data.xml',$Str);
    }
    private function GetYinXingProjectList()
    {
        $Where=array();
        $Where['Fields']='Project_ID,Project_Title,Project_EnglishTitle,Project_PicPathBig';
        $Where['Where']='Project_State=1 ';
        $Where['Order']='Project_Sort Desc,Project_ID Desc';
        $Where['Limit']='5';
        $List=$this->db->All(DBPREFIX.'project',$Where);
        //print_r($List);
        //exit();
        return $List;
    }
    //获取首页关于我们的数据（把右边文字部分 归放在 关于我们 里面）
    private function GetAboutContent($ID)
    {
        $Where=array();
        $Where['Fields']='About_Content';
        $Where['Where']='About_ID="'.$ID.'"';
        $Info=$this->db->GetOne(DBPREFIX.'about',$Where);
        $Str=strip_tags($Info['About_Content']);//去掉html标签，只留下文本格式
        $Str=str_replace(array('&nbsp;',' ',"\r","\n","\r\n","\t"),'',$Str);//有回车换行或者tab缩进键这些组成的数组，统统替换为空
        return trim($Str);//清除该变量左右两边的空格
    }

}