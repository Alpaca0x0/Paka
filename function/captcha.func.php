<?php defined('INIT') or die('NO INIT'); ?>

<?php @include_once(Clas('captcha')); ?>

<?php
$Captcha = new Captcha();
$Captcha->Set([
	'charset' => 'abcdefghkmnprtuvwyzABCDEFGHJKLMNPQRTUVWXYZ2346789',
	'code_length' => 6,
]);

// (isset($_GET['captcha']) && $Securimage->check($_GET['captcha']) === true)


