<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function L($label) to return text in current language
@include_once(Func('captcha'));
$ac_regex = @include_once(Conf('account/regex')); // get the regex of register form
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<div class="ui container">

	<!-- Accordion -->
	<div class="ui styled fluid accordion" id="Account">
		<!-- Sign In -->
		<div class="title" :class="isLogin?'active':null"><i class="dropdown icon"></i> <?php L('Signin_accordion','Page/Account'); ?> </div>
		<div class="content" :class="isLogin?'active':null">
			<h2 class="content-title"><?php L('Signin','Page/Account'); ?></h2>
			<form class="ui form" id="Login" ref="login" method="POST">
				<div class="two fields">
					<div class="field error">
						<label>Username</label>
						<input type="text" name="username" id="username" ref='login_username' placeholder="Username">
					</div>
					<div class="field error">
						<label>Password</label>
						<input type="password" name="password" id="password" ref='login_password' placeholder="Password">
						<a href="#" class="ui red right floated button">(Forgot password?)</a>
					</div>
				</div>

				<div class="ui error message"></div>

				<h4 class="ui horizontal divider header">
					<i class="bar chart icon"></i> Random string here...
				</h4>

				<button class="fluid ui black button">Verify Identity</button>
			</form>
		</div>
		<!-- End Sign In -->


		<!-- Sign Up -->
		<div class="title" :class="isRegister?'active':null"><i class="dropdown icon"></i> <?php L('Signup_accordion','Page/Account'); ?> </div>
		<div class="content" :class="isRegister?'active':null">
			<h2 class="content-title"><?php L('Signup','Page/Account'); ?></h2>
			<form class="ui form" id="Register" ref="register" method="POST" autocomplete="off">
				<div class="two fields">
					<div class="field error four wide">
						<label>Username</label>
						<input type="text" name="username" id="username" ref='register_username' placeholder="Username">
					</div>
					<div class="field error twelve wide">
						<label>Password</label>
						<input type="password" name="password" id="password" ref='register_password' placeholder="Password">
					</div>
				</div>

				<div class="three fields">
					<div class="field error eight wide">
						<label>Email</label>
						<input type="email" name="email" id="email" ref='register_email' placeholder="E-Mail">
					</div>
					<div class="field error three wide">
						<label>Captcha</label>
						<input type="text" name="captcha" minlength="6" maxlength="6">
					</div>
					<div class="field">
						<img src="<?php echo $Captcha->Show(); ?>" onclick="this.src = '<?php echo $Captcha->Show(); ?>?' + Math.random();" title="Change" style="cursor:pointer;">
					</div>
					<!-- <div class="field error">
						<label>Gender</label>
						<div class="ui selection dropdown" id="gender" ref='register_gender'>
							<input type="hidden" name="gender">
							<i class="dropdown icon"></i>
							<div class="default text">Gender</div>
							<div class="menu">
								<div class="item" data-value="male" data-text="Male">
									<i class="male icon"></i> Male
								</div>
								<div class="item" data-value="female" data-text="Female">
									<i class="female icon"></i> Female
								</div>
								<div class="item" data-value="transgender" data-text="Transgender">
									<i class="transgender alternate icon"></i> Transgender
								</div>
								<div class="item" data-value="secret" data-text="Secret">
									<i class="ban icon"></i> Secret
								</div>
							</div>
						</div>
					</div> -->
				</div>

				<div class="inline field error">
					<div class="ui toggle checkbox">
						<input type="checkbox" name="terms" tabindex="0" class="hidden">
						<label>我同意遵守使用條款</label>
					</div>
				</div>

				<div class="ui error message"></div>

				<h4 class="ui horizontal divider header">
					<i class="bar chart icon"></i> Random string here...
				</h4>

				<button class="fluid ui black button" type="submit">Sing Up</button>
			</form>
		</div>
		<!-- End Sign Up -->

	</div>
	<!-- End Accordion -->
</div>

