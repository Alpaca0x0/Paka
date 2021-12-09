<?php defined('INIT') or die('NO INIT'); ?>

<!-- Navbar -->

<div class="ui inverted segment attached" id="Navbar" ref="Navbar">
    <div class="ui inverted secondary pointing animated menu">
        <div class="ui animated black button" onclick="$('.ui.sidebar').sidebar('toggle');" tabindex="0">
            <div class="visible content">
                <i class="bars icon"></i>
            </div>
            <div class="hidden content">
                <i class="right arrow icon"></i>
            </div>
        </div>
        <a class="item" :class="index.isActive" :href="index.link">{{ index.name }}</a>
        <a class="item" :class="about.isActive" :href="about.link">{{ about.name }}</a>
        <a class="item" :class="announcement.isActive" :href="announcement.link">{{ announcement.name }}</a>
        <div class="right menu">
            <a class="ui item" :class="account.isActive" :href="account.link">{{ account.name }}</a>
        </div>
    </div>
</div>

<script type="module">
    import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';

    // Navbar
    createApp({
        data(){return{
            index: {
                id: '<?php echo ID('page',Root('index')); ?>',
                name: "Home",
                isActive: false,
                link: `<?php echo Root('index'); ?>`,
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
                id: '<?php echo ID('page',page('account/index')); ?>',
                name: "Account",
                isActive: false,
                link: `<?php echo page('account/index'); ?>`,
            },
        }},
        mounted(){
            try{
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
    }).mount('div#Navbar');
</script>

<div class="ui hidden divider"></div>

<!-- End Navbar -->

<?php

