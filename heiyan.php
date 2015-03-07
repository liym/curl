<?php
require_once 'curl.class.php';
$curl = new Scurl();
$curl->url='http://www.heiyan.com/accounts/login';
$curl->cookie='heiyan';
$curl->post['email'] = 'aimimi1988@gmail.com';
$curl->post['password']='123654';
//$curl->post['backUrl']='/book/10903/573205';
$curl->debug=true;
$curl->saveCookie();

$url = isset($argv[1])?$argv[1]:'http://www.heiyan.com/book/10903/573205';
echo $curl->request($url);

