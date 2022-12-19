<?php
Inc::component('header');
Inc::component('navbar');
Inc::clas('user');
?>

<div id="Forum" class="ts-app-layout is-full is-vertical">
    <div class="cell is-secondary is-fluid">
        <div class="ts-space is-large"></div>
        <div class="ts-container">
            <div class="ts-grid is-relaxed">
                <div class="column is-3-wide">
                    <div style="position: sticky; top: 4rem">
                        <div class="ts-divider is-section"></div>
                        <div class="ts-wrap is-middle-aligned">
                            <div class="ts-avatar is-circular">
                                <img :src="user.avatar || user.avatarDefault">
                            </div>
                            <div class="ts-text is-heavy" v-text="user.username"></div>
                        </div>
                        <div class="ts-divider is-section"></div>
                        <div class="ts-menu is-start-icon is-separated">
                            <a href="#!" class="item"> <span class="ts-icon is-user-group-icon"></span> 朋友 </a>
                            <a href="#!" class="item"> <span class="ts-icon is-bookmark-icon"></span> 收藏內容 </a>
                            <a href="#!" class="item"> <span class="ts-icon is-store-icon"></span> 市集</a>
                            <a href="#!" class="item"> <span class="ts-icon is-flag-icon"></span> 粉絲專頁</a>
                            <a href="#!" class="item"> <span class="ts-icon is-check-to-slot-icon"></span> 投票</a>
                            <div class="ts-divider"></div>
                            <a class="item"> <span class="ts-icon is-gear-icon"></span> 設定 </a>
                        </div>
                    </div>
                </div>
                <!-- main -->
                <div class="column is-9-wide">
                    <!-- write post -->
                    <div class="ts-segment">
                        <div class="ts-row">
                            <div class="column">
                                <div class="ts-avatar is-large is-circular">
                                    <img :src="user.avatar || user.avatarDefault">
                                </div>
                            </div>
                            <div class="column is-fluid">
                                <div class="ts-input is-fluid">
                                    <textarea rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="ts-divider is-section"></div>
                        <div class="ts-row">
                            <div class="column is-fluid">
                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                    <span class="ts-icon is-image-icon"></span>
                                    照片 / 多媒體
                                </button>
                            </div>
                            <div class="column is-fluid">
                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                    <span class="ts-icon is-users-icon"></span>
                                    標記好友
                                </button>
                            </div>
                            <div class="column is-fluid">
                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                    <span class="ts-icon is-face-smile-icon"></span>
                                    感受 / 活動
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="ts-space"></div>
                    <!-- write post end -->

                    <!-- posts -->
                    <div id="Posts" :ref="setRef" v-for="post in posts.data" v-cloak>
                        <div v-if="post.isRemoved" class="ts-segment is-tertiary">
                            這裡曾有過一篇文章，但已不復存在。
                        </div>
                        <template v-else>
                            <div class="ts-segment" v-click-away="()=>postActions.menuActiver=false">
                                <div class="ts-row">
                                    <div class="column">
                                        <div class="ts-avatar is-large is-circular">
                                            <img :src="post.poster.avatar ? 'data:image/jpeg;base64,'+post.poster.avatar : user.avatarDefault">
                                        </div>
                                    </div>
                                    <div class="column is-fluid">
                                        <div style="line-height: 1.5">
                                            <div class="ts-text is-heavy">
                                                {{ post.poster.nickname ? post.poster.nickname+' ('+post.poster.username+')' : post.poster.username }}
                                            </div>
                                            <div class="ts-meta is-small is-secondary">
                                                <div class="item">
                                                    <div class="ts-icon is-earth-asia-icon is-end-spaced"></div>
                                                    公開
                                                </div>
                                                <a href="#!" class="item">3 分鐘前</a>
                                                <?php if(DEV){ ?>
                                                    <div class="item">#{{post.id}}</div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="ts-space is-small"></div>
                                        {{ post.content }}
                                        <div class="ts-space is-small"></div>
                                    </div>
                                    <!-- post actions -->
                                    <div v-if="user.id===post.poster.id" class="column">
                                        <div>
                                            <button @click="postActions.menuActiver=post.id" class="ts-button is-secondary is-icon">
                                                <span class="ts-icon is-ellipsis-icon"></span>
                                            </button>
                                            <div :class="{ 'is-visible': postActions.menuActiver===post.id }" class="ts-dropdown is-small is-dense is-separated is-bottom-right">
                                                <button class="item" @click="postActions.edit(post)">編輯</button>
                                                <div class="ts-divider"></div>
                                                <button class="item" @click="postActions.delete(post)">刪除</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- post actions end -->
                                </div>
                                <div class="ts-divider is-section"></div>
                                <div class="ts-row">
                                    <div class="column is-fluid">
                                        <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                            <span class="ts-icon is-thumbs-up-icon is-regular"></span>
                                            讚
                                        </button>
                                    </div>
                                    <div class="column is-fluid">
                                        <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                            <span class="ts-icon is-comment-icon is-regular"></span>
                                            留言
                                        </button>
                                    </div>
                                    <div class="column is-fluid">
                                        <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                            <span class="ts-icon is-share-from-square-icon is-regular"></span>
                                            分享
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="ts-space"></div>
                    </div>
                    <!-- posts end -->

                    <!-- if no auto load -->
                    <button v-show="!posts.is.getting && !posts.is.noMore && !posts.is.getError" @click="getPosts()" class="ts-button is-fluid is-start-icon is-circular" v-cloak>
                        <span class="ts-icon is-hand-pointer-icon"></span>
                        若文章無法自動加載，可以按下按鈕手動加載唷！
                    </button>
                    <!-- if no auto load end -->

                    <!-- posts loading -->
                    <div v-show="posts.is.getting" class="ts-placeholder is-loading" v-cloak>
                        <div class="ts-segment">
                            <div class="ts-row">
                                <div class="column">
                                    <div class="ts-avatar is-large is-circular">
                                        <div class="image is-header"></div>
                                    </div>
                                </div>
                                <div class="column is-fluid">
                                    <div class="text is-header"></div>
                                    <div class="text"></div>
                                    <div class="text"></div>
                                </div>
                            </div>
                        </div>
                        <div class="ts-space"></div>
                    </div>
                    <!-- posts loading end -->

                    <!-- no more post -->
                    <div v-show="posts.is.noMore" class="ts-segment is-very-elevated is-dense is-center-aligned" v-cloak>
                        到底部了，已經沒有更多文章囉！
                    </div>
                    <!-- no more post end -->

                    <!-- get posts error -->
                    <div v-show="posts.is.getError" class="ts-segment is-negative is-top-indicated is-very-elevated is-dense is-center-aligned" v-cloak>
                        很抱歉，在獲取文章時發生錯誤！({{ posts.type }}: {{ posts.status }})
                    </div>
                    <!-- get posts error end -->

                    <!-- <div class="ts-segment">
                        <div class="ts-row">
                            <div class="column">
                                <div class="ts-avatar is-large is-circular">
                                    <img src="<?=Uri::img('user.png')?>">
                                </div>
                            </div>
                            <div class="column is-fluid">
                                <div style="line-height: 1.5">
                                    <div class="ts-text is-heavy">Yami Odymel</div>
                                    <div class="ts-meta is-small is-secondary">
                                        <div class="item">
                                            <div class="ts-icon is-earth-asia-icon is-end-spaced"></div>
                                            公開
                                        </div>
                                        <a href="#!" class="item">3 分鐘前</a>
                                    </div>
                                </div>
                                <div class="ts-space is-small"></div>
                                Ken Wong 沒事推什麼 King Exit 的坑，<br>
                                要是我 TeaMeow 今年寫不出來，<br>
                                就是你給我的精神攻擊害的
                                <div class="ts-space is-small"></div>
                                <div class="ts-image is-rounded">
                                    <img src="<?=Uri::img('template/16-9.png')?>">
                                </div>
                            </div>
                        </div>
                        <div class="ts-divider is-section"></div>
                        <div class="ts-row">
                            <div class="column is-fluid">
                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                    <span class="ts-icon is-thumbs-up-icon is-regular"></span>
                                    讚
                                </button>
                            </div>
                            <div class="column is-fluid">
                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                    <span class="ts-icon is-comment-icon is-regular"></span>
                                    留言
                                </button>
                            </div>
                            <div class="column is-fluid">
                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                    <span class="ts-icon is-share-from-square-icon is-regular"></span>
                                    分享
                                </button>
                            </div>
                        </div>
                    </div> -->

                </div>
                <div class="column is-4-wide">
                    <div style="position: sticky; top: 4rem">
                        <!-- <div class="ts-segment">
                            <div class="ts-header is-heavy">限時動態</div>
                            <div class="ts-space"></div>
                            <div class="ts-wrap">
                                <div class="ts-avatar is-large is-circular">
                                    <img src="<?=Uri::img('user.png')?>">
                                </div>
                                <div class="ts-avatar is-large is-circular">
                                    <img src="<?=Uri::img('user.png')?>">
                                </div>
                                <div class="ts-avatar is-large is-circular">
                                    <img src="<?=Uri::img('user.png')?>">
                                </div>
                            </div>
                        </div>
                        <div class="ts-space"></div> -->
                        <div class="ts-segment">
                            <div class="ts-header is-heavy">最近看過的商品</div>
                            <div class="ts-space"></div>
                            <div class="ts-grid is-2-columns">
                                <div class="column">
                                    <div class="ts-image is-rounded" style="max-width: 450px">
                                        <img src="<?=Uri::img('template/4-3.png')?>">
                                        <div class="ts-mask is-secondary is-bottom">
                                            <div class="ts-content is-compact is-start-aligned">
                                                <div class="ts-badge is-secondary">NT$ 2,500</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="ts-image is-rounded" style="max-width: 450px">
                                        <img src="<?=Uri::img('template/4-3.png')?>">
                                        <div class="ts-mask is-secondary is-bottom">
                                            <div class="ts-content is-compact is-start-aligned">
                                                <div class="ts-badge is-secondary">NT$ 5,500</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="ts-image is-rounded" style="max-width: 450px">
                                        <img src="<?=Uri::img('template/4-3.png')?>">
                                        <div class="ts-mask is-secondary is-bottom">
                                            <div class="ts-content is-compact is-start-aligned">
                                                <div class="ts-badge is-secondary">NT$ 100</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="ts-image is-rounded" style="max-width: 450px">
                                        <img src="<?=Uri::img('template/4-3.png')?>">
                                        <div class="ts-mask is-secondary is-bottom">
                                            <div class="ts-content is-compact is-start-aligned">
                                                <div class="ts-badge is-secondary">NT$ 320</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ts-space"></div>
                        <div class="ts-segment">
                            <div class="ts-header is-heavy">宣傳廣告</div>
                            <div class="ts-space"></div>
                            <div class="ts-image is-rounded">
                                <img src="<?=Uri::img('template/16-9.png')?>">
                            </div>
                            <div class="ts-space"></div>
                            <div class="ts-text is-bold">神奇麵包屋</div>
                            <div class="ts-text is-description">這會是你史上吃過最好吃的麵包，比米奇妙妙屋還要神奇！</div>
                        </div>
                        <div class="ts-space is-small"></div>
                        <div class="ts-meta is-small is-secondary is-center-aligned">
                            <a href="#!" class="item">服務條款</a>
                            <a href="#!" class="item">隱私政策</a>
                            <a href="#!" class="item">協助工具</a>
                            <a href="#!" class="item">廣告資訊</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ts-space is-large"></div>
    </div>
</div>

<script type="module">
    import { createApp, reactive, onMounted } from '<?=Uri::js('vue')?>';
    import '<?=Uri::js('ajax')?>';
    import * as Resp from '<?=Uri::js('resp')?>';
    import * as diravtives from '<?=Uri::js('vue/directives/click-away')?>';
    // 
    const Forum = createApp({setup(){
        let refs = reactive({});
        let setRef = (el) => { refs[el.id] = el; }
        // 
        let user = reactive({
            id: <?=User::get('id','false')?>,
            username: '<?=User::get('username','Guest')?>',
            avatarDefault: '<?=Uri::img('user.png')?>',
            avatar: <?=User::get('avatar', null) !== null ? "'".'data:image/jpeg;base64,'.User::get('avatar')."'" : 'null'?>,
        });
        let posts = reactive({
            type: 'error',
            status: 'Unexpected',
            data: [],
            message: '發生非預期的錯誤',
            is: {
                getting: false,
                noMore: false,
                getError: false,
            }
        });
        // 
        let postActions = reactive({
            menuActiver: false, // pid
            edit: (post) => {},
            delete: (post) => {
                if(post.isRemoving){ return; }
                post.isDeleting = true;
                // 
                let msg = {
                    icon: 'error',
                    title: '非預期錯誤',
                    text: '很抱歉，發生了非預期的錯誤！',
                };
                // 
                Swal.fire({
                    icon: 'warning',
                    title: '你確定嗎？',
                    text: "即便刪除，文章內容依舊會存放於伺服器一段時間(可能幾個月)，且每個人都應該為自己的言行舉止負責。",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: '是，刪除！',
                    cancelButtonText: '取消',
                    focusCancel: true,
                }).then((result) => {
                    if(!result.isConfirmed){ return; }
                    $.ajax({
                        type: "POST",
                        url: '<?=Uri::auth('forum/post/delete')?>',
                        data: { pid: post.id },
                        dataType: 'json',
                    }).fail((xhr, status, error) => {
                        console.error(xhr.responseText);
                    }).done((resp) => {
                        console.log(resp);
                        if(!Resp.object(resp)){ return false; }
                        // 
                        if(resp.type === 'success'){
                            msg.icon = 'success';
                            msg.title = '成功刪除';
                            msg.text = '文章已經被刪除囉！';
                            post.isRemoved = true;
                        }else{
                            msg.icon = resp.type;
                            msg.title = resp.type[0].toUpperCase() + resp.type.slice(1);
                            msg.text = resp.message;
                        }
                    }).always(() => {
                        post.isDeleting = false;
                        Swal.fire(msg);
                    });
                });
            }
        });
        // 
        const getPosts = () => {
            if(posts.is.getting || posts.is.noMore || posts.is.getError){ return; }
            posts.is.getting = true;
            let datas = { limit: 12 };
            if(posts.data.length > 0){ datas.before = posts.data.slice(-1)[0].id ; }
            // 
            $.ajax({
                type: "GET",
                url: '<?=Uri::api('forum/posts')?>',
                data: datas,
                dataType: 'json',
            }).always(()=>{
                posts.type = 'error';
                posts.status = 'unexpected';
                posts.message = '發生非預期的錯誤';
            }).fail((xhr, status, error) => {
                console.error(xhr.responseText);
            }).done((resp) => {
                try {
                    console.log(resp);
                    // check response format is correct
                    if(!Resp.object(resp)){ return false; }
                    // get msg
                    posts.type = resp.type;
                    posts.status = resp.status;
                    posts.message = resp.message;
                    // check if success
                    if(resp.type==='success'){
                        if(resp.data === null || resp.data.length < 1){ posts.is.noMore = true; }
                        else{ posts.data.push(...resp.data); }
                    }else{ posts.is.getError = true; }
                } catch (error) { console.error(error); }
            }).always(() => {
                posts.is.getting = false;
            });
        }
        // 
        onMounted(() => {
            getPosts();
            // 
            document.documentElement.onwheel = (event) => {
                let scrollTop = document.documentElement.scrollTop;
                let clientHeight = document.documentElement.clientHeight;
                let scrollHeight = document.documentElement.scrollHeight;
                if(scrollTop+clientHeight > (scrollHeight-scrollHeight/5)){ getPosts(); }
            }
            // refs.Posts.onwheel = () => { refs.Posts.onscroll(); }
        });
        // 
        return { user, posts, setRef, postActions, getPosts };
    }}).directive("clickAway",
        diravtives.clickAway
    ).mount('#Forum');
</script>

<?php
Inc::component('footer');