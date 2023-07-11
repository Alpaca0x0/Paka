<?php
Inc::clas('user');
User::isLogin() or die(header('Location:'.Uri::page('account/login')));
Inc::component('header');
Inc::component('navbar');
$config = Inc::config('account');
?>

<style>
    .cropper-view-box {
        border-radius: 50%;
    }
    .cropper-face {
        background-color:inherit !important;
    }
</style>

<div id="Profile" class="cell is-fluid is-secondary">
    <div class="ts-space is-large"></div>
    <div class="ts-container is-narrow">
        
        <!-- Profile -->
        <div class="ts-header is-large is-heavy">帳戶資料</div>
        <div class="ts-text is-secondary">關於您帳戶的基本資訊。</div>
        <div class="ts-space is-large"></div>
        <!--  -->

        <!-- avatar view modal -->
        <div :class="{'is-visible': fields.avatar.is.cropping}" class="ts-modal is-big" v-cloak>
            <div class="content">
                <!-- close button -->
                <div class="ts-content is-dense">
                    <div class="ts-row">
                        <div class="column is-fluid">
                            <!-- <div class="ts-header">裁剪您的頭貼</div> -->
                        </div>
                        <div class="column">
                            <button @click="fields.avatar.is.cropping=false" type="button" class="ts-close"></button>
                        </div>
                    </div>
                </div>
                <!-- /close button -->
                <div class="ts-container">
                    <div class="ts-divider is-start-text">編輯工具</div>
                    <!-- <div class="ts-box is-horizontal is-hollowed"> -->
                    <div class="ts-row">
                        <div class="column is-fluid">
                            <div class="ts-content">
                                <!-- <div class="ts-header">
                                    裁切您的大頭貼
                                </div> -->
                                <!-- tool buttons -->
                                <div class="ts-wrap">
                                    <button type="button" class="ts-button is-icon" @click="fields.avatar.object.rotate(-5)">
                                        <span class="ts-icon is-arrow-rotate-left-icon"></span>
                                    </button>
                                    <button type="button" class="ts-button is-icon">
                                        <span class="ts-icon is-arrow-rotate-right-icon"></span>
                                    </button>
                                </div>
                                <!-- /tool buttons --> 
                            </div>
                        </div>
                        <div class="column">
                            <div class="ts-image is-1-by-1 is-covered">
                                <div data-ref-id="avatarPreview" :ref="setRef" style="overflow: hidden; width: 200px; height: 200px; border-radius: 50%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="ts-divider is-start-text">裁減圖片</div>
                    <div class="ts-content">
                        <div class="ts-image is-small is-centered" style="height: 480px;">
                            <img :src="fields.avatar.view" data-ref-id="avatarView" :ref="setRef">
                            <div class="ts-space is-small"></div>
                        </div>
                    </div>

                    <div class="ts-divider is-end-text">
                        <button type="button" class="ts-button is-end-icon" @click="fields.avatar.cropped()">
                            完成
                            <span class="ts-icon is-check-icon"></span>
                        </button>
                    </div>
                </div>
                <br>
            </div>
        </div>
        <!-- /avatar view modal -->

        <form @submit.prevent="submit()">
            <div class="ts-grid is-stackable">
                <!-- avatar -->
                <div class="column is-4-wide">
                    <div class="ts-center">
                        <label for="avatarFile">
                            <div class="ts-image is-circular is-small is-centered is-covered">
                                <img :src="fields.avatar.value" data-ref-id="avatar" :ref="setRef">
                            </div>
                            <input id="avatarFile" data-ref-id="avatarFile" :ref="setRef" type="file" accept="image/*" style="display: none;">
                        </label>
                    </div>
                </div>
                <!-- /avatar -->

                <!-- profile fields -->
                <div class="column is-12-wide">
                    <div class="ts-grid is-stackable">
                        <!--  -->
                        <div class="column is-8-wide">
                            <div class="ts-text is-label">Username</div>
                            <div class="ts-space is-small"></div>
                            <div :class="classObjects('username')" class="ts-input is-start-labeled">
                                <span class="label ts-text is-disabled" v-text="'#'+user.id" v-once></span>
                                <input class="ts-segment is-tertiary" v-model="user.username" v-once readonly>
                            </div>
                        </div>
                        <!--  -->
                        <div class="column is-8-wide">
                            <div class="ts-text is-label">E-Mail</div>
                            <div class="ts-space is-small"></div>
                            <div :class="classObjects('email')" class="ts-input">
                                <input @input="checkDatas()" type="email" class="ts-segment is-tertiary" v-model="fields.email.value" data-ref-id="email" :ref="setRef" readonly>
                            </div>
                        </div>
                        <div class="column is-8-wide">
                            <div class="ts-text is-label">Nickname</div>
                            <div class="ts-space is-small"></div>
                            <div :class="classObjects('nickname')" class="ts-input">
                                <input @input="checkDatas()" :readonly="is.submitting" type="text" v-model="fields.nickname.value" data-ref-id="nickname" :ref="setRef">
                            </div>
                        </div>
                        <div class="column is-8-wide">
                            <div class="ts-text is-label">Birthday</div>
                            <div class="ts-space is-small"></div>
                            <div :class="classObjects('birthday')" class="ts-input">
                                <input @input="checkDatas()" :readonly="is.submitting" :min="fields.birthday.range[0]" :max="fields.birthday.range[1]" type="date" v-model="fields.birthday.value" data-ref-id="birthday" :ref="setRef">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /profile fields -->
            </div>

            <!--  -->
            <div class="ts-space is-small"></div>
            <!--  -->
            <div class="ts-row">
                <div class="column is-end-aligned is-fluid">
                    <button type="button" @click="reset()" :class="classObjects('reset')" :disabled="is.submitting" class="ts-button is-icon is-dense is-small is-secondary">
                        <span class="ts-icon is-rotate-left-icon"></span>
                    </button>
                </div>
                <div class="column is-end-aligned">
                    <button type="submit" :class="classObjects('submit')" :disabled="is.submitting" data-ref-id="submit" :ref="setRef" class="ts-button is-end-icon is-dense is-small">
                        保存設定
                        <span class="ts-icon is-check-icon"></span>
                    </button>
                </div>
            </div>
        </form>
        <!--  -->

        <div class="ts-space is-small"></div>
        <div class="ts-divider is-section"></div>
        <div class="ts-space is-small"></div>

        <!-- Events -->
        <div class="ts-grid is-relaxed">
            <div class="column is-16-wide">
                <div class="ts-box is-top-indicated" style="overflow-x:auto;">
                    <div class="ts-content is-dense">
                        <div class="ts-header is-heavy">事件記錄簿 (僅模擬，功能尚未完成)</div>
                    </div>
                    <div class="ts-divider"></div>
                    <table class="ts-table is-basic">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>行為</th>
                                <th>日期</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>192.168.1.122</td>
                                <td>登入</td>
                                <td>2022/12/09</td>
                            </tr>
                            <tr class="is-disabled">
                                <td>192.168.1.146</td>
                                <td>登入失敗</td>
                                <td>2022/11/03</td>
                            </tr>
                            <tr class="is-disabled">
                                <td>192.168.1.123</td>
                                <td>登入失敗</td>
                                <td>2022/10/15</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="ts-divider"></div>
                    <div class="ts-content">
                        <div class="ts-pagination">
                            <a class="item is-back"></a>
                            <a class="item is-active">1</a>
                            <a class="item">2</a>
                            <a class="item">3</a>
                            <a class="item">4</a>
                            <a class="item is-next"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ts-space is-small"></div>
        <div class="ts-divider is-section"></div>
        <div class="ts-space is-small"></div>

        <!-- 統計資訊 -->
        <div class="ts-header is-large is-heavy">統計 (僅模擬，功能尚未完成)</div>
        <div class="ts-text is-secondary">帳戶創立至今的數據。</div>
        <div class="ts-space is-large"></div>
        <div class="ts-grid is-2-columns is-stackable">
            <div class="column">
                <div class="ts-box">
                    <div class="ts-content">
                        <div class="ts-statistic">
                            <div class="value">8,652</div>
                            <div class="comparison is-increased">351</div>
                        </div>
                        總會員數
                    </div>
                    <div class="symbol">
                        <span class="ts-icon is-users-icon"></span>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ts-box">
                    <div class="ts-content">
                        <div class="ts-statistic">
                            <div class="value">3</div>
                            <div class="comparison is-decreased">14</div>
                        </div>
                        平均在線分鐘數
                    </div>
                    <div class="symbol">
                        <span class="ts-icon is-clock-icon"></span>
                    </div>
                </div>
            </div>
        </div>
        <!--  -->
    </div>
