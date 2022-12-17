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
                    <div id="Posts" :ref="setRef" v-for="post in posts">
                        <template v-if="!post.isRemoved">
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
                                        {{ post.title }}
                                        <br>
                                        {{ post.content }}
                                        <div class="ts-space is-small"></div>
                                    </div>
                                    <!-- functions -->
                                    <div class="column">
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
                                    <!-- functions end -->
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
                            <div class="ts-space"></div>
                        </template>
                    </div>
                    <!-- posts end -->

                    <div class="ts-segment">
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
                    </div>
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
            username: '<?=User::get('username','Guest')?>',
            avatarDefault: '<?=Uri::img('user.png')?>',
            avatar: <?=User::get('avatar', null) !== null ? "'".'data:image/jpeg;base64,'.User::get('avatar')."'" : 'null'?>,
        });
        let posts = reactive([]);
        // 
        let postActions = reactive({
            menuActiver: false, // pid
            edit: (post) => {},
            delete: (post) => {
                post.isRemoved = true;
            }
        });
        // 
        let is = reactive({
            // posts
            gettingPosts: false,
            noMorePosts: false,
            // 
        });
        let info = reactive({
            type: null,
            title: null,
            msg: null,
        });
        // 
        const getPosts = () => {
            if(is.gettingPosts || is.noMorePosts){ return; }
            is.gettingPosts = true;
            let datas = { limit: 12 };
            if(posts.length > 0){ datas.before = posts.slice(-1)[0].id ; }
            // 
            $.ajax({
                type: "GET",
                url: '<?=Uri::api('forum/posts')?>',
                data: datas,
                dataType: 'json',
            }).always(()=>{
                info.type = 'error';
                info.title = 'Error';
                info.msg = 'Unexpected Error';
            }).fail((xhr, status, error) => {
                console.error(xhr.responseText);
            }).done((resp) => {
                try {
                    console.log(resp);
                    // check response format is correct
                    if(!Resp.object(resp)){ return false; }
                    // get msg
                    info.type = resp.type;
                    info.title = resp.type[0].toUpperCase() + resp.type.slice(1);
                    info.msg = resp.message;
                    // if warning
                    // check if success
                    if(resp.type==='success'){
                        if(resp.data === null || resp.data.length < 1){ is.noMorePosts = true; }
                        else{ posts.push(...resp.data); }
                    }
                } catch (error) { console.error(error); }
            }).always(() => {
                is.gettingPosts = false;
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
        return { user, posts, setRef, postActions };
    }}).directive("clickAway",
        diravtives.clickAway
    ).mount('#Forum');
</script>

<?php
Inc::component('footer');