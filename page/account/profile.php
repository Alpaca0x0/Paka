<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('user'));
$User->Update();
$User->Is('login') OR exit(header("Location: ".Root('index')));
?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
@include_once(Func('loger'));
$ac_regex = @include_once(Conf('account/regex')); // get the regex of register form
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<div class="ui container" id="Profile">
	<!-- <p ref="id">ID: {{ user.id }}</p>
	<p ref="username">Username: {{ user.name }}</p>
	<p ref="identity">Identity: {{ user.identity }}</p>
	<p ref="token">Spawn Time: {{ user.spawntime }}</p>
	<p ref="token">Token: {{ user.token }}</p>
	<p ref="timeout">Session Life: {{ user.life.h }}:{{ user.life.i }}:{{ user.life.s }}</p> -->

	<form class="ui form" onsubmit="return false;" id="Profile">
		<h4 class="ui dividing header">Primary Info</h4>
		<div class="field">
			<div class="four fields">
				<div class="field">
					<label>ID</label>
					<input type="input" class="ui fluid button" :value="user.id" disabled>
				</div>
				<div class="field">
					<label>Identity</label>
					<input type="input" class="ui fluid button" :value="user.identity" disabled>
				</div>
				<div class="field">
					<label>Username</label>
					<input type="input" class="ui fluid button" :value="user.name" disabled>
				</div>
				<div class="field">
					<label>E-Mail</label>
					<input type="input" class="ui fluid button" :value="user.email" disabled>
				</div>
			</div>
			<div class="two fields">
				<div class="field">
					<label>Token (Don't send to anyone you don't trust)</label>
					<input type="button" class="ui fluid button" :value="user.token">
				</div>
				<div class="field">
					<label>Session Dead-Time</label>
					<input type="input" class="ui fluid button" :value="user.life.h + ':' + user.life.i + ':' + user.life.s" disabled>
				</div>
			</div>
		</div>

		<h4 class="ui dividing header">Secondary Info</h4>
		<div class="field">
			<div class="three fields">
				<div class="field">
					<label>Nick Name</label>
					<input :type="fields.editing=='nickname'?'input':'button'" @focus="fields.editing='nickname'" @blur="fields.editing=''" v-model="fields.nickname.value" name="nickname" class="ui fluid button" placeholder="Nick Name">
				</div>
				<div class="field">
					<label>Gender</label>
					<div class="ui selection dropdown" id="gender">
						<input type="hidden" name="gender">
						<i class="dropdown icon"></i>
						<div class="default text">Gender</div>
						<div class="menu">
							<div class="item" data-value="male" data-text="Male">
								<i class="male icon"></i> {{ tables.gender.male }}
							</div>
							<div class="item" data-value="female" data-text="Female">
								<i class="female icon"></i> {{ tables.gender.female }}
							</div>
							<div class="item" data-value="transgender" data-text="Transgender">
								<i class="transgender alternate icon"></i> {{ tables.gender.transgender }}
							</div>
							<div class="item" data-value="secret" data-text="Secret">
								<i class="ban icon"></i> {{ tables.gender.secret }}
							</div>
						</div>
					</div>
				</div>
				<div class="field">
					<label>Birthday</label>
					<input type="date" class="ui fluid button">
				</div>
			</div>
		</div>

		<h4 class="ui dividing header">Operation</h4>
		<div class="field">
			<h5 class="ui pink header">Change Password</h5>
			<div class="two fields">
				<div class="field">
					<label>Original</label>
					<input type="password" @focus="fields.editing='password'" @blur="fields.editing=''" class="ui fluid button" placeholder="Original password">
				</div>
				<div class="field">
					<label>New</label>
					<input type="password" @focus="fields.editing='password'" @blur="fields.editing=''" class="ui fluid button" placeholder="New password">
				</div>
			</div>
		</div>

		<div class="ui animated fade submit right floated green button " tabindex="0">
			<div class="visible content"><i class="ui icon sync alternate"></i> Update</div>
			<div class="hidden content"><i class="ui icon paper plane"></i> Submit</div>
		</div>
	</form>
</div>

