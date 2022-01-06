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

function L($lable, $category='', $replace=false) {
	global $Lang;
	if(isset($Lang->Map[$category][$lable])) { return $Lang->Map[$category][$lable]; }
	else if($replace) { return $replace; }
	else if(isset($Lang->BasicMap[$category][$lable])){ return $Lang->BasicMap[$category][$lable]; }
	else{ return "!LangError!"; }
}

/****************************************************************/

$lang = strtolower($_COOKIE['lang']);
if(!in_array($lang, array_keys(Langs))){
	$lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];
	$lang = explode(';', $lang)[0];
	$lang = strtolower($lang);
}
setcookie("lang", $lang, time()+60*60*24*30*2, ROOT);

$Lang = new Lang($lang);
$Lang->Init();


