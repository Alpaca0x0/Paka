<?php defined('INIT') or die('NO INIT'); ?>

<?php
class Loger{
	private $Types = Array();
	private $Logs = Array();

	function __construct(){
		$this->Types = ["unknown", "error", "warning", "success", "message", ];
		$this->Push('error',"Haven't Init");
	}

	function Init($array=[]){
		$this->Logs = $array;
	}

	function Push($type, $content, $arg=false){
		$type = trim($type);
		if (!in_array($type, $this->Types)){ return false; }
		$content = trim($content);
		$log = $arg ? [$type, $content, $arg] : [$type, $content];
		array_push($this->Logs, $log);
		return true;
	}

	// get the log
	function Get($type='array'){
		$type = strtolower(trim($type));
		if($type=='array'){ return $this->Logs; }
		if($type=='json'){ return json_encode($this->Logs); }
		return false;
	}

	// get the types
	function Types($type='array'){
		$type = strtolower(trim($type));
		if($type=='array'){ return $this->Types; }
		if($type=='json'){ return json_encode($this->Types); }
		return false;
	}

	// check if have at least one of those $types
	function Check($types=['unknown','error','warning']){
		if(!is_array($types)){ $types = [$types]; }
		foreach($this->Logs as $log){
			if(in_array($log[0],$types)){ return true; }
		}return false;
	}

	// check if have the content of log in logs
	function Have($contents=[]){
		if(!is_array($contents)){ $contents = [$contents]; }
		foreach($this->Logs as $log){
			if(in_array($log[1],$contents)){ return true; }
		}return false;
	}

	// count log which have types
	function Count($types=['unknown','error','warning']){
		if(!is_array($types)){ $types = [$types]; }
		$total = 0; // init number
		foreach($this->Logs as $log){
			if(in_array( $log[0], $types )){ $total += 1; }
		}
		return $total;
	}

	// response logs
	function Resp(){ die($this->Get('json')); }

}