<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';

	let form = new Array();

	const Profile = createApp({
		data(){return{
			tables:{
				gender:{
					male: 'Male',
					female: 'Female',
					transgender: 'Transgender',
					secret: 'Secret'
				}
			},
			fields:{
				editing: "",
				nickname: {
					value: "",
				},
			},
			user:{
				id: '<?php echo $User->Get('id'); ?>',
				name: '<?php echo htmlentities($User->Get('name',' - ')); ?>',
				email: '<?php echo htmlentities($User->Get('email',' - ')); ?>',
				identity: '<?php echo htmlentities($User->Get('identity')); ?>',
				// token: '<?php echo $User->Get('token',' - '); ?>',
				spawntime: '<?php echo $User->Get('spawntime',' - '); ?>',
				life: { h: "00", i: "00", s: "00", },
				nickname: '<?php echo $User->Get('nickname',''); ?>',
				gender: '<?php echo $User->Get('gender',''); ?>',
				birthday: '<?php echo $User->Get('birthday',''); ?>',
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

			this.fields.nickname.value = this.user.nickname;
		}
	}).mount('div#Profile');

	form['Profile'] = $('form#Profile').first();

	form['Profile'].find('#gender.selection').first().dropdown('set selected', Profile.user.gender);

	$('form#Profile').first().form({
		on: 'change',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				$.ajax({
					type: "POST",
					url: '#',
					data: fields,
					dataType: 'json',
					success: (resp)=>{
						Loger.Log('info','Response',resp);
						// check if data is exist
						if(Loger.Have(resp,['username_exist','email_exist'])){
							// log exist datas
							Loger.Log('warning','Exist Datas',exist);
							// if username is exist
		    				if(Loger.Have(resp,'username_exist') && !exist['username'].includes(fields['username'])){
								exist['username'].push(fields['username']);
		    				}
		    				// if email is exist
		    				if(Loger.Have(resp,'email_exist') && !exist['email'].includes(fields['email'])){
								exist['email'].push(fields['email']);
		    				};
						}
						// check if success
						let isSuccess = Loger.Check(resp,'success');
						let swal_config = isSuccess ? { timer: 3200, confirmButtonText: 'Login now!' } : {};
						Loger.Swal(resp, tables['register'], swal_config).then((val)=>{
							if(isSuccess){ window.location.replace('?login'); }
							// update the UI status
							// it will call back to the onSuccess()
							form['login'].form('validate form'); // fix: in promise, event is undefined
						});
					},
				}).then(()=>{
					this.classList.remove('loading');
				});
			}
			return false;
		},
		onFailure: function(formErrors, fields){
			if(!form['register'].form('validate field','username')){ app.$refs.register_username.focus(); }
			else if(!form['register'].form('validate field','password')){ app.$refs.register_password.focus(); }
			else if(!form['register'].form('validate field','email')){ app.$refs.register_email.focus(); }
			else if(!form['register'].form('validate field','gender')){ app.$refs.register_gender.focus(); }
			return false;
		},
		fields: {
			username: {
				identifier: 'username',
				optional: true,
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["username"]; ?>]',
						prompt : 'Your username must be at format {ruleValue}'
					},
					{
						type: 	'exist',
						value: 	'username',
						prompt: 	'This usernmae is exist'
					}
				]
			},
			password: {
				identifier: 'password',
				optional: true,
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["password"]; ?>]',
						prompt : 'Your password must be at format {ruleValue}'
					},
				]
			},
			email: {
				identifier: 'email',
				optional: true,
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["email"]; ?>]',
						prompt : 'Your email must be at format {ruleValue}'
					},
					{
						type: 	'exist',
						value: 	'email',
						prompt: 	'This email is exist'
					}
				]
			},
			gender: {
				identifier: 'gender',
				rules: [
					{
						type	 : 'empty',
						prompt : 'Please select a gender'
					},
				]
			}
			// terms: {
			// 	identifier: 'terms',
			// 	rules: [
			// 		{
			// 			type	 : 'checked',
			// 			prompt : 'You must agree to the terms and conditions'
			// 		},
			// 	]
			// }
		}
	});

</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
