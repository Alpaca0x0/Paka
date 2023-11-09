<?php
// die('The website is under maintenance. Please come back later.<br>網站正在維修中，請稍後再回來看看吧。');

/****************************************************************/
# Read Me
# Do NOT output any thing on this page
# Every page need to include this file or exit()
/****************************************************************/
# Initialize
if(!isset($_SESSION)){ @session_start(); }
date_default_timezone_set('Asia/Taipei');
/****************************************************************/
define('INIT', true); // defined('INIT') or die('NO INIT');
define('DEBUG', true); // debug mode, will show the error message
define('DEV', false); // development mode

if(DEBUG){ ini_set('display_errors','1'); error_reporting(E_ALL); }
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

# Basic web info
{
	# Domain
	define('Domain', explode(':', isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''))[0]);
	# Protocol
	define('Protocol', isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http');
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
	define('Asset', 'asset/');
	define('Frame', ROOT.Asset.'frame/');
	define('JS', ROOT.Asset.'js/');
	define('CSS', ROOT.Asset.'css/');
	define('IMG', ROOT.Asset.'image/');
	define('Page', ROOT.'page/');
	define('API', ROOT.'api/');
	// Both
	define('Mod', Asset.'module/');
}
/****************************************************************/
# Get-Path functions
{
	# Private
	function Func($name,$exname='func.php'){ return rtrim(Func."$name.$exname",' .'); }
	function Conf($name,$exname='conf.php'){ return rtrim(Conf."$name.$exname",' .'); }
	function Clas($name,$exname='class.php'){ return rtrim(Clas."$name.$exname",' .'); }
	function Inc($name,$exname='inc.php'){ return rtrim(Inc."$name.$exname",' .'); }
	function Mod($name,$exname='.php'){ return rtrim(LOCAL.Mod."$name.$exname",' .'); }
	# Public
	function Root($name,$exname='php'){ return rtrim(ROOT."$name.$exname",' .'); }
	function Frame($name,$exname=''){ return rtrim(Frame."$name.$exname",' .'); }
	function JS($name,$exname='js'){ return rtrim(JS."$name.$exname",' .'); }
	function CSS($name,$exname='css'){ return rtrim(CSS."$name.$exname",' .'); }
	function IMG($name,$exname=''){ return rtrim(IMG."$name.$exname",' .'); }
	function Page($name,$exname='php'){ return rtrim(Page."$name.$exname",' .'); }
	function API($name,$exname='php'){ return rtrim(API."$name.$exname",' .'); }
	function Plug($name,$exname='.php'){ return rtrim(ROOT.Mod."$name.$exname",' .'); }
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
// dont run other function, that will be infinity loop

/****************************************************************/
