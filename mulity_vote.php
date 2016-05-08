<?php

$proxyArr = file('proxy.list');
$process = 50;
foreach ($proxyArr as $key => $proxy) {
    $proxy = trim($proxy);
    if (empty($proxy)) {
        continue;
    }

    $cmd = "php vote.php -o {$key} >> /tmp/vote-00{$key}.log &";
    ppopen($cmd);
    if ($key > $process) {
        exit;
    }
}

function ppopen($cmd){
    $ft = popen($cmd,'r');
    $res='';
    while(!feof($ft)){
        $res.=fgets($ft,2048);
    }
    pclose($ft);
    return $res;

}