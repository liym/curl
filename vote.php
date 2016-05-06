<?php
$root = dirname(__FILE__);
require_once $root.'/curl.class.php';
$curl = new Scurl();
$curl->debug = true;
$curl->isproxy = false;
$curl->isAutoProxy = true;

$i = 0;
while(true) {
    $curl->post['cc'] = 25;
    $curl->post['uid'] = md5(time().rand(1, 1000));
    //$curl->url = 'http://vote.ecloud-zj.com/wx/voteSubmit';
    $html = $curl->request('http://vote.ecloud-zj.com/wx/voteSubmit');
    if ($html==1) {
        echo "成功".date("Y-m-d H:i:s \n");
    } else {
        echo "失败原因".date("Y-m-d H:i:s ").$html."\n";
    }
    $i++;
}





