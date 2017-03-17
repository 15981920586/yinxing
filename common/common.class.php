<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 16:43
 */
if(!defined('VISITSTATE')){echo 'visit error';exit();}
class Common
{
    protected $db;
    protected $smarty;

    //构造方法
    public function __construct()
    {
        $this->db=new MySQL;
        //echo '<pre>';
       // print_r($_SERVER);//打印服务器相关配置信息
        //全等表示：既判断类型又判断值
        if(stripos($_SERVER['REQUEST_URI'],'admin')===false) //stripos() 函数查找字符串在另一字符串中第一次出现的位置（不区分大小写）。
        {
            //smarty的基本信息初始化，方便其他页面调用，这些规定都是固定不变的
            $smarty=new smarty;//对smarty进行实例化
            $smarty->caching=false;//smarty缓存先关闭
            $smarty->cache_lifetime=10;//smarty缓存时间为10秒
            $smarty->template_dir='./template';//模板存放位置，即html页存放路径置
            $smarty->compile_dir='./template/view_c/';//预定义变量的存放路径
            $smarty->cache_dir='./template/cache/';//缓存存放的位置
            $smarty->left_delimiter='({';//使用 smarty模板引擎 左边的标签
            $smarty->right_delimiter='})';//使用 smarty模板引擎 右边的标签

            //$smarty->assign('Time',date('Y-m-d H:i:s'));
            /*function insert_get_current_time(){	//请以 insert_ 开头命名自定义函数
                return date("Y-m-d H:m:s");
            }*/

            //index.html中的内容<!--<h2>({insert name="get_current_time"})</h2>-->
            $this->smarty=$smarty;//对 smarty模板引擎 的调用
        }
    }
//当提交一个页面或者提交给数据库数据时，给客户的油耗提示，$State是定义的一个状态变量，$Con是输出的内容变量，$Url是要链接的具体路径
    public  static function MsgShow($State='',$Con='',$Url='')
    {
    //当方法中参数为空值时，下面又无要求输出此变量时，可以省略不写，即可以不一一对应。
        $Str='<script type="text/javascript">';

            if($State=='1')
            {
                if(!empty($Con))
                {
                    $Str.='alert("'.$Con.'");';
                }
                $Str.='location.href="'.$Url.'";';//上面尽管赋有空值，可是在这个地方定义了变量，
                //也就是说不管上面变量是不是赋有空值，这个条件下都要输出此变量。

            }elseif($State=='2')
            {
                if(!empty($Con))
                {
                    $Str.='alert("'.$Con.'");';
                }
                $Str.='history.back();';//返回上一级
            }

        $Str.='</script>';

        return $Str;
    }
}