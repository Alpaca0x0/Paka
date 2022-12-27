<?php
Inc::component('header');
Inc::component('navbar');
Inc::clas('user');
?>

<style>@import url('<?=Uri::css('animate')?>');</style>
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

                        <!-- create post -->
                        <div class="ts-row">
                            <div class="column">
                                <div class="ts-avatar is-large is-circular">
                                    <img :src="user.avatar || user.avatarDefault">
                                </div>
                            </div>
                            <div class="column is-fluid">
                                <div :class="{'is-disabled': post.is.creating}" class="ts-input is-fluid">
                                    <textarea :readonly="post.is.creating" v-model="post.creating.content" rows="4" placeholder="今天想說點什麼？"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- create post end -->

                        <!-- submit button -->
                        <div class="ts-space is-small"></div>
                        <div class="ts-row">
                            <div class="column is-fluid">
                                <!-- <div class="ts-input is-fluid">
                                    <input type="text" class="input" placeholder="搜尋文章…" />
                                </div> -->
                            </div>
                            <div class="column">
                                <button @click="post.create()" :class="{'is-disabled': post.is.creating}" class="ts-button is-start-labeled-icon is-outlined" v-cloak>
                                    <span v-show="!post.is.creating" class="ts-icon is-paper-plane-icon"></span>
                                    <div v-show="post.is.creating" class="ts-icon">
                                        <div class="ts-loading is-small"></div>
                                    </div>
                                    {{ post.is.creating ? '正在發文...' : '發文' }}
                                </button>
                            </div>
                        </div>
                        <!-- submit button end -->
                        
                        <!-- <div class="ts-divider is-section"></div>
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
                        </div> -->
                    </div>
                    <div class="ts-space"></div>
                    <!-- write post end -->

                    <!-- posts -->
                    <transition-group enter-active-class="animate__fast animate__fadeIn">
                        <div v-for="thePost in posts.data" :key="thePost" class="animate__animated" v-cloak>
                            <!-- when post is removed -->
                            <transition enter-active-class="animate__slow animate__flipInX">
                                <div v-show="thePost.is.removed" class="ts-segment is-tertiary animate__animated">
                                    這裡曾有過一篇文章，但已不復存在。
                                </div>
                            </transition>
                            <!-- when post is removed end -->
                            <!-- post -->
                            <transition leave-active-class="animate__hinge">
                                <div v-show="!thePost.is.removed" :id="'Post-'+thePost.id" class="animate__animated" :style="{'animation-duration': '900ms'}" v-cloak>
                                    <div class="ts-segment is-very-elevated">
                                        <div class="ts-row">
                                            <div class="column">
                                                <div class="ts-avatar is-large is-circular">
                                                    <img :src="thePost.poster.avatar ? 'data:image/jpeg;base64,'+thePost.poster.avatar : user.avatarDefault">
                                                </div>
                                            </div>
                                            <div class="column is-fluid">
                                                <div style="line-height: 1.5">
                                                    <div class="ts-text is-heavy">
                                                        {{ thePost.poster.nickname ? thePost.poster.nickname+' ('+thePost.poster.username+')' : thePost.poster.username }}
                                                    </div>
                                                    <div class="ts-meta is-small is-secondary">
                                                        <div class="item">
                                                            <div class="ts-icon is-earth-asia-icon"></div>
                                                            public
                                                        </div>
                                                        <a href="#!" class="item" :title="moment(thePost.datetime*1000).format('YYYY/MM/DD hh:mm')">
                                                            <div class="ts-icon is-clock-icon"></div>
                                                            {{ moment(thePost.datetime*1000).fromNow() }}
                                                        </a>
                                                        <div class="item">
                                                            <div class="ts-icon is-hashtag-icon"></div>
                                                            {{ thePost.id }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ts-space is-small"></div>

                                                <div v-html="thePost.content" :style="{'max-height': thePost.is.viewAllContent ? '' : '8.6rem' }" style="overflow: hidden; overflow-wrap: break-word; white-space: pre-line;"></div>
                                                <a v-show="thePost.content.split(/\r\n|\r|\n/).length > 4" @click="thePost.is.viewAllContent=!thePost.is.viewAllContent" href="#!" class="item ts-text is-tiny is-link">{{ thePost.is.viewAllContent ? '顯示較少' : '…顯示更多' }}</a>

                                            </div>
                                            <!-- post actions -->
                                            <div v-show="user.id===thePost.poster.id" class="column">
                                                <div>
                                                    <button @click="post.preActionPost=thePost.id" v-click-away="()=>post.preActionPost=false" class="ts-button is-secondary is-icon is-small">
                                                        <span class="ts-icon is-ellipsis-icon"></span>
                                                    </button>
                                                    <div :class="{ 'is-visible': post.preActionPost===thePost.id }" class="ts-dropdown is-small is-dense is-separated is-bottom-right">
                                                        <button class="item" @click="post.edit(thePost)">編輯</button>
                                                        <div class="ts-divider"></div>
                                                        <button class="item" @click="post.delete(thePost)">刪除</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- post actions end -->
                                        </div>
                                        <!-- post interactive -->
                                        <div class="ts-divider is-section"></div>
                                        <div class="ts-row">
                                            <div class="column is-fluid">
                                                <button class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                                    <span class="ts-icon is-thumbs-up-icon is-regular"></span>
                                                    讚
                                                </button>
                                            </div>
                                            <div class="column is-fluid">
                                                <button @click="thePost.comments.is.visible=!thePost.comments.is.visible; thePost.comments.is.init || getComments(thePost)" class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                                    <span class="ts-icon is-comment-icon is-regular"></span>
                                                    留言
                                                    <span v-show="thePost.comments.times" class="ts-badge is-outlined is-start-spaced">{{ thePost.comments.times }}</span>
                                                </button>
                                            </div>
                                            <div class="column is-fluid">
                                                <button @click="post.detail(thePost)" class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                                    <span class="ts-icon is-share-from-square-icon is-regular"></span>
                                                    Detail
                                                </button>
                                            </div>
                                        </div>
                                        <!-- post interactive end -->

                                        <!-- comments -->
                                        <!-- comments loading -->
                                        <div v-show="thePost.comments.is.getting" v-show="thePost.comments.is.visible" class="ts-placeholder is-loading" v-cloak>
                                            <div class="ts-space"></div>
                                            <div class="ts-row">
                                                <div class="column">
                                                    <div class="ts-avatar is-large">
                                                        <div class="image is-header"></div>
                                                    </div>
                                                </div>
                                                <div class="column is-fluid">
                                                    <div class="text is-header"></div>
                                                    <div class="text"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- comments loading end -->

                                        <template v-if="thePost.comments.times > 0 && thePost.comments.is.visible && !thePost.comments.is.getting" v-cloak>
                                            <div class="ts-space"></div>
                                            <div class="ts-divider is-start-text">
                                                <a v-show="!thePost.comments.is.noMore" @click="getComments(thePost)" href="#!" class="item ts-text is-tiny is-link">載入更多關於這則貼文的 {{thePost.comments.times - thePost.comments.data.length }} 則留言</a>
                                            </div>
                                            <div v-show="!thePost.comments.is.noMore && thePost.comments.is.getError" class="ts-divider is-start-text">
                                                <span class="ts-text is-tiny is-negative">載入關於這則貼文的留言時發生錯誤</span>
                                            </div>
                                        </template>

                                        <transition-group enter-active-class="animate__faster animate__fadeIn" leave-active-class="animate__fadeOut">
                                            <div v-for="theComment in thePost.comments.data" :key="theComment" v-show="thePost.comments.is.visible" class="animate__animated" :style="{'animation-duration': '250ms'}" v-cloak>

                                                <div class="ts-space"></div>

                                                <!-- when comment is removed -->
                                                <transition enter-active-class="animate__slow animate__flipInX">
                                                    <div v-show="theComment.is.removed" class="ts-segment is-tertiary is-dense animate__animated">
                                                        這裡曾有過一則留言，但已隨風而去。
                                                    </div>
                                                </transition>
                                                <!-- when comment is removed end -->

                                                <!-- comment -->
                                                <transition leave-active-class="animate__hinge">
                                                    <div v-show="!theComment.is.removed" class="ts-row" :style="{'animation-duration': '900ms'}">
                                                        <div class="column is-fluid">
                                                            <div class="ts-conversation">
                                                                <div class="avatar ts-image">
                                                                    <img :src="theComment.commenter.avatar?('data:image/jpeg;base64,'+theComment.commenter.avatar):user.avatarDefault">
                                                                </div>
                                                                <div class="content" style="min-width: 99%; max-width: 99%">
                                                                    <div class="bubble">
                                                                        <div class="author">
                                                                            <a class="ts-text is-undecorated">{{ theComment.commenter.username }}</a>
                                                                        </div>
                                                                        <div v-html="theComment.content" :style="{'max-height': theComment.is.viewAllContent ? '' : '4.3rem' }" style="overflow: hidden; overflow-wrap: break-word; white-space: pre-line;"></div>
                                                                        <a v-show="theComment.content.split(/\r\n|\r|\n/).length > 4" @click="theComment.is.viewAllContent=!theComment.is.viewAllContent" href="#!" class="item ts-text is-tiny is-link">{{ theComment.is.viewAllContent ? '顯示較少' : '…顯示更多' }}</a>
                                                                    </div>
                                                                    <div class="ts-meta is-small is-secondary">
                                                                        <a href="#!" class="item">讚</a>
                                                                        <a @click="thePost.comment.preReplyComment=(thePost.comment.preReplyComment===theComment.id) ? false : theComment.id" href="#!" class="item">回覆</a>
                                                                        <a href="#!" class="item" :title="moment(theComment.datetime*1000).format('YYYY/MM/DD hh:mm')">
                                                                            {{ moment(theComment.datetime*1000).fromNow() }}
                                                                        </a>
                                                                        <div class="item">
                                                                            <div class="ts-icon is-hashtag-icon"></div>
                                                                            {{ theComment.id }}
                                                                        </div>
                                                                    </div>

                                                                    <!-- replies -->
                                                                    <template v-if="theComment.replies.times > 0">
                                                                        <div class="ts-divider is-start-text">
                                                                            <a v-show="!theComment.replies.is.noMore" @click="getReplies(theComment)" href="#!" class="item ts-text is-tiny is-link">載入更多關於這則留言的 {{theComment.replies.times - theComment.replies.data.length }} 則回應</a>
                                                                        </div>
                                                                        <div v-show="!theComment.replies.is.noMore && theComment.replies.is.getError" class="ts-divider is-start-text">
                                                                            <span class="ts-text is-tiny is-negative">載入關於這則留言的回應時發生錯誤</span>
                                                                        </div>
                                                                        <transition-group enter-active-class="animate__faster animate__fadeIn" leave-active-class="animate__fadeOut">
                                                                            <div v-for="theReply in theComment.replies.data" :key="theReply" class="animate__animated" :style="{'animation-duration': '250ms'}" v-cloak>
                                                                                <!-- reply -->
                                                                                <div class="ts-space"></div>
                                                                                <div class="ts-conversation">
                                                                                    <div class="avatar ts-image">
                                                                                        <img :src="theReply.replier.avatar?('data:image/jpeg;base64,'+theReply.replier.avatar):user.avatarDefault">
                                                                                    </div>
                                                                                    <div class="content" style="width: 100%;">
                                                                                        <div class="bubble">
                                                                                            <div class="author">
                                                                                                <a class="ts-text is-undecorated">{{ theReply.replier.username }}</a>
                                                                                            </div>
                                                                                            <div v-html="theReply.content" :style="{'max-height': theReply.is.viewAllContent ? '' : '4.3rem' }" style="overflow: hidden; overflow-wrap: break-word; white-space: pre-line;"></div>
                                                                                            <a v-show="theReply.content.split(/\r\n|\r|\n/).length > 4" @click="theReply.is.viewAllContent=!theReply.is.viewAllContent" href="#!" class="item ts-text is-tiny is-link">{{ theReply.is.viewAllContent ? '顯示較少' : '…顯示更多' }}</a>
                                                                                        </div>
                                                                                        <div class="ts-meta is-small is-secondary">
                                                                                            <a class="item">讚</a>
                                                                                            <a class="item">回覆</a>
                                                                                            <a href="#!" class="item" :title="moment(theReply.datetime*1000).format('YYYY/MM/DD hh:mm')">
                                                                                                {{ moment(theReply.datetime*1000).fromNow() }}
                                                                                            </a>
                                                                                            <div class="item">
                                                                                                <div class="ts-icon is-hashtag-icon"></div>
                                                                                                {{ theReply.id }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- reply end -->
                                                                            </div>
                                                                        </transition-group>
                                                                    </template>
                                                                    <!-- replies end -->

                                                                    <transition enter-active-class="animate__slow animate__flipInX" leave-active-class="animate__fadeOut">
                                                                        <div v-show="thePost.comment.preReplyComment===theComment.id" class="animate__animated" :style="{'animation-duration': thePost.comment.preReplyComment===theComment.id ? '500ms' : '250ms'}">
                                                                            <!-- create reply -->
                                                                            <div class="ts-divider is-section"></div>
                                                                            <div class="ts-conversation">
                                                                                <div class="avatar">
                                                                                    <img :src="user.avatar || user.avatarDefault">
                                                                                </div>
                                                                                <div class="content" style="width: 100%;">
                                                                                    <div class="ts-input is-fluid is-underlined is-small">
                                                                                        <textarea 
                                                                                            :readonly="theComment.reply.is.creating"
                                                                                            @keydown.enter.exact.prevent="reply.create(theComment)" 
                                                                                            @keydown.enter.shift.exact.prevent="theComment.reply.creating.content += '\n'" 
                                                                                            v-model="theComment.reply.creating.content" 
                                                                                            placeholder="回覆這則留言... (可以使用 Shift + Enter 換行)" 
                                                                                            style="height: 2.5rem"
                                                                                            oninput="this.style.height='1px'; this.style.height=this.scrollHeight+4+'px';" 
                                                                                            onkeydown="this.oninput()" 
                                                                                            onfocus="this.oninput()" 
                                                                                            onblur="this.style.height='2.5rem'"
                                                                                        ></textarea>
                                                                                        <!-- reply creating load -->
                                                                                        <div v-show="theComment.reply.is.creating" class="ts-mask is-blurring">
                                                                                            <div class="ts-center">
                                                                                                <div class="ts-content" style="color: #FFF">
                                                                                                    <div class="ts-loading is-small"></div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- reply creating load end -->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- create reply end -->
                                                                        </div>
                                                                    </transition>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- comment actions -->
                                                        <div v-show="user.id===theComment.commenter.id" class="column">
                                                            <div>
                                                                <button @click="thePost.comment.preActionComment=theComment.id" v-click-away="()=>thePost.comment.preActionComment=false" class="ts-button is-secondary is-icon is-small">
                                                                    <span class="ts-icon is-ellipsis-icon"></span>
                                                                </button>
                                                                <div :class="{ 'is-visible': thePost.comment.preActionComment===theComment.id }" class="ts-dropdown is-small is-dense is-separated is-bottom-right">
                                                                    <button class="item" @click="comment.edit(theComment)">編輯</button>
                                                                    <div class="ts-divider"></div>
                                                                    <button class="item" @click="comment.delete(theComment)">刪除</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- comment actions end -->

                                                    </div>
                                                </transition>
                                                <!-- comment end -->
                                            </div>
                                        </transition-group>

                                        <transition enter-active-class="animate__slow animate__flipInX" leave-active-class="animate__fadeOut">
                                            <div v-show="thePost.comments.is.visible" class="animate__animated" :style="{'animation-duration': thePost.comments.is.visible ? '500ms' : '250ms'}">

                                                <!-- create comment -->
                                                <div class="ts-divider is-section"></div>
                                                <div class="ts-conversation">
                                                    <div class="avatar">
                                                        <img :src="user.avatar || user.avatarDefault">
                                                    </div>
                                                    <div class="content" style="width: 100%;">
                                                        <div class="ts-input is-fluid is-underlined is-small">
                                                            <textarea 
                                                                :readonly="thePost.comment.is.creating"
                                                                @keydown.enter.exact.prevent="comment.create(thePost)" 
                                                                @keydown.enter.shift.exact.prevent="thePost.comment.creating.content += '\n'" 
                                                                v-model="thePost.comment.creating.content" 
                                                                placeholder="回覆這則貼文... (可以使用 Shift + Enter 換行)" 
                                                                style="height: 2.5rem"
                                                                oninput="this.style.height='1px'; this.style.height=this.scrollHeight+4+'px';" 
                                                                onkeydown="this.oninput()" 
                                                                onfocus="this.oninput()" 
                                                                onblur="this.style.height='2.5rem'"
                                                            ></textarea>
                                                            <!-- comment creating load -->
                                                            <div v-show="thePost.comment.is.creating" class="ts-mask is-blurring">
                                                                <div class="ts-center">
                                                                    <div class="ts-content" style="color: #FFF">
                                                                        <div class="ts-loading is-small"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- comment creating load end -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- create comment end -->
                                                
                                            </div>
                                        </transition>
                                        
                                        <!-- comments end -->

                                    </div>

                                    <!-- post deleting load -->
                                    <div v-show="thePost.is.deleting" class="ts-mask is-blurring">
                                        <div class="ts-center">
                                            <div class="ts-content" style="color: #FFF">
                                                <div class="ts-loading is-large"></div>
                                                <br>刪除中
                                            </div>
                                        </div>
                                    </div>
                                    <!-- post deleting load end -->

                                </div>
                            </transition>
                            <!-- post -->

                            <div class="ts-space"></div>
                        </div>
                    </transition-group>
                    <!-- posts end -->

                    <!-- if no auto load -->
                    <transition enter-active-class="animate__animated animate__flipInX">
                        <button v-show="!posts.is.getting && !posts.is.noMore && !posts.is.getError" @click="getPosts()" class="ts-button is-fluid is-start-icon is-circular" v-cloak>
                            <span class="ts-icon is-hand-pointer-icon"></span>
                            若文章無法自動加載，可以按下按鈕手動加載唷！
                        </button>
                    </transition>
                    <!-- if no auto load end -->

                    <!-- posts loading -->
                    <div v-show="posts.is.getting" class="ts-placeholder is-loading" v-cloak>
                        <div class="ts-segment">
                            <div class="ts-row">
                                <div class="column">
                                    <div class="ts-avatar is-large">
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
                    <transition enter-active-class="animate__animated animate__flipInX">
                        <div v-show="posts.is.noMore" class="ts-segment is-very-elevated is-dense is-center-aligned" v-cloak>
                            到底部了，已經沒有更多文章囉！
                        </div>
                    </transition>
                    <!-- no more post end -->

                    <!-- get posts error -->
                    <transition enter-active-class="animate__animated animate__flipInX">
                        <div v-show="posts.is.getError" class="ts-segment is-negative is-top-indicated is-very-elevated is-dense is-center-aligned" v-cloak>
                            很抱歉，在獲取文章時發生錯誤！({{ posts.type }}: {{ posts.status }})
                        </div>
                    </transition>
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

<template id="swal-post-detail">
    <swal-html></swal-html>
    <swal-param name="allowEscapeKey" value="true" />
</template>

<script type="module">
    import { createApp, reactive, onMounted } from '<?=Uri::js('vue')?>';
    import '<?=Uri::js('ajax')?>';
    import * as Resp from '<?=Uri::js('resp')?>';
    import * as Directives from '<?=Uri::js('vue/directives/click-away')?>';
    // moment.js to set datetime format
    import moment from '<?=Uri::js('moment')?>';
    // dynamically load current locale package of moment.js
    try{
        let locale = (window.navigator.userLanguage || window.navigator.language);
        await import('<?=Uri::js('moment/locale/','')?>'+locale.toLowerCase()+'.js');
    }catch(e){}
    // 
    const Forum = createApp({setup(){
        let refs = reactive({});
        let setRef = (el) => { refs[el.getAttribute('data-ref-id')] = el; }
        // 
        let user = reactive({
            id: <?=User::get('id','false')?>,
            username: '<?=User::get('username','Guest')?>',
            avatarDefault: '<?=Uri::img('user.png')?>',
            avatar: <?=User::get('avatar', null) !== null ? "'".'data:image/jpeg;base64,'.User::get('avatar')."'" : 'null'?>,
        });
        // 
        let posts = reactive({
            type: 'error',
            status: 'Unexpected',
            data: [],
            message: '發生非預期的錯誤',
            is: {
                getting: true, // init
                noMore: false,
                getError: false,
            },
        });
        // 
        const format = {
            post: (thePosts) => {
                let ret = Array.isArray(thePosts) ? thePosts : [thePosts];
                ret.forEach((item, idx)=>{
                    !('is' in item) && (item.is = {});
                        !('deleting' in item.is) && (item.is.deleting = false);
                        !('removed' in item.is) && (item.is.removed = false);
                        !('viewAllContent' in item.is) && (item.is.viewAllContent = false);
                    !('comments' in item) && (item.comments = {});
                        !('is' in item.comments) && (item.comments.is = {});
                            !('visible' in item.comments.is) && (item.comments.is.visible = false);
                            !('noMore' in item.comments.is) && (item.comments.is.noMore = false);
                            !('init' in item.comments.is) && (item.comments.is.init = false);
                            !('getting' in item.comments.is) && (item.comments.is.getting = false);
                        !('data' in item.comments) && (item.comments.data = []);
                        !('type' in item.comments) && (item.comments.type = 'info');
                        !('status' in item.comments) && (item.comments.status = 'init');
                        !('message' in item.comments) && (item.comments.message = '');
                    // 
                    !('comment' in item) && (item.comment = {});
                        !('preActionComment' in item) && (item.preActionComment = false);
                        !('preReplyComment' in item.comment) && (item.comment.preReplyComment = false);
                        !('is' in item.comment) && (item.comment.is = {});
                            !('creating' in item.comment.is) && (item.comment.is.creating = false);
                        !('content' in item.comment) && (item.comment.content = '');
                        !('creating' in item.comment) && (item.comment.creating = {});
                            !('info' in item.comment.creating) && (item.comment.creating.info = {type:'', status:'', message:'', data:[]});
                            !('content' in item.comment.creating) && (item.comment.creating.content = '');
                    // 
                    return item;
                });
                // 
                if(Array.isArray(thePosts)){ return ret; }
                else{ return ret[0]; }
            },
            comment: (theComment) => {
                let ret = Array.isArray(theComment) ? theComment : [theComment];
                ret.forEach((item, idx)=>{
                    !('is' in item) && (item.is = {});
                        !('removed' in item) && (item.is.removed = false);
                        !('deleting' in item) && (item.is.deleting = false);
                        !('viewAllContent' in item) && (item.is.viewAllContent = false);
                    !('reply' in item) && (item.reply = {});
                        !('is' in item.reply) && (item.reply.is = {});
                            !('creating' in item.reply.is) && (item.reply.is.creating = false);
                        !('creating' in item.reply) && (item.reply.creating = {});
                            !('info' in item.reply.creating) && (item.reply.creating.info = {type:'', status:'', message:'', data:[]});
                            !('content' in item.reply.creating) && (item.reply.creating.content = '');
                    !('replies' in item) && (item.replies = {});
                        !('times' in item.replies) && (item.replies.times = 0);
                        !('is' in item.replies) && (item.replies.is = {});
                            !('noMore' in item.replies.is) && (item.replies.is.noMore = item.replies.times ? false : true);
                        !('data' in item.replies) && (item.replies.data = []);
                        !('type' in item.replies) && (item.replies.type = '');
                        !('status' in item.replies) && (item.replies.status = '');
                        !('message' in item.replies) && (item.replies.message = '');
                    // 
                    return item;
                });
                // 
                if(Array.isArray(theComment)){ return ret; }
                else{ return ret[0]; }
            },
            reply: (theReply) => {
                let ret = Array.isArray(theReply) ? theReply : [theReply];
                ret.forEach((item, idx) => {
                    !('is' in item) && (item.is = {});
                        !('viewAllContent' in item) && (item.is.viewAllContent = false);
                });
                // 
                if(Array.isArray(theReply)){ return ret; }
                else{ return ret[0]; }
            }
        };
        // 
        let post = reactive({
            preActionPost: false, // pid
            is: {
                creating: false,
            },
            // datas using on create()
            creating: {
                info: {},
                content: '',
            },
            // datas using on edit()
            editing: {
                info: {},
                content: '',
            },
            create: () => {
                if(post.is.creating){ return; }
                post.is.creating = true;
                // check data format
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/post/create')?>',
                    data: { content: post.creating.content },
                    dataType: 'json',
                }).always(() => {
                    post.creating.info['type'] = 'error';
                    post.creating.info['status'] = 'unexpected';
                    post.creating.info['message'] = '很抱歉，發生了非預期的錯誤！';
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    // 
                    post.creating.info = {
                        type: resp.type,
                        status: resp.type,
                        data: resp.data,
                        message: resp.message,
                    };
                    // 
                    if(resp.type === 'success'){
                        post.creating.content = '';
                        resp.data = format.post(resp.data);
                        posts.data.unshift(resp.data);
                    }
                }).always(() => {
                    post.is.creating = false;
                    Swal.fire({
                        position: 'bottom-start',
                        icon: post.creating.info.type,
                        title: post.creating.info.message,
                        toast: true,
                        showConfirmButton: false,
                        timer: post.creating.info.type==='success' ? 2000 : false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
            },
            edit: (thePost) => {},
            delete: (thePost) => {
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
                    if(!result.isConfirmed || thePost.is.deleting){ return; }
                    thePost.is.deleting = true;
                    $.ajax({
                        type: "POST",
                        url: '<?=Uri::auth('forum/post/delete')?>',
                        data: { postId: thePost.id },
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
                            msg.text = false;
                            thePost.is.removed = true;
                        }else{
                            msg.icon = resp.type;
                            msg.title = resp.type[0].toUpperCase() + resp.type.slice(1);
                            msg.text = resp.message;
                        }
                    }).always(() => {
                        thePost.is.deleting = false;
                        Swal.fire({
                            ...msg,
                            position: 'bottom-start',
                            toast: true,
                            showConfirmButton: false,
                            timer: msg.icon==='success' ? 2000 : false,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    });
                });
            },
            detail: (thePost) => {
                Swal.fire({
                    template: '#swal-post-detail',
                    showConfirmButton: false,
                    width: '50vw',
                    showClass: {
                        popup: 'animate__animated animate__faster animate__zoomIn'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__faster animate__zoomOut'
                    },
                    html: `
                        <div class="ts-row">
                            <div class="column">
                                <div class="ts-avatar is-large is-circular is-bordered">
                                    <img src="${thePost.poster.avatar ? 'data:image/jpeg;base64,'+thePost.poster.avatar : user.avatarDefault}">
                                </div>
                            </div>
                            <div class="column is-fluid">
                                <div style="line-height: 1.5; text-align: left;">
                                    <div class="ts-text is-heavy">
                                        ${thePost.poster.nickname ? thePost.poster.nickname+' ('+thePost.poster.username+')' : thePost.poster.username}
                                    </div>
                                    <div class="ts-meta is-small is-secondary">
                                        <div class="item">
                                            <div class="ts-icon is-earth-asia-icon"></div>
                                            public
                                        </div>
                                        <a href="#!" class="item" title="${moment(thePost.datetime*1000).format('YYYY/MM/DD hh:mm')}">
                                            <div class="ts-icon is-clock-icon"></div>
                                            ${ moment(thePost.datetime*1000).fromNow() }
                                        </a>
                                        <div class="item">
                                            <div class="ts-icon is-hashtag-icon"></div>
                                            ${ thePost.id }
                                        </div>
                                    </div>
                                </div>
                                <div class="ts-space is-small"></div>
                                <div style="white-space: pre-line; text-align: left;">${thePost.content}</div>
                                <div class="ts-space is-small"></div>
                            </div>
                        </div>
                        `,
                });
            },
        });
        let comment = reactive({
            create: (thePost) => {
                if(thePost.comment.is.creating){ return; }
                thePost.comment.is.creating = true;
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/comment/create')?>',
                    data: {
                        postId: thePost.id,
                        content: thePost.comment.creating.content,
                    },
                    dataType: 'json',
                }).always(() => {
                    thePost.comment.creating.info['type'] = 'error';
                    thePost.comment.creating.info['status'] = 'unexpected';
                    thePost.comment.creating.info['message'] = '很抱歉，發生了非預期的錯誤！';
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    // 
                    thePost.comment.creating.info = {
                        type: resp.type,
                        status: resp.type,
                        data: resp.data,
                        message: resp.message,
                    };
                    // 
                    if(resp.type === 'success'){
                        document.activeElement.blur(); // remove focus status
                        thePost.comment.creating.content = '';
                        resp.data = format.comment(resp.data);
                        resp.data.replies.is.noMore = true;
                        thePost.comments.times += 1;
                        thePost.comments.data.push(resp.data);
                    }
                }).always(() => {
                    thePost.comment.is.creating = false;
                    thePost.comment.creating.info.type === 'success' || Swal.fire({
                        position: 'bottom-start',
                        icon: thePost.comment.creating.info.type,
                        title: thePost.comment.creating.info.message,
                        toast: true,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
            },
            edit: () => {},
            delete: (theComment) => {
                let msg = {
                    icon: 'error',
                    title: '非預期錯誤',
                    text: '很抱歉，發生了非預期的錯誤！',
                };
                // 
                Swal.fire({
                    icon: 'warning',
                    title: '你確定嗎？',
                    text: "即便刪除，留言內容依舊會存放於伺服器一段時間(可能幾個月)，且每個人都應該為自己的言行舉止負責。",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: '是，刪除！',
                    cancelButtonText: '取消',
                    focusCancel: true,
                }).then((result) => {
                    if(!result.isConfirmed || theComment.is.deleting){ return; }
                    theComment.is.deleting = true;
                    $.ajax({
                        type: "POST",
                        url: '<?=Uri::auth('forum/comment/delete')?>',
                        data: { commentId: theComment.id },
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
                            msg.text = false;
                            theComment.is.removed = true;
                        }else{
                            msg.icon = resp.type;
                            msg.title = resp.type[0].toUpperCase() + resp.type.slice(1);
                            msg.text = resp.message;
                        }
                    }).always(() => {
                        theComment.is.deleting = false;
                        Swal.fire({
                            ...msg,
                            position: 'bottom-start',
                            toast: true,
                            showConfirmButton: false,
                            timer: msg.icon==='success' ? 2000 : false,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    });
                });
            },
        });
        let reply = reactive({
            create: (theComment) => {
                if(theComment.reply.is.creating){ return; }
                theComment.reply.is.creating = true;
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/reply/create')?>',
                    data: {
                        replyId: theComment.id,
                        content: theComment.reply.creating.content,
                    },
                    dataType: 'json',
                }).always(() => {
                    theComment.reply.creating.info['type'] = 'error';
                    theComment.reply.creating.info['status'] = 'unexpected';
                    theComment.reply.creating.info['message'] = '很抱歉，發生了非預期的錯誤！';
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    // 
                    theComment.reply.creating.info = {
                        type: resp.type,
                        status: resp.type,
                        data: resp.data,
                        message: resp.message,
                    };
                    // 
                    if(resp.type === 'success'){
                        document.activeElement.blur(); // remove focus status
                        theComment.reply.creating.content = '';
                        theComment.replies.times += 1;
                        resp.data = format.reply(resp.data);
                        theComment.replies.data.push(resp.data);
                    }
                }).always(() => {
                    theComment.reply.is.creating = false;
                    theComment.reply.creating.info.type === 'success' || Swal.fire({
                        position: 'bottom-start',
                        icon: theComment.reply.creating.info.type,
                        title: theComment.reply.creating.info.message,
                        toast: true,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
            },
        });
        // 
        const getPosts = (force=false) => {
            if((posts.is.getting || posts.is.noMore || posts.is.getError) && !force){ return; }
            posts.is.getting = true;
            posts.is.getError = false;
            // 
            let datas = { limit: 12, };
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
                posts.is.getError = true;
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
                        else{
                            if(resp.data.length < datas.limit){ posts.is.noMore = true; }
                            resp.data = format.post(resp.data);
                            posts.data.push(...resp.data);
                            if(!resp.data[0]['id']){ posts.is.getError = true; }
                        }
                    }else{ posts.is.getError = true; }
                } catch (error) { console.error(error); }
            }).always(() => {
                posts.is.getting = false;
            });
        }
        // 
        const getComments = (thePost) => {
            if((thePost.comments.is.getting || thePost.comments.is.noMore || posts.is.getError)){ return; }
            thePost.comments.is.init = true; // dont auto getComments when user has clicked to show comments
            thePost.comments.is.getting = true;
            thePost.comments.is.getError = false;
            // 
            let datas = {
                postId: thePost.id,
                limit: 4,
                orderBy: 'DESC',
            };
            if(thePost.comments.data.length > 0){ datas.before = thePost.comments.data[0].id ; }
            // 
            $.ajax({
                type: "GET",
                url: '<?=Uri::api('forum/comments')?>',
                data: datas,
                dataType: 'json',
            }).always(()=>{
                thePost.comments['type'] = 'error';
                thePost.comments['status'] = 'unexpected';
                thePost.comments['message'] = '發生非預期的錯誤';
            }).fail((xhr, status, error) => {
                console.error(xhr.responseText);
                thePost.comments.is.getError = true;
            }).done((resp) => {
                try {
                    console.log(resp);
                    // check response format is correct
                    if(!Resp.object(resp)){ return false; }
                    // get msg
                    thePost.comments.type = resp.type;
                    thePost.comments.status = resp.status;
                    thePost.comments.message = resp.message;
                    // check if success
                    if(resp.type==='success'){
                        if(resp.data === null || resp.data.length < 1){ thePost.comments.is.noMore = true; }
                        else{
                            if(resp.data.length < datas.limit){ thePost.comments.is.noMore = true; }
                            // resp.data.forEach((theComment) => {
                            //     theComment.replies.is = {
                            //         noMore: theComment.replies.times ? false : true,
                            //     };
                            //     theComment.replies.data = [];
                            // });
                            resp.data = format.comment(resp.data);
                            resp.data = resp.data.reverse();
                            thePost.comments.data.unshift(...resp.data);
                            if(!resp.data[0]['id']){ thePost.comments.is.getError = true; }
                            if(resp.data.length < datas.limit || thePost.comments.times <= thePost.comments.data.length){ thePost.comments.is.noMore = true; }
                        }
                    }else{ thePost.comments.is.getError = true; }
                } catch (error){ console.error(error); }
            }).always(() => {
                thePost.comments.is.getting = false;
            });
        }
        // 
        const getReplies = (theComment) => {
            if((theComment.replies.is.getting || theComment.replies.is.noMore || posts.is.getError)){ return; }
            theComment.replies.is.init = true;
            theComment.replies.is.getting = true;
            theComment.replies.is.getError = false;
            // 
            let datas = {
                commentId: theComment.id,
                limit: 6,
                orderBy: 'DESC',
            };
            if(theComment.replies.data.length > 0){ datas.before = theComment.replies.data[0].id ; }
            // 
            $.ajax({
                type: "GET",
                url: '<?=Uri::api('forum/replies')?>',
                data: datas,
                dataType: 'json',
            }).always(()=>{
                theComment.replies.type = 'error';
                theComment.replies.status = 'unexpected';
                theComment.replies.message = '發生非預期的錯誤';
            }).fail((xhr, status, error) => {
                console.error(xhr.responseText);
                theComment.replies.is.getError = true;
            }).done((resp) => {
                try {
                    console.log(resp);
                    // check response format is correct
                    if(!Resp.object(resp)){ return false; }
                    // get msg
                    theComment.replies.type = resp.type;
                    theComment.replies.status = resp.status;
                    theComment.replies.message = resp.message;
                    // check if success
                    if(resp.type==='success'){
                        if(resp.data === null || resp.data.length < 1){ theComment.replies.is.noMore = true; }
                        else{
                            if(resp.data.length < datas.limit){ theComment.replies.is.noMore = true; }
                            resp.data = format.reply(resp.data);
                            resp.data = resp.data.reverse();
                            theComment.replies.data.unshift(...resp.data);
                            if(!resp.data[0]['id']){ theComment.replies.is.getError = true; }
                            if(resp.data.length < datas.limit || theComment.replies.times <= theComment.replies.data.length){ theComment.replies.is.noMore = true; }
                        }
                    }else{ theComment.replies.is.getError = true; }
                } catch (error){ console.error(error); }
            }).always(() => {
                theComment.replies.is.getting = false;
            });
        }
        // 
        onMounted(() => {
            // moment.js locale
            let locale = (window.navigator.userLanguage || window.navigator.language);
            moment.updateLocale(locale);
            // get posts when beginning
            getPosts(true);
            // scroll event
            addEventListener("scroll", (e) => {
                let scrollTop = document.documentElement.scrollTop;
                let clientHeight = document.documentElement.clientHeight;
                let scrollHeight = document.documentElement.scrollHeight;
                if(scrollTop+clientHeight > (scrollHeight-scrollHeight/5)){ posts.is.noMore || getPosts(); }
            });
        });
        // 
        return { user, posts, post, setRef, getPosts, moment, getComments, getReplies, comment, reply };
    }}).directive("clickAway",
        Directives.clickAway
    ).mount('#Forum');
</script>

<?php
Inc::component('footer');