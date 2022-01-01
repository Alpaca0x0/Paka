<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('user'));
$User->Update();
$User->Is('login') OR exit(header("Location: ".Root('index')));
@include_once(Func('lang')); # Using the function T($id) to return text in current language
@include_once(Func('loger'));
$ac_regex = @include_once(Conf('account/regex')); // get the regex of register form
$iniMaxFileSize = ini_get('upload_max_filesize');
$maxFileSize = 1024*1024*5; // 5mb
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<script type="text/javascript" src="<?php echo JS('cropper.min'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo CSS('cropper.min'); ?>">

<div class="ui container" id="Profile">

	<form enctype="multipart/form-data" class="ui form" onsubmit="return false;" id="Profile">
		<h4 class="ui dividing header">Primary Info</h4>
		<div class="field">
			<div class="five fields">
				<div class="field two wide">
					<label>ID</label>
					<input type="input" class="ui fluid button" :value="user.id" disabled>
				</div>
				<div class="field two wide">
					<label>Identity</label>
					<input type="input" class="ui fluid button" :value="user.identity" disabled>
				</div>
				<div class="field three wide">
					<label>Username</label>
					<input type="input" class="ui fluid button" :value="user.name" disabled>
				</div>
				<div class="field six wide">
					<label>E-Mail</label>
					<input type="input" class="ui fluid button" :value="user.email" disabled>
				</div>
				<div class="field">
					<label>Session Dead-Time</label>
					<input type="input" class="ui fluid button" :value="user.life.h + ':' + user.life.i + ':' + user.life.s" disabled>
				</div>
			</div>
		</div>

		<h4 class="ui dividing header">Secondary Info</h4>
		<div class="field">
			<div class="four fields">
				<div class="field">
					<label>Avatar</label>
					<input id="avatar" type="file" name="avatar" accept="image/png, image/jpeg" style="display: none;">
					<label for='avatar'>
						<a class="ui medium image" style="cursor:pointer;">
							<div class="ui small circular rotate left reveal image">
									<img id="avatarCurrent" :src="user.avatar" style="background-color: black;" class="visible content">
									<img :src="'<?php echo IMG('default','png'); ?>'" class="hidden content">
							</div>
						</a>
					</label>
					<div id="avatarModal" class="ui modal">
						<i class="close icon"></i>
						<div class="header">Avatar</div>
						<div class="image content">
							<div class="ui medium image" style="width: 50%">
								<img id="avatarView" class="ui fluid image">
							</div>
							<div id="avatarPreview" class="ui circular image" style="overflow: hidden; width: 200px; height: 200px"></div>
						</div>

						<div class="actions">
							<div class="ui black deny button">Cancel</div>
							<div class="ui positive right labeled icon button">Crop<i class="checkmark icon"></i></div>
						</div>
					</div>
				</div>
				<div class="field">
					<label>Nick Name</label>
					<input type="text"  v-model="fields.nickname.value" name="nickname" class="ui fluid" placeholder="Nick Name">
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
					<input type="date" class="ui fluid button" name="birthday" v-model="user.birthday">
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
		</div><br>
		
		<div class="ui hidden divider"></div>
	</form>
</div>

<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';

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
				avatar: '<?php $temp=$User->Get('avatar',false); echo $temp===false?IMG('default','png'):'data:image/jpeg;base64, '.base64_encode($temp); ?>',
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


	let form = new Array();
	form['profile'] = $('form#Profile').first();
	form['profile'].find('#gender.selection').first().dropdown('set selected', Profile.user.gender);

	let tables = new Array();
	tables['profile'] = {
		"update_successfully": 			"Update successfully",
		"is_logout": 					"Sorry, you must login before doing this operation!",
		"data_missing": 				"Data missing",
		"birthday_format_not_match": 	"Birthday format not match",
		"nickname_format_not_match": 	"Nickname format not match",
		"gender_format_not_match": 		"Gender format not match",
		"db_cannot_update": 			"Database has some problems",
	};

	

	$('form#Profile').first().form({
		on: 'change',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				let datas = new FormData(form['profile'][0]);

				console.log(datas);

				$.ajax({
					type: "POST",
					url: '<?php echo Page('account/edit'); ?>',
					data: datas,
					dataType: 'json',
					processData: false,  // tell jQuery not to process the data
					contentType: false,  // tell jQuery not to set contentType
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
						Loger.Swal(resp, tables['profile'], swal_config).then((val)=>{
							if(isSuccess){ window.location.reload(); }
							// update the UI status
							// it will call back to the onSuccess()
							form['profile'].form('validate form'); // fix: in promise, event is undefined
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



	let avatar = document.querySelector('input#avatar');
	let avatarView = document.querySelector('img#avatarView');
	let cropper = new Cropper(avatarView);
	let avatarNow; // blob
	
	$('input#avatar').on('change', function(){
		cropper.destroy();
		let image = avatar.files[0];
		if(!image){ return; }
		avatarView.src = URL.createObjectURL(image);
		cropper = new Cropper(avatarView,{
			viewMode: 2,
			aspectRatio: 4/4,
			preview: '#avatarPreview',
		});
		$('#avatarModal.ui.modal').modal({
			// closable: false,
			onDeny: ()=>{
				let file = new File([avatarNow], "",{type:image.type, lastModified:new Date().getTime()});
				let container = new DataTransfer();
				container.items.add(file);
				avatar.files = container.files;
			},
			onHide: ()=>{
				let file = new File([avatarNow], "",{type:image.type, lastModified:new Date().getTime()});
				let container = new DataTransfer();
				container.items.add(file);
				avatar.files = container.files;
			},
			onApprove: ()=>{
				cropper.getCroppedCanvas({
					width: 320,
					height: 320,
				}).toBlob(function(blob){
					avatarNow = blob;
					avatarCurrent.src = URL.createObjectURL(blob);
					let file = new File([blob], image.name,{type:image.type, lastModified:new Date().getTime()});
					let container = new DataTransfer();
					container.items.add(file);
					avatar.files = container.files;
				},image.type,0.8);
			}
		}).modal('show');
	});


</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
