<?php
$root = dirname(__FILE__);
require_once $root.'/curl.class.php';
$curl = new Scurl();
$curl->debug = true;
$curl->isproxy = true;
$curl->isAutoProxy = true;

$i = 0;
while(true) {
    $isSleep = false;
    $curl->post['cc'] = 25;
    $curl->post['uid'] = md5(time().rand(1, 1000));
    //$curl->url = 'http://vote.ecloud-zj.com/wx/voteSubmit';
    $html = $curl->request('http://vote.ecloud-zj.com/wx/voteSubmit');
    if ($html==1) {
        echo "成功".date("Y-m-d H:i:s \n");
    } else {
        if ($html == -9) {
            $html = ' 发现不能刷就1分钟后检测一次';
            $isSleep = true;
        }

        if (empty($html)) {
            $html = ' 对方服务器可能挂了!';
            $isSleep = true;
        }

        echo "失败原因".date("Y-m-d H:i:s ").$html."\n";

        if ($isSleep) {
            sleep(60);
        }
    }
    $i++;
}





