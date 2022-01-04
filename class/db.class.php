<?php defined('INIT') or die('NO INIT'); ?>

<?php @include_once(Conf('db')); ?>

<?php
class DB{
	public $Connect = false;
	private $Query = false; // $this->Query->execute([...])

	function __construct(){}

	function Connection(){
		if($this->Connect){ return true; }
		else{ return false; }
	}

	function Connect(){
		if($this->Connection()){ return true; }
		try{
			$this->Connect = new PDO("mysql:host=".DB['host'].";dbname=".DB['name'].";charset=utf8mb4", DB['user'], DB['pass']);
			if(!$this->Connect){ return false; }
			// setting PDO
			$this->Connect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			// dont convert column to string
			$this->Connect->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
		}catch(Exception $e){ return false; }
		return true;
	}

	function Query($sql){
		// e.g. $sql = "SELECT * FROM `account` WHERE `id`=:example;";
		if(!$this->Connection()){ return false; }
		$db = $this->Connect;
		try{
			$this->Query = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			return true;
		}catch (Exception $e) { return false; }
	}

	function Execute($values=[]){
		if(!$this->Connection()){ return false; }
		$db = $this->Connect;
		try{
		    if($this->Query->execute($values) === false){ // e.g. [':example'=>'value']
		    	return false; //$this->Query->debugDumpParams()
			}else{ return $this->Query; }
		}catch (Exception $e) { return $e; }
	}

	 function Sentence(){
		if(!$this->Connection()){ return false; }
		return $this->Query->queryString;
	}

	function Fetch($result, $type='assoc'){
		if(!$this->Connection()){ return false; }
		$type = strtolower(trim($type));
		# array: Both number and column name save as index
		# assoc: Save column name as index
		# row: Save number as index
		if($type=='assoc'){ return $result->fetch(PDO::FETCH_ASSOC); }
		if($type=='array'){ return $result->fetch(PDO::FETCH_BOTH); }
		if($type=='row'){ return $result->fetch(PDO::FETCH_NUM); }
		if($type=='class'){ return $result->fetch(PDO::FETCH_CLASS); }
		return false;
	}

	function FetchAll($result, $type='assoc'){
		if(!$this->Connection()){ return false; }
		$type = strtolower(trim($type));
		# array: Both number and column name save as index
		# assoc: Save column name as index
		# row: Save number as index
		if($type=='assoc'){ return $result->fetchAll(PDO::FETCH_ASSOC); }
		if($type=='array'){ return $result->fetchAll(PDO::FETCH_BOTH); }
		if($type=='row'){ return $result->fetchAll(PDO::FETCH_NUM); }
		if($type=='class'){ return $result->fetchAll(PDO::FETCH_CLASS); }
		return false;
	}
}
