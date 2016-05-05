<?php
require_once 'curl.class.php';
$curl = new Scurl();
$curl->debug = true;
$curl->isproxy = true;
$curl->isAutoProxy = true;

$i = 0;
while(true) {
    $curl->post['cc'] = 25;
    $curl->post['uid'] = md5(time().rand(1, 1000));
    //$curl->url = 'http://vote.ecloud-zj.com/wx/voteSubmit';
    $html = $curl->request('http://vote.ecloud-zj.com/wx/voteSubmit');
    if ($html==1) {
        echo "成功\n";
    } else {
        echo "失败原因".$html."\n";
    }
    echo date("Y-m-d H:i:s ")."\n";
    usleep(500);
    if ($i>1500) {
        break;
    }
    $i++;
}





