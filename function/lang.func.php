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

function T($lable, $category='', $replace=' - ') {
	global $Lang;
	$args = func_get_args();
	if(isset($Lang->Map[$category][$lable])) { return $Lang->Map[$category][$lable]; }
	else if(isset($args[2])) { return $replace; }
	else if(isset($Lang->BasicMap[$category][$lable])){ return $Lang->BasicMap[$category][$lable]; }
	else{ return "!LangError!"; }
}

/****************************************************************/

$Lang = new Lang('zh-tw');
$Lang->Init();

