<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<?php

?>

<script type="text/javascript" src="<?php echo JS('device-detect'); ?>"></script>
<script type="module">

	// console.log(Device.Is('Mobile'));
	console.log(document.body.clientWidth);
	
</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
