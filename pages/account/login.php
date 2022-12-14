<?php
Inc::clas('user');
!User::isLogin() or die(header('Location:'.Root));
Inc::clas('captcha');
Inc::sub('header');
Inc::sub('navbar');
$config = Inc::conf('account');
?>

<div class="ts-content is-tertiary is-vertically-padded">
    <!-- <div class="ts-space"></div> -->
    <div class="ts-container is-very-narrow">
        <div class="ts-header is-big is-heavy">Login</div>
        <div class="ts-text is-secondary">還沒有帳號？<a href="<?=htmlentities(Uri::page('account/register'))?>">註冊</a>一個吧！</div>
    </div>
    <!-- <div class="ts-space"></div> -->
</div>

<div class="ts-divider"></div>

<div class="ts-space is-big"></div>

<div id="Login" class="ts-container">
    <div class="ts-center">
        <div class="ts-header is-large is-heavy is-icon">
            <div class="ts-icon is-face-smile-icon"></div>
            Paka
        </div>
        <div class="ts-space is-large"></div>

        <form @submit.prevent="submit();">
            <!-- info board -->
            <div v-show="info.type" class="ts-segment is-dense"  style="width: 320px">
                <div class="ts-checklist">
                    <div class="item" :class="classObjects('info')" v-text="info.msg"></div>
                </div>
            </div>
            <!--  -->
            <div class="ts-segment" style="width: 320px">
                <div class="ts-wrap is-vertical">

                    <div class="ts-text is-label">使用者帳號 / E-Mail</div>
                    <div :class="classObjects('username')" @input="checkDatas()" class="ts-input is-start-icon is-underlined is-fluid">
                        <span class="ts-icon is-user-icon"></span>
                        <input type="text" ref="refUsername" v-model="fields.username.value" :readonly="is.submitting" v-focus>
                    </div>
                    

                    <div class="ts-text is-label">密碼</div>
                    <div :class="classObjects('password')" @input="checkDatas()" class="ts-input is-start-icon is-underlined is-fluid">
                        <span class="ts-icon is-lock-icon"></span>
                        <input type="password" ref="refPassword" v-model="fields.password.value" :readonly="is.submitting">
                    </div>
                    <!-- <div class="ts-meta is-end-aligned ts-text is-tiny is-description">
                        <a class="item" href="#!">忘記密碼?</a>
                    </div> -->
                    <div v-show="is.need.verified" class="ts-text is-label">驗證碼</div>
                    <img v-if="is.need.verified" :src="fields.captcha.src" @click="fields.captcha.change()" style="cursor: pointer;">
                    <div v-show="is.need.verified" :class="classObjects('captcha')" @input="checkDatas()" class="ts-input is-start-icon is-underlined is-fluid">
                        <span class="ts-icon is-robot-icon"></span>
                        <input type="text" ref="refCaptcha" v-model="fields.captcha.value" :readonly="is.submitting" maxlength="6">
                    </div>

                    <button type="submit" ref="refSubmit" :class="{'is-loading': is.submitting}" :disabled="is.submitting" class="ts-button is-fluid">登入</button>
                    

                </div>
            </div>
        </form>
        
    </div>
</div>
<!-- <div class="ts-text is-center-aligned is-description">message</div> -->

<script type="module">
    import { createApp, reactive, ref, onMounted, nextTick, } from '<?=Uri::js('vue')?>';
    import * as directives from '<?=Uri::js('vue/directives')?>';
    import '<?=Uri::js('ajax')?>';
    import * as Resp from '<?=Uri::js('resp')?>';
    // 
    const Login = createApp({setup(){
        const refUsername = ref();
        const refPassword = ref();
        const refCaptcha = ref();
        const refSubmit = ref();
        // 
        let is = reactive({
            submit: false,
            login: <?=User::isLogin() ? 'true' : 'false'?>,
            need: {
                'verified': false
            }
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
                optional: true,
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
        const classObjects = (key) => {
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
        const checkDatas = () => {
            let isPass = true;
            Object.values(fields).forEach((field) => {
                if(typeof(field.value) === undefined || typeof(field.regex) === undefined){ return; }
                if(!Array.isArray(field.regex)){ field.regex = [field.regex]; }
                let isMatch = (field.regex).some((regex)=>(field.value).match(regex));
                if(!isMatch && !field.optional){
                    isPass = false;
                    if(typeof(field.status) !== undefined){ field.status = 'warning'; }
                }else{
                    if(typeof(field.status) !== undefined){ field.status = 'success'; }
                }
            });
            return isPass;
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
        const submit = () => {
            if(is.submitting || !checkDatas()){ return false; }
            is.submitting = true;
            refSubmit.value.focus();
            // info
            info.type = null;
            info.title = 'Info';
            info.msg = 'Submitting... Please wait...';
            // datas
            let datas = {
                username: fields.username.value,
                password: fields.password.value,
            };
            // if need captcha
            if(is.need.verified){ datas.captcha = fields.captcha.value; }
            // 
            $.ajax({
                type: "POST",
                url: '<?=Uri::auth('account/login')?>',
                data: datas,
                dataType: 'json',
            }).always(()=>{
                info.type = 'error';
                info.title = 'Error';
                info.msg = 'Unexpected Error';
            }).fail((xhr, status, error) => {
                console.error(xhr.responseText);
            }).done((resp) => { // set msg
                try {
                    console.log(resp);
                    // check response format is correct
                    if(!Resp.object(resp)){ return false; }
                    // get msg
                    info.type = resp.type;
                    info.title = resp.type[0].toUpperCase() + resp.type.slice(1);
                    info.msg = resp.message;
                    // check if success
                    is.login = resp.type==='success';
                    if(is.login){ window.location.replace('<?=Root?>'); }
                } catch (error) { console.error(error); }
            }).always((resp)=>{ // update value and status of dom
                if(is.login){ return; }
                // check response format is correct
                if(!Resp.object(resp)){ return false; }
                // check if need captcha
                if(['needs_captcha', 'captcha_not_match'].includes(resp.status)
                    || (['password_not_match'].includes(resp.status) && resp.data < 1) ){
                    fields.captcha.change();
                    fields.captcha.value = '';
                    // show dom first
                    (async ()=>{
                        is.need.verified = true;
                        await nextTick();
                        refCaptcha.value.focus();
                    })();
                }else{ is.need.verified = false; }
                fields.captcha.optional = !is.need.verified;
                // other fields status
                if(['password_not_match'].includes(resp.status)){
                    (async ()=>{
                        fields.password.value = '';
                        await nextTick();
                        refPassword.value.focus();
                    })();
                }
                // update fields status
                checkDatas();
                // other fields status, dont update status
                if(['not_found_user'].includes(resp.status)){
                    fields.username.status = 'warning';
                    fields.username.value = '';
                    refUsername.value.focus();
                }
                // submitting off
                is.submitting = false;
            });
        };
        // 
        onMounted(() => {
            if(is.login){ window.location.replace('<?=Root?>'); }
            checkDatas();
        });
        // 
        return {
            fields, submit, checkDatas, is, classObjects, Dev, info,
            refUsername, refPassword, refCaptcha, refSubmit
        }
    }}).directive('focus',
        directives.focus,
    ).mount('#Login');

</script>

<?php
Inc::sub('footer');