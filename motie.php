<?php 
require_once 'curl.class.php';
$curl = new Scurl();
$curl->debug=true;
$curl->isproxy=false;


$curl->cookie='php100';
$curl->post['email']='1912727434@qq.com';
$curl->post['password'] = '123654';
$curl->post['remember'] = 'on';
$curl->post['backUrl'] = '/home';
$curl->url = 'http://www.motie.com/accounts/login';
$curl->saveCookie();
echo $html = $curl->request('http://www.motie.com//ajax/chapter/more/wrap?chapterId=1442922');
?>
