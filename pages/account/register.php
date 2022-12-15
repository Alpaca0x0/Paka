<?php
Inc::clas('user');
!User::isLogin() or die(header('Location: '.Root));
Inc::clas('captcha');
Inc::component('header');
Inc::component('navbar');
$config = Inc::config('account');
?>

<div class="ts-content is-tertiary is-vertically-padded">
    <!-- <div class="ts-space"></div> -->
    <div class="ts-container is-very-narrow">
        <div class="ts-header is-big is-heavy">Register</div>
        <div class="ts-text is-secondary">已有帳號？馬上<a href="<?=Uri::page('account/login')?>">登入</a>吧！</div>
    </div>
    <!-- <div class="ts-space"></div> -->
</div>

<div class="ts-divider"></div>

<div class="ts-space is-big"></div>

<div id="Register" class="ts-container is-very-narrow">
    <div class="ts-text is-label">使用者帳號</div>
    <div class="ts-space"></div>
    <div class="ts-input is-negative is-underlined is-fluid">
        <input type="text">
    </div>
    <div class="ts-space is-small"></div>
    <div class="ts-text is-description"><?=htmlentities($config['description']['username'])?></div>

    <div class="ts-space is-large"></div>
    <div class="ts-text is-label">電子郵件地址</div>
    <div class="ts-space"></div>
    <div class="ts-input is-underlined is-fluid">
        <input type="text">
    </div>

    <div class="ts-space is-large"></div>
    <div class="ts-text is-label">密碼</div>
    <div class="ts-space"></div>
    <div class="ts-input is-underlined is-fluid">
        <input type="password">
    </div>
    <div class="ts-space is-small"></div>
    <div class="ts-text is-description"><?=htmlentities($config['description']['password'])?></div>
    
    <div class="ts-space is-large"></div>
    <div class="ts-text is-label">驗證碼</div>
    <img :src="fields.captcha.src" @click="fields.captcha.change()" style="cursor: pointer;">
    <div :class="classObject('captcha')" @input="checkDatas()" class="ts-input is-start-icon is-underlined is-fluid">
        <span class="ts-icon is-robot-icon"></span>
        <input type="text" ref="refCaptcha" v-model="fields.captcha.value" :readonly="is.submitting" maxlength="6">
    </div>


    <div class="ts-space is-large"></div>
    <button class="ts-button is-fluid">下一步</button>
    <div class="ts-space is-small"></div>
    <div class="ts-text is-center-aligned is-description">按下「下一步」表示您也接受「伊繁星最高協議」、「個人隱私政策」、「使用者規範」。</div>
</div>

<script type="module">
    import { createApp, reactive, ref } from '<?=Uri::js('vue')?>';
    import * as directives from '<?=Uri::js('vue/directives')?>';

    const Register = createApp({setup(){
        let is = reactive({
            submit: false,
        });
        // 
        let fields = reactive({
            username: {
                status: 'warning', 
                value: '<?=(DEV)?'alpaca0x0':''?>',
                regex: [<?=$config['username']?>,<?=$config['email']?>],
            },
            password: {
                status: 'warning',
                value: '<?=(DEV)?'passw0rd':''?>',
                regex: <?=$config['password']?>,
            },
            captcha: {
                status: 'warning',
                value: '',
                regex: <?=$config['captcha']?>,
                src: '<?=Captcha::src()?>?' + Math.random(),
                change: ()=>{
                    (async ()=>{
                        fields.captcha.src='<?=Captcha::src()?>?' + Math.random();
                        await nextTick();
                        <?php if(DEV){ ?>
                            // auto type captcha when in DEV mode
                            fields.captcha.value = Dev.getCaptcha(); 
                        <?php } ?>
                    })();
                },
            }
        });
        // 
        const classObject = (key) => {
            let objects = {
                username: {
                    'is-negative': fields.username.status==='warning',
                    'is-disabled': is.submitting,
                },
                password: {
                    'is-negative': fields.password.status==='warning',
                    'is-disabled': is.submitting,
                },
                captcha: {
                    'is-negative': fields.captcha.status==='warning',
                    'is-disabled': is.submitting,
                },
                info: {
                    'is-negative': info.type==='warning' || info.type==='error',
                    'is-positive': info.type==='success',
                }
            };
            return objects[key];
        }
        //  
        const Dev = {
            getCaptcha: () => {
                var captcha = '';
                $.ajax({
                    type: "GET",
                    url: '<?=Uri::api('captcha')?>',
                    dataType: 'json',
                    async : false,
                    success: (resp) => { captcha = resp; }
                }); return captcha;
            }
        };
        // 
        let info = reactive({
            type: null,
            title: null,
            msg: null,
        });
        // 
        return { is, fields, classObject };
    }}).directive('focus',
        directives.focus
    ).mount('#Register');
</script>


<?php
Inc::component('footer');