<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/1
 * Time: 16:43
 */
define('VISITSTATE','ON');//声明，定义常量（访客状态是开启还是关闭）
define('DBHOST','127.0.0.1');//服务器主机，本地回送地址
define('DBNAME','yinxingjnhweb2');//数据库名称
define('DBUSERNAME','root');//数据库账户
define('DBUSERPWD','root');//数据库密码
define('DBPREFIX',' web_');//设置数据库表的前缀，前缀可以是app_等其他的
define('ADMINTIMECHECK',3600);//设置当前管理员是否一小时内未操作
define('ADMINLISTCOUNT','11');//后台文章列表每页显示限制条数，翻页原理（当前页-1）*索引偏移量
define('PASSWORDKEY','xy*?(#*4565jlsdf%{:}');//md5加密时，定义秘钥常量，使别人无法破解
