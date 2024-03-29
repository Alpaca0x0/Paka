<?php
Inc::clas('user');
?>

<div id="Navbar">
    <div class="ts-box is-squared is-very-elevated">
        <div class="ts-row">
            <div class="column is-fluid">
                <div class="ts-tab is-small">
                    <!-- left items -->
                    <template v-for="(item, name) in items">
                        <a v-if="!item.isHidden" :href="item.isActive?'#!':item.link" :class="[item.isActive || name===onMouseItem?'is-active':'', item.isDisabled ? 'is-disabled':'']" @mouseover="onMouseItem=name" @mouseleave="onMouseItem=false" class="item">
                            <div class="ts-icon is-small tablet-:u-hidden" :class="[item.icon?'is-'+item.icon+'-icon':'']"></div>
                            <div class="label" v-text="item.text"></div>
                        </a>
                    </template>
                    <!--  -->
                </div>
            </div>
            <div class="column">
                <div class="ts-tab is-small">
                    <!-- right items -->
                    <template v-for="(item, name) in ritems">
                        <a v-if="!item.isHidden" :href="item.isActive?'#!':item.link" :class="[item.isActive || name===onMouseItem?'is-active':'', item.isDisabled ? 'is-disabled':'']" @mouseover="onMouseItem=name" @mouseleave="onMouseItem=false" class="item">
                            <div class="ts-icon" :class="[item.icon?'is-'+item.icon+'-icon':'']"></div>
                            <div class="label" v-text="item.text"></div>
                        </a>
                    </template>
                    <!-- if login -->
                    <template v-if="is.login">
                        <a class="item is-end-icon" data-dropdown="profile-dropdown" @mouseover="onMouseItem='account';" @mouseleave="onMouseItem=false;" :class="[ritems.account.isActive || 'account'===onMouseItem?'is-active':'', ritems.account.isDisabled ? 'is-disabled':'']">
                            <div class="label">
                                <span class="ts-avatar is-small is-circular">
                                    <img src="<?=User::get('avatar', false) ? 'data:image/jpeg;base64,'.User::get('avatar','') : Uri::img('user.png')?>">
                                </span>
                                <?=User::get('username')?>
                            </div>
                            <span class="ts-icon is-chevron-down-icon"></span>
                        </a>
                        <div class="ts-dropdown" data-name="profile-dropdown" data-position="bottom-end">
                            <!-- sub items -->
                            <template v-for="(item, nmae) in subItems.account">
                                <a v-if="!item.isHidden" :href="item.isActive?'#!':item.link" :class="{'is-selected':item.isActive, 'is-disabled':item.isDisabled}" class="item">
                                    {{ item.text }}
                                    <span class="ts-icon" :class="[item.icon?'is-'+item.icon+'-icon':'']">
                                </a>
                            </template>
                            <!-- /sub item -->
                            <!-- bottom items -->
                            <div class="ts-divider"></div>
                            <!-- logout -->
                            <a @click="logout()" class="item">
                                登出 <span class="ts-icon is-right-from-bracket-icon"></span>
                            </a>
                        </div>
                    </template>
                    <!--  -->
                </div>
            </div>
        </div>    
    </div>
</div>

<!-- <div class="ts-divider"></div> -->
<div class="ts-space is-big"></div>

