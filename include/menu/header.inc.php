<?php defined('INIT') or die('NO INIT'); ?>

<?php 
	@include_once(Func('lang'));
	@include_once(Func('user'));
	$User->Update();
?>
<script type="text/javascript" src="<?php echo JS('user'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>


<div id="Menu">

	<!-- Sidebar -->
	<div class="ui left vertical inverted sidebar menu" id="Sidebar">
	<!-- <div class="ui sidebar inverted vertical menu" id="Sidebar"> -->
		<a class="item" :class="index.isActive" :href="index.link">{{ index.name }}</a>
		<?php if($User->Get('identity')==='admin'){ ?>
		<a class="item" :class="admin.isActive" :href="admin.link">{{ admin.name }}</a>
		<?php } ?>
		<a class="item" :class="about.isActive" :href="about.link">{{ about.name }}</a>
		<a class="item" :class="announcement.isActive" :href="announcement.link">{{ announcement.name }}</a>
		<!-- <a class="item" :class="index.isActive" :href="index.link">{{ index.name }}</a> -->
		<a class="item" :class="forum.isActive" :href="forum.link">{{ forum.name }}</a>
	</div>
	<!-- Pusher -->
	<div class="pusher"></div>
	<!-- End Sidebar -->

	<!-- Navbar -->
	<div class="ui inverted segment attached" id="Navbar">
		<div class="ui top fixed inverted animated menu">
			<div class="ui animated black button" onclick="$('.ui.sidebar').sidebar('toggle');" tabindex="0" v-if="sidebar.display">
				<div class="visible content">
					<i class="bars icon"></i>
				</div>
				<div class="hidden content">
					<i class="right arrow icon"></i>
				</div>
			</div>

			<template v-if="navbar.display">
				<a class="header item" :class="index.isActive" :href="index.link">
					<img class="logo" src="<?php echo IMG('default.png'); ?>">
					{{ index.projectName }}
			    </a>
				<a class="item" :class="about.isActive" :href="about.link">{{ about.name }}</a>
				<a class="item" :class="announcement.isActive" :href="announcement.link">{{ announcement.name }}</a>
				<!-- <a class="item" :class="index.isActive" :href="index.link">{{ index.name }}</a> -->
				<a class="item" :class="forum.isActive" :href="forum.link">{{ forum.name }}</a>
				<?php if($User->Get('identity')==='admin'){ ?>
				<a class="item" :class="admin.isActive" :href="admin.link">{{ admin.name }}</a>
				<?php } ?>
			</template>

			<div class="right menu">

				<!-- Change Language -->
				<div class="ui item dropdown right floating icon" id="languages" data-content="Change Language">
					<i class="world icon"></i><!-- &nbsp;Language -->
					<div class="menu transition hidden " style="display: block !important;" :style="{width: clientWidth>550?'320px':'180px', }">
						<div class="header">Change Language</div>
						<div class="ui icon search input">
							<i class="search icon"></i>
							<input type="text" placeholder="Search Language">
						</div>
						<div class="scrolling menu">					
							<template v-for="lang,key in languages.langs">
								<div class="item" :class="{ disabled:!lang.isSupported, selected:(languages.current===key) }" :data-percent="lang['percent']" :data-value="key" :data-english="lang['english']">
									<span class="description">{{ clientWidth>550?lang.english:'&nbsp;' }}</span>
									{{ lang['description']?lang['description']:lang['english'] }}
								</div>
							</template>
							<!-- 
							<div class="item" data-percent="0" data-value="en-us" data-english="English">
								<span class="description">English</span>
								English
							</div>
							<div class="item selected" data-percent="0" data-value="zh-tw" data-english="Chinese (Taiwan)">
								<span class="description">中文 (臺灣)</span>
								Chinese (Taiwan)
							</div> 
							-->
						</div>
					</div>
				</div>
				<!-- End Change Language -->

				<!-- Is Login -->
				<div class="ui item dropdown link" v-if="account.isLogin" id="account">
					<i class="user icon"></i> {{ account.name }} <i class="dropdown icon"></i>
					<div class="menu">
						<a class="item" :class="profile.isActive" :href="profile.link"><i class="edit icon"></i> {{ profile.name }}</a>
						<div class="divider"></div>
						<a class="item" @click="logout"><i class="sign out alternate icon"></i> Logout</a>
					</div>
				</div>
				<!-- Not Login -->
				<a class="ui item" :class="account.isActive" :href="account.link" v-else>{{ account.name }}</a>
			</div>
		</div>
	</div>
</div>
<!-- End Menu -->




