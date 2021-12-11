<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<?php
	@include_once(Func('user'));
	echo $User->Get('id','please login first');
?>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
