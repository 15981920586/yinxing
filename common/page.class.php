<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/30
 * Time: 16:50
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
/*
 * http://www.baidu.com/?p=1
 * http://www.baidu.com/?p=2
 * http://www.baidu.com/?p=3&ID=3  $_GET['ID']
 * */
class Page
{
    public $Home='首页';
    public $Prev='上一页';
    public $Next='下一页';
    public $End='末页';
    public $Excursion='5';      //索引偏移量
    public $PageCount='10';     //当前默认显示条数
//$PageName只是一个变量，代表翻页总的名称，不管是那一页，名字都是p
//$CurrentPage是当前所在页码，此变量和$PageName即p组合起来为：p=第几页，左边是名字，右边是值，二者组合为一个a标签链接，共同显示在url里面，
//即Http://localhost:82/?Action=类名&Method=方法&p=第几页
    public $PageName='p';       //翻页名称
    public $CurrentPage;        //当前所在的页码
    public $ListCount;          //总记录数

    //$State 以逗号形式对其分隔成数组，逗号是默认的分割位置  H 首页 P 上一页 N 下一页 E 末页 X 索引
    public function ShowPage($State)
    {
        $Str='';
        //print_r($State);//打印结果是：H,X,E
        if(!empty($State))
        {
            $Arr=explode(',',$State);
           // echo '<pre>';
            //print_r($Arr);
            foreach($Arr as $key => $value)
            {
                if($value=='H'){$Str.=$this->HomePage();};
                if($value=='P'){$Str.=$this->PrevPage();};
                if($value=='X'){$Str.=$this->ExcursionPage();};
                if($value=='N'){$Str.=$this->NextPage();};
                if($value=='E'){$Str.=$this->EndPage();};
            }
        }else
        {
            $Str=$this->HomePage().$this->PrevPage().$this->ExcursionPage().$this->NextPage().$this->EndPage();
        }
        return $Str;

    }

    //获取当前的位置
    public function CurrentStartSeat()
    {
        return ($this->CurrentPage-1)*$this->PageCount;//PageCount当前默认显示条数
    }

    //索引偏移量即
    private function ExcursionPage()
    {
        $Str='';
        if($this->CurrentPage>($this->Excursion+1))
        {
            if($this->CurrentPage+$this->Excursion>=$this->TotalPage())
            {
                if($this->TotalPage()<($this->Excursion*2))
                {
                    $Start=1;
                }else{
                    $Start=$this->TotalPage()-($this->Excursion*2);
                }
                $Count=$this->TotalPage();
            }else{
                $Start=$this->CurrentPage-$this->Excursion;
                $Count=$this->Excursion+$this->CurrentPage;
            }
            //echo $Start.'<br>'.$Count;
            for($i=$Start;$i<=$Count;$i++)
            {
                if($i==$this->CurrentPage)
                {
                    $Class=' class="current"';
                    $Con=' '.$i.' ';
                }else{
                    $Class='';
                    $Con='<a href="'.$this->CurrentUrl().$this->PageName.'='.$i.'"> '.$i.' </a>';
                }
                $Str.='<span'.$Class.'>'.$Con.'</span>';
            }
        }else{
            if((($this->Excursion*2)+1)>=$this->TotalPage())
            {
                $Count=$this->TotalPage();
            }else{
                $Count=($this->Excursion*2)+1;
            }

            for($i=1;$i<=$Count;$i++)
            {
                if($i==$this->CurrentPage)
                {
                    $Class=' class="current"';
                    $Con=' '.$i.' ';
                }else{
                    $Class='';
                    $Con='<a href="'.$this->CurrentUrl().$this->PageName.'='.$i.'"> '.$i.' </a>';
                }
                $Str.='<span'.$Class.'>'.$Con.'</span>';
            }
        }
        return $Str;
    }

    //首页验证
    private function HomePage()
    {
        $Str='';
        if($this->CurrentPage==1 || empty($this->CurrentPage))
        {
            $Str='<span>'.$this->Home.'</span>';
        }else{
            $Str='<span><a href="'.$this->CurrentUrl().$this->PageName.'=1">'.$this->Home.'</a></span>';
        }
        return $Str;
    }

    //上一页
    private function PrevPage()
    {
        $Str='';
        if($this->CurrentPage>1)
        {
            if($this->CurrentPage>$this->TotalPage())
            {
                $CPage=$this->TotalPage();
            }else{
                $CPage=$this->CurrentPage-1;
            }
            $Str='<span><a href="'.$this->CurrentUrl().$this->PageName.'='.abs($CPage).'">'.$this->Prev.'</a></span>';
        }else{
            $Str='<span>'.$this->Prev.'</span>';
        }

        return $Str;
    }

    //获取下一页
    private function NextPage()
    {
        $Str='';
        if($this->CurrentPage>=$this->TotalPage())
        {
            $Str='<span>'.$this->Next.'</span>';
        }else{
            $Str='<span><a href="'.$this->CurrentUrl().$this->PageName.'='.abs($this->CurrentPage+1).'">'.$this->Next.'</a></span>';
        }

        return $Str;
    }

    //获取尾页
    private function EndPage()
    {
        $Str='';
        if($this->CurrentPage>=$this->TotalPage())
        {
            $Str='<span>'.$this->End.'</span>';
        }else{
            $Str='<span><a href="'.$this->CurrentUrl().$this->PageName.'='.abs($this->TotalPage()).'">'.$this->End.'</a></span>';
        }
        return $Str;
    }

    //获取当前数据一共有多少页
    private function TotalPage()
    {
        return ceil($this->ListCount/$this->PageCount);
    }

    //获取当前连接
    private function CurrentUrl()
    {
        //echo '<pre>';
        //print_r($_SERVER);
        if(is_array($_GET))
        {
            unset($_GET[$this->PageName]);
            $UrlStr='';

            foreach($_GET as $key => $val)
            {
                $UrlStr.=$key.'='.$val.'&';
            }

//            if(!empty($UrlStr))
//            {
//                $UrlStr=substr($UrlStr,0,-1);
//            }
        }
        //print_r($_GET);print_r($UrlStr);
        //echo '<br>';
        $Str='';
        if(strpos($_SERVER['REQUEST_URI'],'?')===false)
        {
            $Str='?';
        }
        $CurrentUrl=str_replace($_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']);
        $CurrentUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$CurrentUrl.$Str.$UrlStr;
        return $CurrentUrl;
    }
}