<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
$ac_regex = @include_once(Conf('account/regex')); // get the regex of register form
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<div class="ui container">

	<!-- Accordion -->
	<div class="ui styled fluid accordion" id="Account">
		<!-- Sign In -->
		<div class="title"><i class="dropdown icon"></i> I'm already a member </div>
		<div class="content">
			<h2 class="content-title">Sign In</h2>
			<form class="ui form" id="SignIn">

				<div class="two fields">
					<div class="field error">
						<label>First Name</label>
						<input placeholder="First Name" type="text">
					</div>
					<div class="field">
						<label>Last Name</label>
						<input placeholder="Last Name" type="text">
					</div>
				</div>

				<div class="inline field error">
					<div class="ui checkbox">
						<input type="checkbox" tabindex="0" class="hidden">
						<label>我同意遵守使用條款</label>
					</div>
				</div>

				<button class="fluid ui black button">Verify Identity</button>
			</form>
		</div>
		<!-- End Sign In -->




		<!-- Sign Up -->
		<div class="title active"><i class="dropdown icon"></i> I have no account, join now </div>
		<div class="content active">
			<h2 class="content-title">Sign Up</h2>
			<form class="ui form" id="SignUp" method="POST" autocomplete="off">

				<div class="two fields">
					<div class="field">
						<label>Username</label>
						<input type="text" name="username" id="username" ref='username' placeholder="Username" value="alpaca0x0">
					</div>
					<div class="field error">
						<label>Password</label>
						<input type="password" name="password" id="password" ref='password' placeholder="Password" value="passw0rd">
					</div>
				</div>

				<div class="two fields">
					<!-- <div class="field">
						<label>Gender</label>
						<select name="gender" class="ui dropdown" id="gender">
							<option value="">Gender</option>
							<option value="male"><i class="male icon"></i>Male</option>
							<option value="female"><i class="female icon"></i>Female</option>
						</select>
					</div> -->
					<div class="field error">
						<label>Email</label>
						<input type="email" name="email" id="email" ref='email' placeholder="E-Mail" value="alpaca0x0@gmail.com">
					</div>
					<div class="field error">
						<label>Gender</label>
						<div class="ui selection dropdown" id="gender" ref='gender'>
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
					</div>
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
<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>
<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	createApp({
		datas(){return{
			//
		}},
		mounted(){
			let _this = this;

			// Register form

			// Initialize
			$('#Account.ui.accordion').accordion();
			$('form#SignUp #gender.selection').dropdown();
			$('.ui.checkbox').checkbox();

			// Loger tables
			Loger.Tables['Display'] = {
				"db_insert_successfully": 		"Welcome to join us",
				"data_missing": 				"Data missing",
				"username_format_not_match": 	"Username format not match",
				"email_format_not_match": 		"Email format not match",
				"password_format_not_match": 	"Password format not match",
				"db_cannot_query": 				"Database has some problems",
				"username_exist": 				'Username is exist',
				'email_exist': 					'Email is exist',
				'database_cannot_connect': 		'Database has some problems when connecting',
				'db_cannot_insert': 			'Database has some problems when inserting your data',
			}

			// the username or email is exist
			let exist = new Array();
			exist['username'] = [];
			exist['email'] = [];

			// Authenticate form
			let form = $('form.ui.form#SignUp');

			// custom the function of authentication
			form.form.settings.rules.exist = function(value,type){
				return !exist[type].includes(value);
			}

			form.form({
				on: 'change',
				inline: true,
				keyboardShortcuts: false,
				// delay: 800,
				onSuccess: function(event, fields){
					event.preventDefault();

					// if(fields['email'].includes(exist['email'])){}
					// if(fields['email'].includes(exist['email'])){}

					this.classList.add('loading');

					console.log(`onSuccess()`);

		    		$.ajax({
		    			type: "POST",
		    			url: 'register.php',
		    			data: fields,
		    			dataType: 'json',
		    			success: (resp)=>{
		    				console.log(`ajax success()`);
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
		    				Loger.Swal(resp).then(()=>{
		    					console.log(`swal then`);
								// update the UI status
								form.form('validate form');
				    			this.classList.remove('loading');
		    				});
		    			},
		    		}).then(()=>{
		    			console.log(`ajax then`);
		    		});
					return false;
				},
				onFailure: function(formErrors, fields){
					console.log(`onFailure()`);
					if(!form.form('validate field','username')){ console.log('focus username'); _this.$refs.username.focus(); }
					else if(!form.form('validate field','password')){ console.log('focus password'); _this.$refs.password.focus(); }
					else if(!form.form('validate field','email')){ console.log('focus email'); _this.$refs.email.focus(); }
					else if(!form.form('validate field','gender')){ console.log('focus gender'); _this.$refs.gender.focus(); }
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
					},
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
			}); // semantic
		} // vue
	}).mount('form#SignUp');
</script>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
