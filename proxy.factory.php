<?php
require_once('curl.class.php');
$curl = new Scurl();

$curl->isproxy = true;
$curl->maxtime = 10;
$proxy = file('proxy.test.list');
$i = 0;
$proxyB = [];
while(true) {
	$proxyA = trim($proxy[$i]);
	$curl->proxy = $proxyA;
	$curl->url = 'http://1212.ip138.com/ic.asp';
    //$curl->url = 'http://vote.ecloud-zj.com/wx/votetop';
	$html = $curl->getStatus();
	if ($html == 200) {
        echo ".";
		$proxyB[] = $proxyA;
	}

	$i++;
	if($i > count($proxy)) {
		break;
	}
}

if (count($proxyB) > 0) {
	$textProxy = implode("\n", $proxyB);
	file_put_contents('proxy.list.bak', $textProxy);
	echo "ok";
} else {
	echo "error";
}
