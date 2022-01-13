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
		<!-- Accordion -->
		<div class="ui styled fluid accordion" id="info">
			<!-- Primary Info -->
			<div class="title" :class="view=='primary'?'active':null"><i class="dropdown icon"></i> Primary Info </div>
			<div class="content" :class="view=='primary'?'active':null">
				<!-- <h2 class="content-title">Sign In</h2> -->
				<!-- <h4 class="ui dividing header">Primary Info</h4> -->
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
			</div>

			<!-- Secondary Info -->
			<div class="title" :class="view=='secondary'?'active':null"><i class="dropdown icon"></i> Secondary Info </div>
			<div class="content" :class="view=='secondary'?'active':null">
				<!-- <h4 class="ui dividing header">Secondary Info</h4> -->
				<form enctype="multipart/form-data" class="ui form" onsubmit="return false;" id="Profile">
					<div class="field">
						<label>Avatar</label>
						<input id="avatar" type="file" name="avatar" accept="image/png, image/jpeg" style="display: none;">
						<div class="ui container center aligned">
							<label for='avatar' style="cursor:pointer;">
								<div class="ui circular image" onmouseenter="$(this).dimmer('show');" onmouseleave="$(this).dimmer('hide');">
									<img id="avatarCurrent" class="ui small image circular" :src="user.avatar" style="background-color: black;">
									<div class="ui center aligned dimmer">
										<div class="content">
											<h2 class="ui inverted header">Change</h2>
										</div>
									</div>
								</div>
							</label>
						</div>
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

					<div class="three fields">
						<div class="field">
							<label>Nick Name</label>
							<input type="text" :value="user.nickname" name="nickname" class="ui fluid" placeholder="Nick Name">
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
							<input type="date" class="ui fluid button" name="birthday" :value="user.birthday">
						</div>
					</div><!-- End fields -->
					<button class="ui right floated green button"><i class="ui icon sync alternate"></i> Update Profile</button>
					<div class="ui hidden divider"></div><br>
				</form>
			</div>

			<!-- Operation -->
			<div class="title" :class="view=='operation'?'active':null"><i class="dropdown icon"></i> Operation </div>
			<div class="content" :class="view=='operation'?'active':null">
				<!-- <h4 class="ui dividing header">Operation</h4> -->
				<form class="ui form" onsubmit="return false;" id="Operation">
					<div class="field">
						<h5 class="ui pink header">Change Password</h5>
						<div class="two fields">
							<div class="field">
								<label>Original</label>
								<input type="password" name="old_password" class="ui fluid button" placeholder="Original password">
							</div>
							<div class="field">
								<label>New</label>
								<input type="password" name="new_password" class="ui fluid button" placeholder="New password">
							</div>
						</div>
					</div>
					<button class="ui right floated green button"><i class="ui icon sync alternate"></i> Update</button>
					<div class="ui hidden divider"></div><br>
				</form>
			</div>
		</div>
		<br>
		<div class="ui hidden divider"></div>
		<br>
</div>


<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>

