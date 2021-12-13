<?php defined('INIT') or die('NO INIT'); ?>

<?php
$config = @include_once(Conf('user'));
@include_once(Clas('user'));
?>

<?php
$User = new User();
$User->Init($config);
