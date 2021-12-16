<?php defined('INIT') or die('NO INIT'); ?>

<?php 
    @include_once(Func('user'));
    $User->Update();
?>
<script type="text/javascript" src="<?php echo JS('user'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>


<div id="Menu">
	<!-- Sidebar -->
	<div class="ui sidebar inverted vertical menu" id="Sidebar">
	    <a class="item" :class="about.isActive" :href="about.link">{{ about.name }}</a>
	    <a class="item" :class="announcement.isActive" :href="announcement.link">{{ announcement.name }}</a>
	    <a class="item" :class="index.isActive" :href="index.link">{{ index.name }}</a>
	    <a class="item" :class="forum.isActive" :href="forum.link">{{ forum.name }}</a>
	</div>
	<!-- Pusher -->
	<div class="pusher"></div>
	<!-- End Sidebar -->


	<!-- Navbar -->
	<div class="ui inverted segment attached" id="Navbar">
	    <div class="ui inverted secondary pointing animated menu">
	        <div class="ui animated black button" onclick="$('.ui.sidebar').sidebar('toggle');" tabindex="0" v-if="sidebar.display">
	            <div class="visible content">
	                <i class="bars icon"></i>
	            </div>
	            <div class="hidden content">
	                <i class="right arrow icon"></i>
	            </div>
	        </div>

	        <template v-if="navbar.display">
	            <a class="item" :class="about.isActive" :href="about.link">{{ about.name }}</a>
	            <a class="item" :class="announcement.isActive" :href="announcement.link">{{ announcement.name }}</a>
	            <a class="item" :class="index.isActive" :href="index.link">{{ index.name }}</a>
	            <a class="item" :class="forum.isActive" :href="forum.link">{{ forum.name }}</a>
	        </template>

	        <div class="right menu" id="account">
	            <!-- Not Login -->
	            <a class="ui item" :class="account.isActive" :href="account.link" v-if="!account.isLogin">{{ account.name }}</a>
	            <!-- Is Login -->
	            <div class="ui item dropdown link" id="account" v-if="account.isLogin">
	                {{ account.name }}<i class="dropdown icon"></i>
	                <div class="menu">
	                    <a class="item" :class="profile.isActive" :href="profile.link"><i class="edit icon"></i> {{ profile.name }}</a>
	                    <a class="item" @click="logout"><i class="sign out alternate icon"></i> Logout</a>
	                </div>
	            </div>
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
                name: "Home",
                isActive: false,
                link: `<?php echo Root('index'); ?>`,
            },
            forum: {
                id: '<?php echo ID('page',Root('forum')); ?>',
                name: "Forum",
                isActive: false,
                link: `<?php echo Root('forum'); ?>`,
            },
            about: {
                id: '<?php echo ID('page',Root('about')); ?>',
                name: "About",
                isActive: false,
                link: `<?php echo Root('about'); ?>`,
            },
            announcement: {
                id: '<?php echo ID('page',Root('announcement')); ?>',
                name: "Announcement",
                isActive: false,
                link: `<?php echo Root('announcement'); ?>`,
            },
            account: {
                id: '<?php echo ID('page',Page('account/index')); ?>',
                name: "Account",
                isActive: false,
                link: `<?php echo Page('account/index'); ?>`,
                isLogin: <?php echo $User->Is('Login')?"true":"false"; ?>,
            },
            profile: {
                id: '<?php echo ID('page', Page('account/profile')); ?>',
                name: 'Profile',
                isActive: false,
                link: '<?php echo Page('account/profile'); ?>',
            }
        }},
        methods:{
        	resize: function(){
				this.clientWidth = document.body.clientWidth;
                this.clientHeight = document.body.clientHeight;
                // console.log(`${Menu.clientWidth}x${Menu.clientHeight}`);
                if(this.clientWidth < 500){ this.navbar.display = false; }
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
                }).then((result) => {
                    if (result.isConfirmed) {
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
                    this.account.name = "<?php echo htmlentities($User->Get('username',"Error")); ?>";
                }
                // active
                let id = '<?php echo ID('page'); ?>'; // current page id
                let current = Object.keys(this.$data).find(item => {
                    if(!Array.isArray(this[item].id)){ this[item].id = [this[item].id]; }
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


</script>


<div class="ui hidden divider"></div>


<?php

