<?php defined('INIT') or die('NO INIT'); ?>
<?php @include_once(Conf('lang')); ?>

<?php @include_once(Func('db')); ?>

<?php
class Lang{
	public $Lang = 'None'; // Current language
	public $Datas = 'N/A'; // Get from database (original datas)
	public $Map = 'N/A'; // Analyzed the $Datas

	function __construct($lang='en-us'){
		$lang = strtolower(trim($lang));
		$this->Lang = in_array($lang, Langs)?$lang:Langs[0];
		return true;
	}
	
	function __destruct(){ }

	function Get(){
		# Get the language datas from database, and put the datas into $Map
		global $DB;
		$DB['query'] = $DB['connect']->prepare("SELECT `id`,`en-us`,`$this->Lang` FROM `language`;");
		$DB['query']->execute();
		// echo $DB['query']->debugDumpParams();
		$this->Datas = $DB['query']->fetchAll(PDO::FETCH_ASSOC);
		return true;
	}

	function Map(){
		// Analyze the $Map and insert into $Table
		$this->Map = Array();
		foreach ($this->Datas as $key => $val) {
			$this->Map[$val['id']] = is_null($val[$this->Lang])?$val[Langs[0]]:$val[$this->Lang];
		}
		return true;
	}
}

// DB
// $Lang = new Lang('en-us');
// $Lang->Get();
// $Lang->Map();