</div>

<style> @import '<?=Uri::css('cropper')?>'; </style>

<script type="module">
    import { createApp, ref, reactive, onMounted, nextTick, } from '<?=Uri::js('vue')?>';
    import { focus } from '<?=Uri::js('vue/directives')?>';
    const Directives = { focus };
    import '<?=Uri::js('ajax')?>';
    import * as Resp from '<?=Uri::js('resp')?>';
    import Cropper from '<?=Uri::js('cropper')?>';

    const Profile = createApp({setup(){
        let user = reactive({
            id: <?=User::get('id', 0)?>,
            avatarDefault: '<?=Uri::img('user.png')?>',
            avatar: <?=User::get('avatar', null) !== null ? "'".'data:image/jpeg;base64,'.User::get('avatar')."'" : 'null'?>,
            username: '<?=User::get('username', '-')?>',
            email: '<?=User::get('email', '')?>',
            nickname: '<?=User::get('nickname', '')?>',
            birthday: '<?=User::get('birthday', '')?>',
        });
        // 
        let refs = reactive({});
        let setRef = (el) => { refs[el.getAttribute('data-ref-id')] = el; }
        // 
        let is = reactive({
            submitting: false,
        });
        // 
        let fields = reactive({
            email: {
                status: 'success',
                value: user.email,
                regex: <?=$config['email']?>,
            },
            nickname: {
                status: 'success', 
                value: user.nickname,
                regex: <?=$config['nickname']?>,
            },
            birthday: {
                status: 'success',
                value: user.birthday,
                range: [null, null]
            },
            avatar: {
                is: {
                    cropping: false,
                },
                value: user.avatar || user.avatarDefault,
                view: user.avatar || user.avatarDefault,
                object: null,
                cropped: undefined,
                image: undefined,
                blob: null,
            },
        });
        fields.avatar.cropped = () => {
            let image = fields.avatar.image;
            fields.avatar.object.getCroppedCanvas({
                width: 320,
                height: 320,
            }).toBlob(function(blob){
                fields.avatar.value = URL.createObjectURL(blob);
                let file = new File([blob], image.name,{type:image.type, lastModified:new Date().getTime()});
                let container = new DataTransfer();
                container.items.add(file);
                refs.avatarFile.files = container.files;
                fields.avatar.blob = blob;
            }, image.type,0.8);
            fields.avatar.is.cropping = false;
        }
        // set birthday range
        {let today = new Date();
        let dd = String(today.getDate()).padStart(2, '0');
        let mm = String(today.getMonth() + 1).padStart(2, '0');
        fields.birthday.range = [
            String(today.getFullYear() - <?=$config['birthday'][1]?>) + `-${mm}-${dd}`,
            String(today.getFullYear() - <?=$config['birthday'][0]?> + `-${mm}-${dd}`),
        ];}
        // 
        const classObjects = (key) => {
            let objects = {
                username: {
                    'is-disabled': is.submitting,
                },
                email: {
                    'is-negative': fields.email.status==='warning',
                    'is-disabled': is.submitting,
                },
                nickname: {
                    'is-negative': fields.nickname.status==='warning',
                    'is-disabled': is.submitting,
                },
                birthday: {
                    'is-negative': fields.birthday.status==='warning',
                    'is-disabled': is.submitting,
                },
                submit: {
                    'is-loading': is.submitting,
                    'is-disabled': is.submitting,
                },
                reset: {
                    'is-disabled': is.submitting,
                },
            };
            return objects[key];
        }
        // 
        const checkDatas = () => {
            let isPass = true;
            fields.nickname.value = fields.nickname.value.replace(/\s+/g, ' ').trim(' '); // remove all space in nickname
            Object.values(fields).forEach((field) => {
                if(typeof(field.value) === undefined || typeof(field.regex) === undefined || field.value === null){ return; }
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
        };
        // 
        const update = () => {
            user.email = fields.email.value;
            user.nickname = fields.nickname.value;
            user.birthday = fields.birthday.value;
        }
        // 
        const submit = () => {
            if(is.submitting || !checkDatas()){ return; }
            is.submitting = true;
            // make no element be focused
            refs.submit.focus();
            refs.submit.blur();
            // 
            let datas = {
                email: fields.email.value,
                nickname: fields.nickname.value,
                birthday: fields.birthday.value,
                avatar: refs.avatarFile.files,
            };
            datas = new FormData();
            datas.append('email', fields.email.value);
            datas.append('nickname', fields.nickname.value);
            datas.append('birthday', fields.birthday.value);
            datas.append('avatar', fields.avatar.blob);
            // 
            let info = {
                type: 'error',
                msg: 'Unexpected error!',
            };
            // 
            $.ajax({
                type: 'POST',
                url: '<?=Uri::auth('account/profile')?>',
                data: datas,
                dataType: 'json',
                contentType: false,
                processData: false,
            }).fail((xhr, status, error) => {
                console.error(xhr.responseText);
            }).done((resp) => {
                console.log(resp);
                if(!Resp.object(resp)){ return false; }
                // 
                info.type = resp.type;
                info.msg = resp.message;
                // 
                if(resp.type === 'success'){
                    for(const [key, val] of Object.entries(resp.data)){
                        fields[key]['value'] = val === null ? '' : val;
                    }update();
                }else{
                    if(['nickname_format'].includes(resp.status)){
                        fields.nickname.status = 'warning';
                        refs.nickname.focus();
                    }else if(['birthday_format','too_young','too_old'].includes(resp.status)){
                        fields.birthday.status = 'warning';
                        refs.birthday.focus();
                    }
                }
            }).always(() => {
                Swal.fire({
                    position: 'bottom-start',
                    icon: info.type,
                    title: info.msg,
                    toast: true,
                    showConfirmButton: false,
                    timer: info.type==='success' ? 2000 : false,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                is.submitting = false;
            });
        };
        // 
        const reset = () => {
            fields.email.value = user.email;
            fields.nickname.value = user.nickname;
            fields.birthday.value = user.birthday;
            checkDatas();
        }
        // 
        onMounted(() => {
            fields.avatar.object = new Cropper(refs.avatarView);
            refs.avatarFile.onchange = () => {
                fields.avatar.image = refs.avatarFile.files[0];
                if(!fields.avatar.image){ return; }
                (async ()=>{
                    fields.avatar.object.destroy();
                    fields.avatar.view = URL.createObjectURL(fields.avatar.image);
                    await nextTick();
                    fields.avatar.object = new Cropper(refs.avatarView, {
                        viewMode: 2,
                        aspectRatio: 1 / 1,
                        initialAspectRatio: 1 / 1,
                        preview: refs.avatarPreview,
                    });
                    fields.avatar.is.cropping = true;
                    fields.avatar.object.crop();
                })();
            };
        });
        // 
        return {
            user, refs, setRef, checkDatas, submit, fields, classObjects, is, reset,
        }
    }}).directive('focus',
        Directives.focus
    ).mount('#Profile');
</script>





<?php
Inc::component('footer');
