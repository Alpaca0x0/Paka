<?php defined('INIT') or die('NO INIT'); ?>

<?php
@include_once(Func('lang'));
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<meta name="viewport" content="width=device-width, maximum-scale=1, user-scalable=no" />
	<title><?php L('AlpacaTech'); ?></title>

	<!-- A, custom js on this website -->
	<script type="text/javascript" src="<?php echo JS('init','php'); ?>"></script>

	<!-- jquery -->
	<script type="text/javascript" src="<?php echo Frame('jquery/jquery@3.6.0.min','js'); ?>"></script>

	<!-- Vue 3 Framework -->
	<!-- ES Module, Use import { createApp } from 'path'; -->

	<!-- semantic-ui@2.4.2 -->
	<link rel="stylesheet" href="<?php echo Frame('semantic-ui/2.4.2/dist/semantic.min','css'); ?>">
	<script src="<?php echo Frame('semantic-ui/2.4.2/dist/semantic.min','js'); ?>"></script>

	<!-- SweetAlert2 -->
	<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>
	<script type="text/javascript">
		Swal.mixin({
			icon: 'info',
			title: 'info',
			html: false,
			timer: false,
			timerProgressBar: true,
			// confirmButtonText: 'Got it',
		});
	</script>
</head>
<body>

<div class="ui active dimmer" id="Loader"><div class="ui text loader">Loading...</div></div>

<!-- Header -->
<?php
