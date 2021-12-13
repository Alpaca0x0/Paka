<?php @include_once('../../init.php'); ?>

<?php
$User->Is('login') OR exit(header("Location: ".Root('index')));
?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
@include_once(Func('loger'));
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<div class="ui container" id="Profile">
	<p ref="id">ID: {{ user.id }}</p>
	<p ref="username">Username: {{ user.name }}</p>
	<p ref="identity">Identity: {{ user.identity }}</p>
	<p ref="identity">Token: {{ user.token }}</p>
</div>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	createApp({
		data(){return{
			user:{
				id: '<?php echo $User->Get('id'); ?>',
				name: '<?php echo htmlentities($User->Get('name',' - ')); ?>',
				identity: '<?php echo htmlentities($User->Get('identity')); ?>',
				token: '<?php echo $User->Get('token',' - '); ?>'
			}
		}},
		mounted(){
			//
		}
	}).mount('#Profile');
</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