<script type="module">
		import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';

		// Menu
		const Menu = createApp({
				data(){return{
						clientWidth: document.body.clientWidth,
						clientHeight: document.body.clientHeight,

						navbar: {
							display: false,
						},
						sidebar: {
							display: false,
						},

						index: {
							id: '<?php echo ID('page',Root('index')); ?>',
							name: '<?php L('Index','Navbar'); ?>',
							projectName: '<?php L('Project_Name'); ?>',
							isActive: false,
							link: `<?php echo Root('index'); ?>`,
						},
						forum: {
							id: '<?php echo ID('page',Root('forum')); ?>',
							name: '<?php L('Forum','Navbar', 'Forum'); ?>',
							isActive: false,
							link: `<?php echo Root('forum'); ?>`,
						},
						about: {
							id: '<?php echo ID('page',Root('about')); ?>',
							name: '<?php L('About','Navbar', 'About'); ?>',
							isActive: false,
							link: `<?php echo Root('about'); ?>`,
						},
						announcement: {
							id: '<?php echo ID('page',Root('announcement')); ?>',
							name: '<?php L('Announcement','Navbar', 'Announcement'); ?>',
							isActive: false,
							link: `<?php echo Root('announcement'); ?>`,
						},
						account: {
							id: ['<?php echo ID('page',Page('account/index')); ?>', '<?php echo ID('page',Page('account/verify')); ?>'],
							name: '<?php L('Account','Navbar', 'Account'); ?>',
							isActive: false,
							link: `<?php echo Page('account/index'); ?>`,
							isLogin: <?php echo $User->Is('Login')?"true":"false"; ?>,
						},
						profile: {
							id: '<?php echo ID('page', Page('account/profile')); ?>',
							name: '<?php L('Profile','Navbar', 'Profile'); ?>',
							isActive: false,
							link: '<?php echo Page('account/profile'); ?>',
						},
						languages: {
							langs: <?php echo json_encode(Langs); ?>,
							current: '<?php echo $Lang->Lang; ?>',
						},

						<?php if($User->Get('identity')==='admin'){ ?>
						admin: {
							id: '<?php echo ID('page', Root('admin')); ?>',
							name: 'Admin',
							isActive: false,
							link: '<?php echo Root('admin'); ?>',
						},
						<?php } ?>
				}},
				methods:{
					resize: function(){
						this.clientWidth = document.body.clientWidth;
						this.clientHeight = document.body.clientHeight;
						// console.log(`${Menu.clientWidth}x${Menu.clientHeight}`);
						if(this.clientWidth < 640){ this.navbar.display = false; }
						else{ this.navbar.display = true; }
						this.sidebar.display = !this.navbar.display;
					},
					logout: function(){
						Swal.fire({
							title: 'Do you want to logout?',
							icon: 'warning',
							showDenyButton: true,
							confirmButtonText: 'Logout now',
							denyButtonText: 'Wait, no!',
							focusDeny: true,
						}).then((result) => {
							if (result.isConfirmed) {
								Swalc.loading('Logout...').fire();
								User.Logout('<?php echo Page('account/logout'); ?>', '<?php echo $User->Get('token','no token'); ?>', {
									success: (resp) => {
											Loger.Log('info','Logout Response', resp);
											let table = {
													"token_not_match": "Token is not match",
													"data_missing": "Looks like some datas are missing...",
													"logout_successfully": "Bye bye, Expect you come back soon QQ",
											}
											let isSuccess = Loger.Check(resp,'success');
											let swal_config = isSuccess ? { timer:4000, } : {};
											Loger.Swal(resp, table, swal_config).then((val)=>{
													if(isSuccess){ window.location.replace('<?php echo ROOT; ?>'); }
											});
									},
									error: (resp) => {
											Loger.Log('error','Logout Unexpected Errors', resp);
											Swal.fire('Sorry, we got the some expected errors...', 'Error', 'error');
									},
								});
							} else if (result.isDenied) {
								Swal.fire('Thank you for keep :)', '', 'info');
							}
						}) // end swal()
					} // end logout()
				},
				mounted(){
					// auto run
					this.resize();

					// listener
					window.addEventListener('resize',()=>{this.resize()});

						try{
							// change the account item to be login style
							if(this.account.isLogin){
									$('#Menu .ui.dropdown#account').dropdown();
									this.account.name = '<?php echo htmlentities($User->Get('nickname',$User->Get('username',"Error"))); ?>';
							}
							// active
							let id = '<?php echo ID('page'); ?>'; // current page id
							let current = Object.keys(this.$data).find(item => {
									if(!this[item].id){ return; }
									else if(!Array.isArray(this[item].id)){ this[item].id = [this[item].id]; }
									return this[item].id.includes(id);
							});
							// if current page is found in items of menu
							if(current){
									current = this[current];
									current.isActive = 'active';
									current.link = "#";
							}
						}catch(e){}
				}
		}).mount('#Menu');

		$('#languages.dropdown').dropdown({
			onChange: function(value, text, $selectedItem){
				Swalc.loading('Please Wait', 'Changing language to '+value.trim()+' ...').fire();
				setCookie('lang',value); window.location.reload();
			}
		});

</script>
<!-- 
<div class="item" data-percent="0" data-value="en-us" data-english="English">
	<span class="description">English</span>
	English
</div>
<div class="item active selected" data-value="male">Male</div> -->


<div class="ui hidden divider"></div>


<?php @include_once(Inc('loading')); ?>

<?php

