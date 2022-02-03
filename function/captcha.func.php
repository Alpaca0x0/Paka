<?php defined('INIT') or die('NO INIT'); ?>

<?php @include_once(Clas('captcha')); ?>

<?php
$Captcha = new Captcha();
$config = parse_ini_file(Conf('captcha','ini'),true);
$Captcha->Set($config);

// (isset($_GET['captcha']) && $Securimage->check($_GET['captcha']) === true)


