<?php 
require_once 'curl.class.php';
$curl = new Scurl();
$curl->debug=true;

//用法一 简单get 默认有代理 
/*
echo $html = $curl->request('http://www.baidu.com');
*/

//用法二  简单get 随机代理
/*
$curl->proxy='auto';
echo $html = $curl->request('http://www.baidu.com');
*/

//用法三 带cookie 的简单 get 
$curl->proxy=false;
$curl->cookie='VIS_RESEARCH_SESSIONID=1k33o8dd8n37alcrrpb0rinv32'; 
echo $html = $curl->request('http://vis.10jqka.com.cn/research/cbbdb/news/productid/3/nid/1507');  
  

//用法四 登陆并且 带着cookie 访问
/*
$curl->cookie='php100';
$curl->post['pwuser']='qwert123456789';
$curl->post['pwpwd'] = 'qwert12345';
$curl->post['step'] = '2';
$curl->post['lgt'] = '0';
$curl->post['jumpurl'] = 'http://bbs.php100.com';
$curl->url = 'http://bbs.php100.com/login.php';
$curl->saveCookie();
echo $html = $curl->request('http://bbs.php100.com/u.php?uid=329616');
*/
?>
