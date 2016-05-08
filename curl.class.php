<?php
/**
 *
 * @author fangjiefeng
 * @email fang.jief@163.com
 * @date 2015-02-10
 * @version 1.2
 */

class Scurl{

    public $proxyNum = null;
	public $isproxy = true;
	public $isAutoProxy = false;
    public $isBestProxy = false;
    public $bestProxy = [];
	public $proxy='proxy.jgb:8081';
    public $proxyfile = '/proxy.list';
	public $referer='';
    public $agent='Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.94 Safari/537.36';
    public $post;
	public $url='';
	public $head;
	public $isHead=false;
	public $isLocaltion = false;
	public $debug = false;
	public $cookie=false;//cookie str or cookie file
	public $cookiepath = '/tmp/';
	public $cookieotime = 3600;
	public $maxtime = 60;
	public $proxyArr = [];
	
	
	
	public function request($url){
		$this->url=$url;
		return $this->ppopen($this->getcmd());
	}
	

	public function saveCookie(){
		if($this->cookie){
			$cookiefile = $this->cookiepath.$this->cookie;
			if(file_exists($cookiefile)){
				if((time()-filectime($cookiefile)) > $this->cookieotime){
					unlink($cookiefile);
				}else{
					$this->post='';
					return true;
				}
			}
			if($this->debug){
				echo "cookie file: {$cookiefile}\n";
			}
			$cmd = $this->getcmd(2). " -c {$cookiefile} ";
			$res=$this->ppopen($cmd);
			@chmod($cookiefile,0777);
			$this->post='';
		}else{
			$this->post='';
			exit('place set cookie path');
		}
	}
	
	
	public function getStatus(){
		$this->isHead=true;
		if(!$this->url){
			exit("no url set\n");
		}
		$cmd = $this->getcmd();
		$res = $this->ppopen($cmd);
		if(preg_match('/HTTP\/\d+\.\d+\s+(\d+)\s+\w+\n?/is',$res,$m)){
			return $m[1];
		}
		//echo $res;
	}
	

	private function getcmd($type=1){
		$this->cmd='';
		$this->cmd = "curl -s '{$this->url}' ";
		$this->__proxy();
		$this->__referer();
		$this->__agent();
		$this->__head();
		$this->__post();
		if($type !=2 ){
			$this->__cookie();
		}


		if(preg_match('/https/is',$this->url)){

			$this->cmd .= " -k ";
		}
		if($this->isHead){
			$this->cmd .= " -I ";
		}
		if($this->isLocaltion){
			$this->cmd .= " -L ";
		}
		if($this->maxtime){
			$this->cmd .=" -m {$this->maxtime} ";
		}
		
		if($this->debug){
		
			echo "$this->cmd\n";
		}
		return $this->cmd;
	}
	

	private function __proxy(){
		if($this->isproxy && $this->proxy){
			if($this->isAutoProxy){
				if(empty($this->proxyArr)) {
					$root = dirname(__FILE__);
					if(file_exists($root.$this->proxyfile)){
						$this->proxyArr = $this->checkProxy(file($root.$this->proxyfile));
					}else{
						exit('no proxy file :'. $root.$this->proxyfile);
					}
				}
				$this->proxy = $this->getOkProxy();
                
			}
			$proxyinfo=explode('@',$this->proxy);
			if(isset($proxyinfo[1])){
				$this->cmd .= " -x {$proxyinfo[0]} -U {$proxyinfo[1]} ";
			}else{
				$this->cmd .= " -x {$proxyinfo[0]} ";
			}
		}	
	}
	
	private function __referer(){
		if($this->referer){
			$this->cmd .= " -e '{$this->referer}' ";

		}	
	}
	
	private function __agent(){
		if($this->agent){
			$this->cmd .=" -A '{$this->agent}' ";
		}	
	}
	
	private function __head(){
		if($this->head){
			if(is_array($this->head)){
				foreach($this->head as $row){
					$this->cmd .= " -H '{$row}' ";
				}
			}else{
				$this->cmd .= " -H '{$this->head}' ";
			}
			
		}	
	}
	
	private function __cookie(){
		$cookiefile = $this->cookiepath.$this->cookie;
		if(file_exists($cookiefile)){
			$this->cmd .=  " -b '{$cookiefile}' ";
		}elseif(is_string($this->cookie)){
			$this->cmd .= " -b '{$this->cookie}' ";
		}
	}
	
	private function __post(){
		if($this->post){
			if(is_string($this->post)){
				$str = $this->post;
			}else{
				$str = http_build_query($this->post);
			}
			$this->cmd .= " -d '{$str}' ";
		}
		$this->post='';	
	}

	private function ppopen($cmd){
		$ft = popen($cmd,'r');
		$res='';
		while(!feof($ft)){
			$res.=fgets($ft,2048);
		}
		pclose($ft);
		return $res;

	}

    private function checkProxy($proxyArr) {
        $newProxyArr = [];
        foreach ($proxyArr as $proxy) {
            $proxy = trim($proxy);
            if (empty($proxy)) {
                continue;
            }

            if (!preg_match('/.*:.*/is', $proxy)) {
                continue;
            }

            $newProxyArr[] = $proxy;
        }

        return $newProxyArr;
    }

    
    public function getOkProxy()
    {
        if ($this->proxyNum > count($this->proxyArr) && count($this->bestProxy) > 0) {
            $this->proxyNum = 0;
            $this->isBestProxy = true;
        }

        if ($this->isBestProxy) {
            $proxyArr = array_keys($this->bestProxy);
            if (!isset($proxyArr[$this->proxyNum])) {
                $this->isBestProxy = false;
                $this->proxyNum = 0;
                return trim($this->proxyArr[$this->proxyNum]);
            }
            return $proxyArr[$this->proxyNum];
        }
        if (!is_null($this->proxyNum) && isset($this->proxyArr[$this->proxyNum])) {
            return trim($this->proxyArr[$this->proxyNum]);
        } else {
            if ($this->proxyNum > count($this->proxyArr)) {
                $this->proxyNum = 0;
            }
            $max = count($this->proxyArr)-1;
            return trim($this->proxyArr[rand(0,$max)]);
        }
    }
}