<script type="module">
    import { createApp, ref, reactive, onMounted } from '<?=Uri::js('vue')?>';
    import '<?=Uri::js('ajax')?>';
    import * as Resp from '<?=Uri::js('resp')?>';
    import { clickOutside } from '<?=Uri::js('vue/directives')?>';
    const Directives = { clickOutside };

    const Navbar = createApp({setup(){
        let is = reactive({
            login: <?=User::isLogin() ? 'true' : 'false'?>,
        });
        // 
        let items = reactive({ 
            'forum': {
                'id': '<?=ID(Router::tryFile(Path::page.'forum'))?>',
                'text': '首頁',
                'link': '<?=Uri::page('forum')?>',
                'icon': 'mug-hot',
            },
            // 
            'rule': {
                'id': '<?=ID(Router::tryFile(Path::page.'rules'))?>',
                'text': '社群規章',
                'link': '<?=Uri::page('rules')?>',
                'icon': 'info',
                'isDisabled': false,
                'isHidden': false,
            },
            'about': {
                'id': '<?=ID(Router::tryFile(Path::page.'about'))?>',
                'text': '關於',
                'link': '<?=Uri::page('about')?>',
                'icon': 'question',
            },
        });
        let ritems = reactive({
            'account': {
                'id': [
                    '<?=ID(Router::tryFile(Path::page.'account/index.php'))?>',
                    '<?=ID(Router::tryFile(Path::page.'account/login.php'))?>',
                    '<?=ID(Router::tryFile(Path::page.'account/register.php'))?>',
                    '<?=ID(Router::tryFile(Path::page.'account/profile.php'))?>',
                ],
                'text': '帳號',
                'link': '<?=User::isLogin()?Uri::page('account/profile.php'):Uri::page('account/')?>',
                'icon': 'user',
                'isHidden': is.login,
            },
        });
        let subItems = reactive({
            'account':{
                'profile': {
                    'id': '<?=ID(Router::tryFile(Path::page.'account/profile.php'))?>',
                    'text': '個人資訊',
                    'link': '<?=Uri::page('account/profile')?>',
                    'icon': 'user',
                }
            }
        });
        //
        let currentPageId = '<?=ID?>';
        let onMouseItem = ref(false);
        // 
        const logout = () => {
            Swal.fire({
                title: '確定要登出？',
                text: '登出將無法使用許多功能！',
                icon: 'warning',
                showDenyButton: true,
                confirmButtonText: '確定',
                denyButtonText: '再留一下子好了',
                focusDeny: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // confirm logout
                    // show waitting msg
                    Swal.fire({
                        title: '請稍後',
                        text: '正在登出...',
                        timerProgressBar: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                    });
                    // request logout
                    $.ajax({
                        type: 'GET',
                        url: '<?=Uri::auth('account/logout'); ?>',
                        data: { token: '<?=User::get('token','')?>', },
                        dataType: 'json',
                    }).fail((resp) => {
                        console.error(resp);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Sorry, we got the some unexpected errors...',
                        });
                    }).done((resp) => {
                        console.log(resp);
                        // check response format is correct
                        if(!Resp.object(resp)){ return false; }
                        // 
                        let isSuccess = resp.type==='success';
                        if(isSuccess){ window.location.replace('<?=Uri::page('account/login')?>'); }
                        else{
                            Swal.fire({
                                icon: resp.type,
                                title: resp.type[0].toUpperCase() + resp.type.slice(1),
                                text: resp.message,
                            });
                        }
                    });
                } else if (result.isDenied) { }
            }) // end swal()
        };
        //
        onMounted(()=>{
            // active navbar item
            for(let i of [items, ritems]){
                for(let [key, val] of Object.entries(i)) {
                    if(!Array.isArray(val.id)){ val.id = [val.id]; }
                    if(val.id.includes(currentPageId)){ val.isActive = true; }
                }
            }
            // active subitem
            for(let [name, item] of Object.entries(subItems)) {
                for(let [key, val] of Object.entries(item)) {
                    if(!Array.isArray(val.id)){ val.id = [val.id]; }
                    if(val.id.includes(currentPageId)){ val.isActive = true; }
                }
            }
        })
        //
        return { is, items, ritems, subItems, currentPageId, onMouseItem, logout }
    }}).directive("clickOutside",
        Directives.clickOutside
    ).mount('#Navbar');
</script>

<style>
    #Navbar {
        position: fixed;
        top: 0px;
        width: 100vw;
        z-index: 99;
    }
    #Navbar .item {
        cursor: pointer;
    }
</style>