<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>
<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';

	// vue app
	const app = createApp({
		data(){return{
			isRegister: false,
			isLogin: false,
			login: {
				test: false,
			},
			register: {
				test: false,
			}
		}},
		mounted(){
			this.isRegister = <?php echo isset($_GET['register'])?'true':'false'; ?>;
			this.isLogin = !this.isRegister;
		} // vue
	}).mount('#Account');

	// ********************************************************************************** //

	// Initialize
	$('#Account.ui.accordion').accordion();
	// $('form#Register #gender.selection').dropdown();
	$('.ui.checkbox').checkbox();

	// defined form
	let form = new Array();
	form['register'] = $('form.ui.form#Register');
	form['login'] = $('form.ui.form#Login');

	// defined var
	let tables = new Array();
	
	// ********************************************************************************** //

	// Login form

	// Loger tables
	tables['login'] = {
		"login_successfully": 			"Login successfully",
		"data_missing": 				"Data missing",
		"username_format_not_match": 	"Username format not match",
		"password_format_not_match": 	"Password format not match",
		"db_cannot_query": 				"Database has some problems",
		"cannot_verify_your_identity": 	"Sorry, can not verify your identity! <br>Maybe you typed the incorrect info, try again!",
		'is_unverified': 				"Sorry, the account is unverified, please verify the email",
		'is_review': 					"Sorry, because of certain reasons, we are reviewing your account, you cannot login before we done the process",
		'account_not_alive': 			"Sorry, because of certain reasons, the account cannot be login now"
	};

	form['login'].form({
		on: 'change',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				Swal.loading();

				$.ajax({
					type: "POST",
					url: 'login.php',
					data: fields,
					dataType: 'json',
					success: function(resp){
						Loger.Log('info','Response',resp);
						// check if success
						let isSuccess = Loger.Check(resp,'success');
						let swal_config = isSuccess ? { timer:2000, } : {};
						Loger.Swal(resp, tables['login'], swal_config).then((val)=>{
							if(isSuccess){ window.location.replace('<?php echo ROOT; ?>'); }
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
			if(!form['login'].form('validate field','username')){ app.$refs.login_username.focus(); }
			else if(!form['login'].form('validate field','password')){ app.$refs.login_password.focus(); }
			return false;
		},
		fields: {
			username: {
				identifier: 'username',
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["username"]; ?>]',
						prompt : 'Your username must be at format {ruleValue}'
					}
				]
			},
			password: {
				identifier: 'password',
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["password"]; ?>]',
						prompt : 'Your password must be at format {ruleValue}'
					},
				]
			}
		}
	});

	// ********************************************************************************** //

	// Register form

	// Loger tables
	tables['register'] = {
		"db_insert_successfully": 		"Welcome to join us",
		"data_missing": 				"Data missing",
		"username_format_not_match": 	"Username format not match",
		"email_format_not_match": 		"Email format not match",
		"password_format_not_match": 	"Password format not match",
		"db_cannot_query": 				"Database has some problems",
		"username_exist": 				'Username already is exist',
		'email_exist': 					'Email is already exist',
		'database_cannot_connect': 		'Database has some problems when connecting',
		'db_cannot_insert': 			'Database has some problems when inserting your data',
		'cannot_send_email': 			'Something error when sending email',
		'error_send_email': 			'We got the error when sending email',
		'captcha_incorrect': 			'The captcha code incorrect',
	};

	// the username or email is exist
	let exist = new Array();
	exist['username'] = [];
	exist['email'] = [];


	// custom the function of authentication
	form['register'].form.settings.rules.exist = function(value,type){
		return !exist[type].includes(value);
	}

	form['register'].form({
		on: 'change',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				Swal.loading();

				$.ajax({
					type: "POST",
					url: 'register.php',
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
						let swal_config = isSuccess ? { timer: 2600, confirmButtonText: 'Great' } : {};
						Loger.Swal(resp, tables['register'], swal_config).then((val)=>{
							if(isSuccess){
								<?php $timeout = include(Conf('account/regex')); $timeout = $timeout['verify']['timeout']; ?>
								let timerInterval;
								Swal.fire({
									title: 'Go to check the email',
									html: 'The token will be timeout after <b></b> seconds.<br>(P.s. You can close this page, it\'s okay)',
									timer: <?php echo $timeout*1000; ?>,
									timerProgressBar: true,
									showCancelButton: false,
									showConfirmButton: false,
									allowOutsideClick: false,
									didOpen: () => {
										Swal.showLoading();
										const b = Swal.getHtmlContainer().querySelector('b');
										b.textContent = parseInt(Swal.getTimerLeft()/1000, 10);
										timerInterval = setInterval(() => {
											b.textContent = parseInt(Swal.getTimerLeft()/1000, 10);
										}, 1000);
									},
									willClose: () => {
										clearInterval(timerInterval);
									}
								}).then((result) => {
									// if (result.dismiss === Swal.DismissReason.timer) {
									// 	console.log('I was closed by the timer')
									// }
									Swal.fire({
										title: 'Timeout',
										html: 'If you have not verified the email...<br>Please go to register again',
										confirmButtonText: 'Register now',
									}).then(()=>{
										window.location.replace('?register');
									});
								});
							}
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
			// else if(!form['register'].form('validate field','gender')){ app.$refs.register_gender.focus(); }
			return false;
		},
		fields: {
			username: {
				identifier: 'username',
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
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["password"]; ?>]',
						prompt : 'Your password must be at format {ruleValue}'
					},
				]
			},
			email: {
				identifier: 'email',
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["email"]; ?>]',
						prompt : 'Your email must be at format {ruleValue}'
					},
					{
						type: 	'exist',
						value: 	'email',
						prompt: 'This email is exist'
					}
				]
			},
			captcha: {
				identifier: 'captcha',
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["captcha"]; ?>]',
						prompt : 'Your captcha must be at format {ruleValue}'
					},
				]
			},
			// gender: {
			// 	identifier: 'gender',
			// 	rules: [
			// 		{
			// 			type	 : 'empty',
			// 			prompt : 'Please select a gender'
			// 		},
			// 	]
			// },
			terms: {
				identifier: 'terms',
				rules: [
					{
						type	 : 'checked',
						prompt : 'You must agree to the terms and conditions'
					},
				]
			}
		}
	});
</script>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
