<?php
/**
 * 
 * 超级curl 自定义curl , 整合curl 方便获取html
 * @author fangjiefeng
 * @email fang.jief@163.com
 * @date 2015-02-10
 * @version 1.2
 */

class Scurl{

	public $isproxy = true;
	public $isAutoProxy = false;
	public $proxy='proxy.jgb:8081'; // 192.168.0.1:88@hexin:hx300033 代理用@来切割 自动代理请填写 auto
    public $proxyfile = '/proxy.list';
	public $referer='';
    public $agent='Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.94 Safari/537.36';
    public $post; // post 参数 array or str
	public $url='';
	public $head; //head 参数 array or str
	public $isHead=false;
	public $isLocaltion = false;//是否跳转
	public $debug = false;
	public $cookie=false;//cookie str or cookie file
	public $cookiepath = '/tmp/';
	public $cookieotime = 3600;//cookie 超时时间 默认3600秒
	public $maxtime = 60;//设置最大超时间 默认为60秒
	public $proxyArr = [];
	
	
	
	public function request($url){
		$this->url=$url;
		return $this->ppopen($this->getcmd());
	}
	
	/**
	 * 
	 * 把cookie 放到一个文件中，当后面要用到cookie 可以直接调用
	 */
	public function saveCookie(){
		if($this->cookie){
			$cookiefile = $this->cookiepath.$this->cookie;
			if(file_exists($cookiefile)){
				//如查cookie 没有超时就没必须重新登陆
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
			exit('请配置cookie');
		}
	}
	
	
	public function getStatus(){
		$this->isHead=true;
		if(!$this->url){
			exit("请先设置好url\n");
		}
		$cmd = $this->getcmd();
		$res = $this->ppopen($cmd);
		if(preg_match('/HTTP\/\d+\.\d+\s+(\d+)\s+\w+\n?/is',$res,$m)){
			echo $m[1];
			return $m[1];
		}
		//echo $res;
	}
	
	/**
	 * 
	 * 
	 * @param unknown_type $type
	 * 1 默认配置
	 * 2 save cookie
	 */
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
	

	/**
	 * 配置代理，这里搞个自动 或手动代理
	 * Enter description here ...
	 */
	private function __proxy(){
		if($this->isproxy && $this->proxy){
			if($this->isAutoProxy){
				//自动随机代理
				if(empty($this->proxyArr)) {
					$root = dirname(__FILE__);
					if(file_exists($root.$this->proxyfile)){
						$this->proxyArr = file($root.$this->proxyfile);
					}else{
						exit('自动代理请先在当前目录配置代理文件 proxy.list 格式为host:port@user:pass 或 host:port');
					}
				}
				$max=count($this->proxyArr)-1;
				$this->proxy = trim($this->proxyArr[rand(0,$max)]);
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

	


}
