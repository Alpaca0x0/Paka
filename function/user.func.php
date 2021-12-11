<?php defined('INIT') or die('NO INIT'); ?>

<?php
@include_once(Clas('user'));
?>

<?php
$User = new User();
$User->Init();
