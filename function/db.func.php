<?php defined('INIT') or die('NO INIT'); ?>

<?php
@include_once(Clas('db'));
?>

<?php
$DB = new DB();
if(!$DB->Connect()){
	@include_once(Func('loger'));
	$Loger->Resp('error','database_cannot_connect'); 
};
