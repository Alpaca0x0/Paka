<?php defined('INIT') or die('NO INIT'); ?>

<?php @include_once(Conf('lang')); ?>

<?php //@include_once(Func('db')); ?>

<?php
class Lang{
	public $Lang = 'None'; // Current language
	public $Map = 'N/A'; // Languages tables
	public $BasicLang = 'en-us'; // The basic language
	public $BasicMap = 'N/A'; // The basic language map

	function __construct($lang='en-us'){
		$lang = strtolower(trim($lang));
		$this->Lang = in_array($lang, array_keys(Langs))?$lang:Langs[0];
		return true;
	}
	
	function __destruct(){ }

	function Init(){
		# Get the language datas from init file, and put the datas into $Map
		try{
			$this->BasicMap = parse_ini_file(Conf("languages/{$this->BasicLang}",'ini'),true);
			$this->Map = parse_ini_file(Conf("languages/{$this->Lang}",'ini'),true);
		}catch(Exception $e){
			return false;
		} return true;
	}
}




// File ini
// $Lang = new Lang('en-us');
// $Lang->Init();
