<?php
$root = dirname(__FILE__);
require_once $root.'/curl.class.php';
$curl = new Scurl();
$curl->debug = true;
$curl->isproxy = true;
$curl->isAutoProxy = true;
$curl->agent = 'Mozilla/5.0 (Linux; Android 5.1.1; MX4 Pro Build/LMY48W) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036215 Safari/537.36 MicroMessenger/6.3.16.49_r03ae324.780 NetType/WIFI Language/zh_CN';
$opt = getopt('x:');
$curl->proxyNum = isset($opt['x']) ? $opt['x'] : null;
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
            $html = ' 返回为空对方服务器异常!';
        }

        echo "失败原因".date("Y-m-d H:i:s ").$html."\n";

        if ($isSleep) {
            sleep(60);
        }
    }
    $i++;
}





