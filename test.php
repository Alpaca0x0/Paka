<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<?php
	@include_once(Func('user'));
	echo $User->Get('identity','no').'<br>';
	echo $User->Update().'<br>';
	// $_SESSION['timeout'] = time();
	echo $User->Get('identity','no').'<br>';
?>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
