<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('user'));
$User->Update();
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
	<p ref="token">Spawn Time: {{ user.spawntime }}</p>
	<p ref="token">Token: {{ user.token }}</p>
	<p ref="timeout">Session Life: {{ user.life.h }}:{{ user.life.i }}:{{ user.life.s }}</p>
</div>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	createApp({
		data(){return{
			user:{
				id: '<?php echo $User->Get('id'); ?>',
				name: '<?php echo htmlentities($User->Get('name',' - ')); ?>',
				identity: '<?php echo htmlentities($User->Get('identity')); ?>',
				token: '<?php echo $User->Get('token',' - '); ?>',
				spawntime: '<?php echo $User->Get('spawntime',' - '); ?>',
				life: { h: "-", i: "-", s: "-", },
			},
			timeout: 60*60*6,
		}},
		mounted(){
			let timeout = this.user.spawntime, t, currentTime;
			setInterval(() => {
				currentTime = new Date().getTime() / 1000;
				t = this.timeout - (currentTime - timeout);
				this.user.life.h = parseInt(t / 60 / 60);
				this.user.life.i = parseInt((t / 60) % 60);
				this.user.life.s = parseInt(t % 60);
			},1000);
		}
	}).mount('#Profile');
</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
