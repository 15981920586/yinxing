<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 21:07
 */

if(!defined('VISITSTATE')){echo 'error visit';exit();}
class MySQL
{
    //定义一个私有数据库属性
    private $db;
    //构造方法，目的初始化数据:调用 本程序和数据库相连接的方法
    public function __construct()
    {
        $this->db=$this->ConnectDB();
    }
//设置mysqli和数据库相连接的基本信息以及编码格式的设置
    public function ConnectDB()
    {
        $mysqli=new mysqli(DBHOST,DBUSERNAME,DBUSERPWD,DBNAME);
        $mysqli->set_charset('utf8');
        //echo mysqli_errno($mysqli);

        return $mysqli;
    }
//从数据库中获取所有数据，其中$TableName是表名，$Param是条件，在下面方法调用中一般都用的$Where这个数组
    public function All($TableName,$Param)
    {
        //echo '123';
        //print_r($Param);
        if(is_array($Param))
        {
            //以下四个参数是字段，条件，排序，限制条件
            $Fields='';$Where='';$Order='';$Limit='';
            if(empty($Param['Fields']))
            {
                $Fields='*';//当字段为空时，返回所有数据，即数据库中的 *
            }else
            {
                $Fields=$Param['Fields'];//字段不为空时，就返回该字段信息
            }

            if(!empty($Param['Where']))
            {
                $Where=' WHERE '.$Param['Where'];//条件不为空时，拿SQL语句中的一部分WHERE和对应的条件进行拼接 组成sql中的条件句

                //print_r($Where);
            }
            if(!empty($Param['Order']))
            {//排序不为空时执行这里面的 排序 拼接
                $Order=' ORDER BY '.$Param['Order'];
            }
            if(!empty($Param['Limit']))
            {//限制条件不为空时，执行限制条件语句的拼接
                $Limit=' LIMIT '.$Param['Limit'];
            }
            //ALL方法中执行SQL语句的一个拼接，这样才满足数据库语句的基本规范，我们才可以从数据库中获取数据
            $SQL='SELECT '.$Fields.' FROM '.$TableName.$Where.$Order.$Limit;
            //print_r($SQL);
            // echo '<br>';
            $Result=$this->db->query($SQL);//数据库语句的执行
            //echo '<br>';
            //print_r($Result);
            while($Row=mysqli_fetch_assoc($Result))//获取多条数据时的需要先将每条数据附给$Row，再用while进行循环
            {
                //print_r($Row);
                $Arr[]=$Row;//因为的所有数据记录，所以$Arr要加一个中括号，表示多个数据
            }
            if(empty($Arr))
            {
                $Arr='';
            }
            return $Arr;
        }else{
            return false;
        }
    }
    //获取单条数据
    public  function GetOne($TableName,$Param=array())
    {
        if(is_array($Param))
        {
            $Fields='';$Where='';
            if(empty($Param['Fields']))
            {
                $Fields=' * ';
            }else{
                $Fields=$Param['Fields'];
            }
            if(!empty($Param['Where']))
            {
                $Where=' WHERE '.$Param['Where'];
            }
            $SQL='SELECT ' .$Fields.' FROM ' .$TableName. $Where. ' LIMIT 1 ';//单条数据时，限制输出一条
            //print_r($SQL);
            $Result=$this->db->query($SQL);//执行
            $Arr=mysqli_fetch_assoc($Result);//获取单条数据执行的SQL语句
            return $Arr;
        }else{
            return false;
        }
    }
    //修改保存数据到数据库中
    public function EditSave($TableName,$Param,$Where)
    {
        if(is_array($Param))
        {
            $Str='';
            foreach($Param as $key => $value)
            {
                //escape_string()函数是：转义 SQL 语句中使用字符串中的特殊字符
                $Str.=$this->db->escape_string(trim($key)).'="'.$this->db->escape_string(trim($value)).'",';
            }
            if(!empty($Str))
            {
                $Str=substr($Str,0,-1);//当不为空时，随机取数，排除最后一位
            }
            $SQL='UPDATE '.$TableName.' SET '.$Str.' WHERE '.$Where.';';
            //print_r($SQL);
            $State= $this->db->query($SQL);
            if($State)
            {
                return true;//状态为ture即真时返回一个ture
            }else{
                return false;//状态为false即假时返回一个false
            }
        }else{
            return false;
        }
    }
    //执行SQL语句
    public function Query($SQL)
    {
        $State=$this->db->query($SQL);
        if($State)
        {
            return true;
        }else{
            return false;
        }
    }
    //删除数据记录
    public  function Del($TableName,$Where,$Limit=true)
    {
        //假如$Limit为真，即由限制条件的话，执行删除一条操作，若$Limit=false时不走if判断题，即默认删除多条
        if($Limit)
        {
            $Limit=' LIMIT 1';
        }
        $SQL='DELETE FROM '.$TableName.' WHERE '.$Where.$Limit.';';
        //print_r($SQL);
        $State=$this->db->query($SQL);
        if($State)
        {
           return true;
        }else{
           return false;
        }
    }
//添加数据到数据库中
    public function Add($TableName,$Param)
    {
        if(is_array($Param))
        {
            $FieldStr='';$ContentStr='';
            foreach($Param as $key=>$value)
            {
                $FieldStr.=$key.',';//定义key值为数据库中字段，并用逗号拼接
                $ContentStr.='"'.$this->db->escape_string($value).'",';//escape_string()清除数据库语句中的特殊字符，内容部分是$value，也用逗号拼接
            }
            if(!empty($FieldStr))
            {
                //substr() 函数返回字符串的一部分。
                $FieldStr=substr($FieldStr,0,-1);//字段不为空时，清除最后一个逗号
            }
            if(!empty($ContentStr))
            {
                $ContentStr=substr($ContentStr,0,-1);//内容不为空时，清除最后一个逗号
            }

            if(!empty($FieldStr) && !empty($ContentStr))
            {
                //插入语句的基本结构：insert into 表名 （字段一，字段二，字段三） value（‘字段一的值’，‘字段二的值’，‘字段三的值’）.
                $SQL='INSERT INTO '.$TableName.' ('.$FieldStr.') VALUES ('.$ContentStr.');';
                $this->db->query($SQL);
                //echo $SQL;
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //析构函数方法，即程序在销毁前可以再最后做的事情
    public function __destruct()
    {
        //本析构函数默认是关闭数据库，因为程序一旦打开数据库，一定要关闭
        $this->db->close();
    }
}
