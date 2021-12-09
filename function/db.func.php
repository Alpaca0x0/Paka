<?php defined('INIT') or die('NO INIT'); ?>

<?php
@include_once(Clas('db'));
?>

<?php
$DB = new DB();
if(!$DB->Connect()){ die('DB - Can not connect.'); };