<script type="module">
	import { createApp, ref, reactive } from '<?php echo Frame('vue/vue','js'); ?>';

	const Profile = createApp({
		setup(){
			<?php
			$temp = 'secondary';
			if(isset($_GET['primary'])){ $temp = 'primary'; }
			else if(isset($_GET['secondary'])){ $temp = 'secondary'; }
			else if(isset($_GET['operation'])){ $temp = 'operation'; }
			?>
			let view = ref('<?php echo $temp; ?>');

			const tables = reactive({
				gender: {
					male: 'Male',
					female: 'Female',
					transgender: 'Transgender',
					secret: 'Secret'
				}
			});

			let user = reactive({
				id: '<?php echo $User->Get('id'); ?>',
				name: '<?php echo htmlentities($User->Get('name',' - ')); ?>',
				email: '<?php echo htmlentities($User->Get('email',' - ')); ?>',
				identity: '<?php echo htmlentities($User->Get('identity')); ?>',
				avatar: '<?php $temp=$User->Get('avatar',false); echo $temp===false?IMG('default','png'):'data:image/jpeg;base64, '.base64_encode($temp); ?>',
				spawntime: '<?php echo $User->Get('spawntime',' - '); ?>',
				life: { h: "00", i: "00", s: "00", },
				nickname: '<?php echo $User->Get('nickname',''); ?>',
				gender: '<?php echo $User->Get('gender',''); ?>',
				birthday: '<?php echo $User->Get('birthday',''); ?>',
			});

			const timeout = 60*60*6; let currentTime, t;
			setInterval(() => {
				currentTime = new Date().getTime() / 1000;
				t = timeout - (currentTime - user.spawntime);
				user.life.h = parseInt(t / 60 / 60);
				user.life.i = parseInt((t / 60) % 60);
				user.life.s = parseInt(t % 60);
			},1000);

			return {
				view, tables, user, timeout,
			};
		},
		mounted(){
			//
		}
	}).mount('div#Profile');

	$('#Profile .ui.accordion#info').accordion();

	let form = new Array();
	form['profile'] = $('form#Profile').first();
	form['operation'] = $('form#Operation').first();

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
	tables['operation'] = {
		"is_logout": 					"Sorry, you must login before doing this operation!",
		"data_missing": 				"Data missing",
		"password_format_not_match": 	"Password format not match",
		"update_successfully": 			"Update successfully",
		"nothing_changed": 				"Nothing changed, maybe original password incorrect or the new password is same as older",
		"db_cannot_update": 			"Database has some problems",
	};

	

	form['profile'].form({
		on: 'change',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				Swalc.loading().fire();

				let datas = new FormData(form['profile'][0]);

				$.ajax({
					type: "POST",
					url: '<?php echo Page('account/edit'); ?>',
					data: datas,
					dataType: 'json',
					processData: false,	// tell jQuery not to process the data
					contentType: false,	// tell jQuery not to set contentType
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
						let swal_config = isSuccess ? { timer: 3200, confirmButtonText: 'Great' } : {};
						Loger.Swal(resp, tables['profile'], swal_config).then((val)=>{
							if(isSuccess){
								Swalc.loading().fire('Refresh the interface');
								window.location.reload();
							}
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
			console.error(formErrors);
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




	form['operation'].form({
		on: 'change',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				Swal.fire({
					title: 'Waiting...',
					html: 'Please waiting for response...',
					timerProgressBar: true,
					showCancelButton: false,
					showConfirmButton: false,
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				let datas = new FormData(form['operation'][0]);

				$.ajax({
					type: "POST",
					url: '<?php echo Page('account/repassword'); ?>',
					data: datas,
					dataType: 'json',
					processData: false,	// tell jQuery not to process the data
					contentType: false,	// tell jQuery not to set contentType
					success: (resp)=>{
						Loger.Log('info','Response',resp);
						// check if success
						let isSuccess = Loger.Have(resp,'update_successfully');
						let swal_config = isSuccess ? { timer: 3200, confirmButtonText: 'Great' } : {};
						Loger.Swal(resp, tables['operation'], swal_config).then((val)=>{
							if(isSuccess){ window.location.reload(); }
							// update the UI status
							// it will call back to the onSuccess()
							form['operation'].form('validate form'); // fix: in promise, event is undefined
						});
					},
				}).then(()=>{
					this.classList.remove('loading');
				});
			}
			return false;
		},
		onFailure: function(formErrors, fields){
			console.error(formErrors);
		},
		fields: {
			new_password: {
				identifier: 'new_password',
				optional: true,
				rules: [
					{
						type	 : 'regExp[<?php echo $ac_regex["password"]; ?>]',
						prompt : 'Your password must be at format {ruleValue}'
					},
				]
			}
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
