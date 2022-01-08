<?php defined('INIT') or die('NO INIT'); ?>

<?php @include_once(Clas('lang')); ?>

<?php

// DB Lang
// $Lang = new Lang('zh-tw');
// $Lang->Get(); // Get the datas of language table from database into $Lang->Datas
// $Lang->Map(); // Analyze and sort datas into $Lang->Map

// function T_DB($id, $replace=" - "){
// 	global $Lang;
// 	return isset($Lang->Map[$id])?$Lang->Map[$id]:$replace;
// }


// Ini File Lang
// $Lang = new Lang();
// $Lang->Init();

function T($lable, $category='', $replace=false) {
	global $Lang;
	if(isset($Lang->Map[$category][$lable])) { return $Lang->Map[$category][$lable]; }
	else if($replace) { return $replace; }
	else if(isset($Lang->BasicMap[$category][$lable])){ return $Lang->BasicMap[$category][$lable]; }
	else{ return "!LangError!"; }
}

function L($lable, $category='', $replace=false) {
	echo T($lable,$category,$replace);
	return true;
}

/****************************************************************/

// get lang from cookie
$lang = strtolower($_COOKIE['lang']);
// if in allowlist
if(in_array($lang, array_keys(Langs))){ }
// or try expload with "-", and check again
else if(in_array(explode('-',$lang)[0], array_keys(Langs))){
	$lang = explode('-',$lang)[0];
}
// give up, try to catch lang of browser and check allowlist
else{
	$lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
	$lang = explode(';', $lang)[0];
	if(!in_array($lang, array_keys(Langs))){
		// still not in allowlist, try expload with "-", and check again
		$lang = in_array(explode('-',$lang)[0], array_keys(Langs)) ? explode('-',$lang)[0] : $lang;
	}
}

$lang = strtolower(trim($lang));

$Lang = new Lang($lang);
setcookie("lang", $Lang->Init(), time()+60*60*24*30*2, ROOT);


