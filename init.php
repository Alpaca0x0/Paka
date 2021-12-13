<?php ini_set('display_errors','1'); error_reporting(E_ALL);
if(!isset($_SESSION)){ session_start(); }
define('INIT', true); // defined('INIT') or die('NO INIT');
date_default_timezone_set('Asia/Taipei');

/****************************************************************/
# Basic paths
{
	define('LOCAL', __DIR__.DIRECTORY_SEPARATOR); # Private
	define('ROOT', str_replace('\\', '/', substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])).DIRECTORY_SEPARATOR)); # Public
}

# Global Constants
{
	define('ID', ID('page', $_SERVER['SCRIPT_NAME'])); // ID of Current Page
	define('PATH', dirname($_SERVER['SCRIPT_NAME']).'/'); // Path of Current Page
}
/****************************************************************/
# Including file paths
{
	# Private
	define('Func', LOCAL.'function/');
	define('Conf', LOCAL.'config/');
	define('Clas', LOCAL.'class/');
	define('Inc', LOCAL.'include/');
	# Public
	define('Asset', ROOT.'asset/');
	define('Frame', Asset.'frame/');
	define('JS', Asset.'js/');
	define('CSS', Asset.'css/');
	define('IMG', Asset.'image/');
	define('Page', ROOT.'page/');
}
/****************************************************************/
# Get-Path functions
{
	# Private
	function Func($name,$exname='func.php'){ return rtrim(Func."$name.$exname",' .'); }
	function Conf($name,$exname='conf.php'){ return rtrim(Conf."$name.$exname",' .'); }
	function Clas($name,$exname='class.php'){ return rtrim(Clas."$name.$exname",' .'); }
	function Inc($name,$exname='inc.php'){ return rtrim(Inc."$name.$exname",' .'); }
	# Public
	function Root($name,$exname='php'){ return rtrim(ROOT."$name.$exname",' .'); }
	function Frame($name,$exname=''){ return rtrim(Frame."$name.$exname",' .'); }
	function JS($name,$exname='js'){ return rtrim(JS."$name.$exname",' .'); }
	function CSS($name,$exname='css'){ return rtrim(CSS."$name.$exname",' .'); }
	function IMG($name,$exname='png'){ return rtrim(IMG."$name.$exname",' .'); }
	function Page($name,$exname='php'){ return rtrim(Page."$name.$exname",' .'); }
}
# Function
{
	// function E($string){ echo (string)$string; }
	function ID($type, $val=false){
		$type = trim(strtolower($type));
		$val = $val?trim($val):$_SERVER['SCRIPT_NAME'];
		if($type == 'page') { return sha1($val); }
		return false;
	}
	//
	function PATH($type, $val=false){
		$type = trim(strtolower($type));
		$val = $val?trim($val):$_SERVER['SCRIPT_NAME'];
		if($type == 'page'){ return $val; }
		return false;
	}
}
/****************************************************************/

/****************************************************************/

// auto run
@include_once(Func('user'));
$User->Update();

/****************************************************************/
