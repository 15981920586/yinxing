<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 11:57
 */
class SystemAction extends Base
{
    public function Index()
    {
        $FilePath='../common/WebBaseInfo.php';
        if(file_exists($FilePath))
        {
            include($FilePath);
        }
        include('./template/system.html');
    }

    //保存网站基本信息
    public function BaseSave()
    {
        //echo '<pre>';
        //print_r($_POST);
        if(is_array($_POST))
        {
            $Str='<?php'."\r\n".'$WebBaseInfo=array();'."\r\n";
            foreach($_POST as $key => $value)
            {
                //判断系统是否自动开启转译get_magic_quotes_gpc()
                //addcslashes是在系统没有开启转译时 添加的转译
                //($value,'\'\\"-')这个里面的符号最外面是两个单引号
                //里面是自定义设置：对 杠，单引号，双斜杠，双引号，减号进行转译，即对什么符号进行转译
            	if(!get_magic_quotes_gpc()){
                	$Str.='$WebBaseInfo[\''.$key.'\']=\''.addcslashes($value,'\'\\"-').'\';'."\r\n";
            	}else{//否则不添加转译addcslashes
            		$Str.='$WebBaseInfo[\''.$key.'\']=\''.$value.'\';'."\r\n";
            	}
            }

            $Str.='return $WebBaseInfo;'."\r\n".'?>';
            file_put_contents('../common/WebBaseInfo.php',$Str);//创建文件，并把字符串写入文件中
        }
        echo Common::MsgShow(1,'更新成功','./?Action=System');
        exit();
    }
}