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

                        <!-- submit button -->
                        <div class="ts-space is-small"></div>
                        <div class="ts-row">
                            <div class="column is-fluid"></div>
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
                                <div v-if="thePost.is.removed" class="ts-segment is-tertiary animate__animated">
                                    這裡曾有過一篇文章，但已不復存在。
                                </div>
                            </transition>
                            <!-- when post is removed end -->

                            <!-- post -->
                            <transition leave-active-class="animate__hinge">
                                <div v-if="!thePost.is.removed" :id="'Post-'+thePost.id" class="animate__animated" :style="{'animation-duration': '900ms'}" v-cloak>
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
                                                        <a href="#!" class="item" :title="moment(thePost.datetime*1000).format('YYYY/MM/DD hh:mm:ss')">
                                                            <div class="ts-icon is-clock-icon"></div>
                                                            {{ moment(thePost.datetime*1000).fromNow() }}
                                                        </a>
                                                        <a v-if="thePost.edited.last_datetime" href="#!" class="item" :title="'在 ' + moment(thePost.edited.last_datetime*1000).fromNow() + ' 編輯'">
                                                            <div class="ts-icon is-pen-to-square-icon"></div>
                                                            已編輯
                                                        </a>
                                                        <div v-if="is.Dev" class="item">
                                                            <div class="ts-icon is-hashtag-icon"></div>
                                                            {{ thePost.id }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ts-space is-small"></div>

                                                <template v-if="!thePost.is.preEditing">
                                                    <div v-html="thePost.content" :style="{'max-height': thePost.is.viewAllContent ? '' : '8.6rem' }" style="width: 30rem; overflow: hidden; overflow-wrap: break-word; white-space: pre-line;"></div>
                                                    <a v-show="thePost.content.split(/\r\n|\r|\n/).length > 4" @click="thePost.is.viewAllContent=!thePost.is.viewAllContent" href="#!" class="item ts-text is-tiny is-link">{{ thePost.is.viewAllContent ? '顯示較少' : '…顯示更多' }}</a>
                                                </template>
                                                <template v-else>
                                                    <!-- when post is preEditing -->
                                                    <div class="ts-input is-fluid is-underlined" style="width: 30.2rem;">
                                                        <textarea 
                                                            :readonly="thePost.is.editing"
                                                            v-model="thePost.preEditing.content" 
                                                            :placeholder="thePost.content" 
                                                            oninput="this.style.height='1px'; this.style.height=this.scrollHeight+4+'px';" 
                                                            onkeydown="this.oninput()" 
                                                            onfocus="this.oninput()"
                                                            v-focus
                                                        ></textarea>
                                                        <!-- post editing load -->
                                                        <div v-show="thePost.is.editing" class="ts-mask is-blurring">
                                                            <div class="ts-center">
                                                                <div class="ts-content" style="color: #FFF">
                                                                    <div class="ts-loading is-small"></div> 編輯中
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- post editing load end -->
                                                    </div>

                                                    <!-- submit and cancel buttons -->
                                                    <div class="ts-space is-small"></div>
                                                    <div class="ts-row">
                                                        <div class="column is-fluid"></div>
                                                        <div class="column">
                                                            <div class="ts-wrap">
                                                                <button @click="thePost.is.preEditing=false; thePost.preEditing.content=thePost.content" :class="{'is-disabled': thePost.is.editing}" :disabled="thePost.is.editing" class="ts-button is-outlined is-small is-dense" v-cloak>取消</button>
                                                                <button @click="post.edit(thePost)" :class="{'is-disabled': thePost.is.editing}" class="ts-button is-end-labeled-icon is-small is-dense" v-cloak>
                                                                    <span v-show="!thePost.is.editing" class="ts-icon is-pen-to-square-icon"></span>
                                                                    <div v-show="thePost.is.editing" class="ts-icon">
                                                                        <div class="ts-loading is-small"></div>
                                                                    </div>
                                                                    {{ post.is.creating ? '正在編輯貼文...' : '編輯' }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- submit and cancel buttons end -->
                        
                                                    <!-- when post is preEditing end -->
                                                </template>

                                            </div>

                                            <!-- post actions -->
                                            <div v-show="user.id===thePost.poster.id" class="column" style="max-height: 20px">
                                                <a @click="post.preActionPost=thePost.id" v-click-outside="()=>post.preActionPost=post.preActionPost===thePost.id?false:post.preActionPost" style="cursor: pointer;">
                                                    <span class="ts-icon is-spaced is-ellipsis-icon"></span>
                                                </a>
                                                <div :class="{ 'is-visible': post.preActionPost===thePost.id }" class="ts-dropdown is-small is-dense is-separated is-bottom-right">
                                                    <button class="item" @click="thePost.preEditing.content=thePost.content; thePost.is.preEditing=true">編輯</button>
                                                    <div class="ts-divider"></div>
                                                    <button class="item" @click="post.delete(thePost)">刪除</button>
                                                </div>
                                            </div>
                                            <!-- post actions end -->

                                        </div>
                                        <!-- post interactive -->
                                        <div class="ts-divider is-section"></div>
                                        <div class="ts-row">
                                            <div class="column is-fluid">
                                                <button @click="thePost.liked.have ? post.unlike(thePost) : post.like(thePost)" :disabled="thePost.is.liking || thePost.is.unliking" :class="{'is-disabled': thePost.is.liking || thePost.is.unliking}" class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                                    <span :class="{'is-regular':!thePost.liked.have}" class="ts-icon is-heart-icon"></span>
                                                    {{ thePost.liked.have ? '收回喜歡' : '喜歡' }}
                                                    <span v-show="thePost.liked.count" class="ts-badge is-outlined is-start-spaced">{{ thePost.liked.count }}</span>
                                                </button>
                                            </div>
                                            <div class="column is-fluid">
                                                <button @click="thePost.comments.is.visible=!thePost.comments.is.visible; thePost.comments.is.init || getComments(thePost)" class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                                    <span :class="{'is-regular':!thePost.comments.is.visible}" class="ts-icon is-comment-icon"></span>
                                                    留言
                                                    <span v-show="thePost.comments.count" class="ts-badge is-outlined is-start-spaced">{{ thePost.comments.count }}</span>
                                                </button>
                                            </div>
                                            <div class="column is-fluid">
                                                <button @click="post.share(thePost)" class="ts-button is-dense is-start-icon is-ghost is-fluid">
                                                    <span :class="{'is-regular':!thePost.is.preSharing}" class="ts-icon is-share-from-square-icon"></span>
                                                    分享
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

                                        <template v-if="!thePost.comments.is.noMore && thePost.comments.is.visible && !thePost.comments.is.getting" v-cloak>
                                            <div class="ts-space"></div>
                                            <div class="ts-divider is-start-text">
                                                <a @click="getComments(thePost)" href="#!" class="item ts-text is-tiny is-link">載入更多關於這則貼文的 {{thePost.comments.count - thePost.comments.data.length }} 則留言</a>
                                            </div>
                                            <div v-show="thePost.comments.is.getError" class="ts-divider is-start-text">
                                                <span class="ts-text is-tiny is-negative">載入關於這則貼文的留言時發生錯誤</span>
                                            </div>
                                        </template>
                                        <!-- comments loading end -->

                                        <transition-group enter-active-class="animate__faster animate__fadeIn" leave-active-class="animate__fadeOut">
                                            <div v-for="theComment in thePost.comments.data" :key="theComment" v-show="thePost.comments.is.visible" class="animate__animated" :style="{'animation-duration': '250ms'}" v-cloak>

                                                <div class="ts-space"></div>

                                                <!-- when comment is removed -->
                                                <transition enter-active-class="animate__slow animate__flipInX">
                                                    <div v-if="theComment.is.removed" class="ts-segment is-tertiary is-dense animate__animated">
                                                        這裡曾有過一則留言，但已隨風而去。
                                                    </div>
                                                </transition>
                                                <!-- when comment is removed end -->

                                                <!-- comment -->
                                                <transition leave-active-class="animate__hinge">
                                                    <div v-if="!theComment.is.removed" class="ts-row" :style="{'animation-duration': '900ms'}">

                                                        <div class="column">
                                                            <div class="ts-conversation">
                                                                <div class="avatar ts-image">
                                                                    <img :src="theComment.commenter.avatar?('data:image/jpeg;base64,'+theComment.commenter.avatar):user.avatarDefault">
                                                                </div>
                                                                <div class="content" style="max-width: 35rem;">
                                                                    <div class="bubble">
                                                                        <div class="author">
                                                                            <a class="ts-text is-undecorated">{{ theComment.commenter.username }}</a>
                                                                        </div>
                                                                        <template v-if="!theComment.is.preEditing">
                                                                            <div v-html="theComment.content" :style="{'max-height': theComment.is.viewAllContent ? '' : '4.3rem' }" style="max-width: 28rem; overflow: hidden; overflow-wrap: break-word; white-space: pre-line;"></div>
                                                                            <a v-show="theComment.content.split(/\r\n|\r|\n/).length > 4" @click="theComment.is.viewAllContent=!theComment.is.viewAllContent" href="#!" class="item ts-text is-tiny is-link">{{ theComment.is.viewAllContent ? '顯示較少' : '…顯示更多' }}</a>
                                                                        </template>
                                                                        <template v-else>
                                                                            <!-- when comment is preEditing -->
                                                                            <div class="ts-input is-fluid is-underlined" style="width: 30rem;">
                                                                                <textarea 
                                                                                    @keydown.enter.exact.prevent="comment.edit(theComment)" 
                                                                                    @keydown.enter.shift.exact.prevent="theComment.preEditing.content += '\n'" 
                                                                                    :readonly="theComment.is.editing"
                                                                                    v-model="theComment.preEditing.content" 
                                                                                    :placeholder="theComment.content" 
                                                                                    oninput="this.style.height='1px'; this.style.height=this.scrollHeight+4+'px';" 
                                                                                    onkeydown="this.oninput()" 
                                                                                    onfocus="this.oninput()"
                                                                                    v-focus
                                                                                ></textarea>
                                                                            </div>
                                                                        </template>

                                                                        <!-- comment editing load -->
                                                                        <div v-show="theComment.is.editing" class="ts-mask is-blurring">
                                                                            <div class="ts-center">
                                                                                <div class="ts-content" style="color: #FFF">
                                                                                    <div class="ts-loading is-small"></div> 編輯中
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- comment editing load end -->

                                                                        <!-- comment deleting load -->
                                                                        <div v-show="theComment.is.deleting" class="ts-mask is-blurring">
                                                                            <div class="ts-center">
                                                                                <div class="ts-content" style="color: #FFF">
                                                                                    <div class="ts-loading is-small"></div> 刪除中
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- comment deleting load end -->

                                                                    </div>
                                                                    <div class="ts-row">
                                                                        <div class="ts-meta is-small is-secondary column is-fluid">
                                                                            <a href="#!" class="item" @click="theComment.liked.have ? comment.unlike(theComment) : comment.like(theComment)">
                                                                                {{ theComment.liked.have ? '收回讚' : '讚' }}
                                                                            </a>
                                                                            <a @click="thePost.comment.preReplyComment=(thePost.comment.preReplyComment===theComment.id) ? false : theComment.id" href="#!" class="item">回覆</a>
                                                                            <div class="item">
                                                                                <a href="#!" :title="moment(theComment.datetime*1000).format('YYYY/MM/DD hh:mm:ss')" class="ts-text is-undecorated">
                                                                                    {{ moment(theComment.datetime*1000).fromNow() }}
                                                                                </a>
                                                                                <a href="#!" :title="'在 ' + moment(theComment.edited.last_datetime*1000).fromNow() + ' 編輯'" class="ts-text is-undecorated">
                                                                                    {{ theComment.edited.count > 0 ? ' (已編輯)' : '' }}
                                                                                </a>
                                                                            </div>
                                                                            <div v-show="is.Dev" class="item">
                                                                                <div class="ts-icon is-hashtag-icon"></div>
                                                                                {{ theComment.id }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="column">
                                                                            <div v-show="theComment.is.preEditing && !theComment.is.editing">
                                                                                <a href="#!" @click="theComment.is.preEditing=false" class="ts-text is-link">
                                                                                    <div class="ts-icon is-xmark-icon"></div> 取消編輯
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- comment actions -->
                                                        <div v-show="user.id===theComment.commenter.id" class="column" style="max-height: 20px; max-width: 30px">
                                                            <a @click="thePost.comment.preActionComment=theComment.id" v-click-outside="()=>thePost.comment.preActionComment=thePost.comment.preActionComment===theComment.id?false:thePost.comment.preActionComment" style="cursor: pointer;">
                                                                <span class="ts-icon is-ellipsis-icon"></span>
                                                            </a>
                                                            <div :class="{ 'is-visible': thePost.comment.preActionComment===theComment.id }" class="ts-dropdown is-small is-dense is-separated is-bottom-right">
                                                                <button class="item" @click="theComment.preEditing.content=theComment.content; theComment.is.preEditing=true;">編輯</button>
                                                                <div class="ts-divider"></div>
                                                                <button class="item" @click="comment.delete(theComment)">刪除</button>
                                                            </div>
                                                        </div>
                                                        <!-- comment actions end -->

                                                    </div>
                                                </transition>
                                                <!-- comment end -->

                                                <!-- replies -->
                                                <div v-if="!theComment.is.removed" class="ts-row">
                                                <!-- theComment.replies.count > 0  -->
                                                    <!-- 縮排 -->
                                                    <div class="column" style="width: 2.7rem;"></div>

                                                    <div class="column is-fluid">

                                                        <!-- replies loading -->
                                                        <div v-show="theComment.replies.is.getting" v-show="theComment.replies.is.visible" class="ts-placeholder is-loading" v-cloak>
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

                                                        <template v-if="!theComment.replies.is.noMore && !theComment.replies.is.getting">
                                                            <div class="ts-divider is-start-text">
                                                                <div class="column is-fluid">
                                                                    <a @click="getReplies(theComment)" href="#!" class="item ts-text is-tiny is-link">載入更多關於這則留言的 {{theComment.replies.count - theComment.replies.data.length }} 則回應</a>
                                                                </div>
                                                            </div>
                                                            <div v-show="theComment.replies.is.getError" class="ts-divider is-start-text">
                                                                <div class="column is-fluid">
                                                                    <span class="ts-text is-tiny is-negative">載入關於這則留言的回應時發生錯誤</span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <!-- replies loading end -->
                                                        <template v-else-if="!theComment.replies.is.getting && theComment.replies.count > 0">
                                                            <div class="ts-divider is-start-text">
                                                                <div class="column is-fluid">
                                                                    <a @click="theComment.replies.is.visible=!theComment.replies.is.visible" href="#!" class="item ts-text is-tiny is-link">
                                                                        {{theComment.replies.is.visible ? '隱藏以下 '+theComment.replies.data.length+' 則回應' : '顯示關於這則留言的 '+theComment.replies.data.length+' 則回應'}}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <transition-group enter-active-class="animate__faster animate__fadeIn" leave-active-class="animate__fadeOut">
                                                            <div v-show="theComment.replies.is.visible" v-for="theReply in theComment.replies.data" :key="theReply" class="animate__animated" :style="{'animation-duration': '250ms'}" v-cloak>

                                                                <div class="ts-space"></div>

                                                                <!-- when reply is removed -->
                                                                <transition enter-active-class="animate__slow animate__flipInX">
                                                                    <div v-if="theReply.is.removed" class="ts-segment is-tertiary is-dense animate__animated">
                                                                        這裡曾有過一則回應，但已蕩然無存。
                                                                    </div>
                                                                </transition>
                                                                <!-- when reply is removed end -->

                                                                <!-- reply -->
                                                                <transition leave-active-class="animate__hinge">
                                                                    <div v-if="!theReply.is.removed" class="ts-row" :style="{'animation-duration': '900ms'}">

                                                                        <div class="column">
                                                                            <div class="ts-conversation">
                                                                                <div class="avatar ts-image">
                                                                                    <img :src="theReply.replier.avatar?('data:image/jpeg;base64,'+theReply.replier.avatar):user.avatarDefault">
                                                                                </div>
                                                                                <div class="content" style="max-width: 28rem;">
                                                                                    <div class="bubble">
                                                                                        <div class="author">
                                                                                            <a class="ts-text is-undecorated">{{ theReply.replier.username }}</a>
                                                                                        </div>
                                                                                        <template v-if="!theReply.is.preEditing">
                                                                                            <div v-html="theReply.content" :style="{'max-height': theReply.is.viewAllContent ? '' : '4.3rem' }" style="max-width: 24.3rem; overflow: hidden; overflow-wrap: break-word; white-space: pre-line;"></div>
                                                                                            <a v-show="theReply.content.split(/\r\n|\r|\n/).length > 4" @click="theReply.is.viewAllContent=!theReply.is.viewAllContent" href="#!" class="item ts-text is-tiny is-link">{{ theReply.is.viewAllContent ? '顯示較少' : '…顯示更多' }}</a>
                                                                                        </template>
                                                                                        <template v-else>
                                                                                            <!-- when reply is preEditing -->
                                                                                            <div class="ts-input is-fluid is-underlined" style="width: 26.3rem;">
                                                                                                <textarea 
                                                                                                    @keydown.enter.exact.prevent="reply.edit(theReply)" 
                                                                                                    @keydown.enter.shift.exact.prevent="theReply.preEditing.content += '\n'" 
                                                                                                    :readonly="theReply.is.editing"
                                                                                                    v-model="theReply.preEditing.content" 
                                                                                                    :placeholder="theReply.content" 
                                                                                                    oninput="this.style.height='1px'; this.style.height=this.scrollHeight+4+'px';" 
                                                                                                    onkeydown="this.oninput()" 
                                                                                                    onfocus="this.oninput()"
                                                                                                    v-focus
                                                                                                ></textarea>
                                                                                            </div>
                                                                                            <!-- when reply is preEditing end -->
                                                                                        </template>

                                                                                        <!-- reply editing load -->
                                                                                        <div v-show="theReply.is.editing" class="ts-mask is-blurring">
                                                                                            <div class="ts-center">
                                                                                                <div class="ts-content" style="color: #FFF">
                                                                                                    <div class="ts-loading is-small"></div> 編輯中
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- reply editing load end -->
                                                                                        
                                                                                        <!-- reply deleting load -->
                                                                                        <div v-show="theReply.is.deleting" class="ts-mask is-blurring">
                                                                                            <div class="ts-center">
                                                                                                <div class="ts-content" style="color: #FFF">
                                                                                                    <div class="ts-loading is-small"></div> 刪除中
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- reply deleting load end -->

                                                                                    </div>

                                                                                    <div class="ts-row">
                                                                                        <div class="ts-meta is-small is-secondary column is-fluid">
                                                                                            <a class="item">讚</a>
                                                                                            <!-- <a class="item">回覆</a> -->
                                                                                            <div class="item">
                                                                                                <a href="#!" :title="moment(theReply.datetime*1000).format('YYYY/MM/DD hh:mm:ss')" class="ts-text is-undecorated">
                                                                                                    {{ moment(theReply.datetime*1000).fromNow() }}
                                                                                                </a>
                                                                                                <a href="#!" :title="'在 ' + moment(theReply.edited.last_datetime*1000).fromNow() + ' 編輯'" class="ts-text is-undecorated">
                                                                                                    {{ theReply.edited.count > 0 ? ' (已編輯)' : '' }}
                                                                                                </a>
                                                                                            </div>
                                                                                            <div v-show="is.Dev" class="item">
                                                                                                <div class="ts-icon is-hashtag-icon"></div>
                                                                                                {{ theReply.id }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="column">
                                                                                            <div v-show="theReply.is.preEditing && !theReply.is.editing">
                                                                                                <a href="#!" @click="theReply.is.preEditing=false" class="ts-text is-link">
                                                                                                    <div class="ts-icon is-xmark-icon"></div> 取消編輯
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- reply actions -->
                                                                        <div v-show="user.id===theReply.replier.id" class="column" style="max-height: 20px; max-width: 30px">
                                                                            <a @click="theComment.reply.preActionReply=theReply.id" v-click-outside="()=>theComment.reply.preActionReply=theComment.reply.preActionReply===theReply.id?false:theComment.reply.preActionReply" style="cursor: pointer;">
                                                                                <span class="ts-icon is-ellipsis-icon"></span>
                                                                            </a>
                                                                            <div :class="{ 'is-visible': theComment.reply.preActionReply===theReply.id }" class="ts-dropdown is-small is-dense is-separated is-bottom-right">
                                                                                <button class="item" @click="theReply.preEditing.content=theReply.content; theReply.is.preEditing=true">編輯</button>
                                                                                <div class="ts-divider"></div>
                                                                                <button class="item" @click="reply.delete(theReply)">刪除</button>
                                                                            </div>
                                                                        </div>
                                                                        <!-- reply actions end -->
                                                                        
                                                                    </div>
                                                                </transition>
                                                                <!-- reply end -->

                                                            </div>
                                                        </transition-group>

                                                        <transition enter-active-class="animate__slow animate__flipInX" leave-active-class="animate__fadeOut">
                                                            <div v-if="thePost.comment.preReplyComment===theComment.id" class="animate__animated" :style="{'animation-duration': thePost.comment.preReplyComment===theComment.id ? '500ms' : '250ms'}">
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
                                                                                v-focus
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
                                                <!-- replies end -->

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
                                        
                                        <!-- post deleting load -->
                                        <div v-show="thePost.is.deleting" class="ts-mask is-blurring">
                                            <div class="ts-center">
                                                <div class="ts-content" style="color: #FFF">
                                                    <div class="ts-loading"></div> 刪除中
                                                </div>
                                            </div>
                                        </div>
                                        <!-- post deleting load end -->

                                    </div>
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
                                    <span class="ts-icon is-heart-icon is-regular"></span>
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

<template id="swal-post-share">
    <swal-html></swal-html>
    <swal-param name="allowEscapeKey" value="true" />
</template>

<script type="module">
    import { createApp, reactive, onMounted } from '<?=Uri::js('vue')?>';
    import '<?=Uri::js('ajax')?>';
    import * as Resp from '<?=Uri::js('resp')?>';
    import { focus, clickOutside } from '<?=Uri::js('vue/directives')?>';
    const Directives = { focus, clickOutside };
    
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
        let is = reactive({
            Dev: <?=DEV?'true':'false'?>
        });
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
                    !('preEditing' in item) && (item.preEditing = {});
                        !('content' in item.preEditing) && (item.preEditing.content = '');
                    !('is' in item) && (item.is = {});
                        !('deleting' in item.is) && (item.is.deleting = false);
                        !('editing' in item.is) && (item.is.editing = false);
                        !('liking' in item.is) && (item.is.liking = false);
                        !('unliking' in item.is) && (item.is.unliking = false);
                        !('removed' in item.is) && (item.is.removed = false);
                        !('viewAllContent' in item.is) && (item.is.viewAllContent = false);
                        !('preEditing' in item.is) && (item.is.preEditing = false);
                        !('preSharing' in item.is) && (item.is.preSharing = false);
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
                    !('preEditing' in item) && (item.preEditing = {});
                        !('content' in item) && (item.preEditing.content = '');
                    !('is' in item) && (item.is = {});
                        !('removed' in item) && (item.is.removed = false);
                        !('deleting' in item) && (item.is.deleting = false);
                        !('viewAllContent' in item) && (item.is.viewAllContent = false);
                        !('preEditing' in item) && (item.is.preEditing = false);
                    !('reply' in item) && (item.reply = {});
                        !('preActionReply' in item) && (item.preActionReply = false);
                        !('is' in item.reply) && (item.reply.is = {});
                            !('creating' in item.reply.is) && (item.reply.is.creating = false);
                        !('creating' in item.reply) && (item.reply.creating = {});
                            !('info' in item.reply.creating) && (item.reply.creating.info = {type:'', status:'', message:'', data:[]});
                            !('content' in item.reply.creating) && (item.reply.creating.content = '');
                    !('replies' in item) && (item.replies = {});
                        !('count' in item.replies) && (item.replies.count = 0);
                        !('is' in item.replies) && (item.replies.is = {});
                            !('visible' in item.replies.is) && (item.replies.is.visible = true);
                            !('noMore' in item.replies.is) && (item.replies.is.noMore = item.replies.count ? false : true);
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
                    !('preEditing' in item) && (item.preEditing = {});
                        !('content' in item.preEditing) && (item.preEditing.content = '');
                    !('is' in item) && (item.is = {});
                        !('viewAllContent' in item) && (item.is.viewAllContent = false);
                        !('deleting' in item) && (item.is.deleting = false);
                        !('removed' in item) && (item.is.removed = false);
                        !('preEditing' in item) && (item.is.preEditing = false);
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
            edit: (thePost) => {
                if(thePost.is.editing){ return; }
                thePost.is.editing = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤！',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/post/edit')?>',
                    data: {
                        postId: thePost.id,
                        content: thePost.preEditing.content,
                    },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    // 
                    info = {
                        type: resp.type,
                        status: resp.type,
                        data: resp.data,
                        message: resp.message,
                    };
                    // 
                    if(resp.type === 'success'){
                        if(resp.data){
                            thePost.content = resp.data.content;
                            thePost.edited.last_datetime = resp.data.edited.last_datetime;
                            thePost.edited.count = resp.data.edited.count;
                        }
                        thePost.is.preEditing = false;
                    }
                }).always(() => {
                    thePost.is.editing = false;
                    Swal.fire({
                        position: 'bottom-start',
                        icon: info.type,
                        title: info.message,
                        toast: true,
                        showConfirmButton: false,
                        timer: info.type==='success' ? 2000 : false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
            },
            delete: (thePost) => {
                if(thePost.is.deleting){ return; }
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
            share: (thePost) => {
                if(thePost.is.preSharing){ return; }
                thePost.is.preSharing = true;
                Swal.fire({
                    template: '#swal-post-share',
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
                                        <a href="#!" class="item" title="${moment(thePost.datetime*1000).format('YYYY/MM/DD hh:mm:ss')}">
                                            <div class="ts-icon is-clock-icon"></div>
                                            ${ moment(thePost.datetime*1000).fromNow() }
                                        </a>
                                        <div class="item">
                                            <div class="ts-icon is-hashtag-icon"></div>
                                            ${ thePost.id }
                                        </div>
                                        `+(
                                            thePost.edited.last_datetime ? 
                                        `<a href="#!" class="item" title="在 ${moment(thePost.edited.last_datetime*1000).fromNow()} 編輯">
                                            <div class="ts-icon is-pen-to-square-icon"></div>
                                            已編輯
                                        </a>` : ''
                                        )+`
                                    </div>
                                </div>
                                <div class="ts-space is-small"></div>
                                <div style="white-space: pre-line; text-align: left;">${thePost.content}</div>
                                <div class="ts-space is-small"></div>
                            </div>
                        </div>
                        `,
                }).then(()=>{
                    thePost.is.preSharing = false;
                });
            },
            like: (thePost) => {
                if(thePost.is.liking || thePost.is.unliking){ return; }
                thePost.is.liking = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/post/like')?>',
                    data: { postId: thePost.id },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    info = resp;
                    // 
                    if(resp.type === 'success'){
                        thePost.liked.have = 1;
                        if(resp.status==='successfully'){
                            thePost.liked.count += 1;
                        }
                    }
                }).always(() => {
                    thePost.is.liking = false;
                    info.type==='success' || Swal.fire({
                        icon: info.type,
                        title: info.type[0].toUpperCase() + info.type.slice(1),
                        text: info.message,
                        position: 'bottom-start',
                        toast: true,
                        showConfirmButton: false,
                        timer: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
                // 
            },
            unlike: (thePost) => {
                if(thePost.is.unliking || thePost.is.liking){ return; }
                thePost.is.unliking = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/post/unlike')?>',
                    data: { postId: thePost.id },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    info = resp;
                    // 
                    if(resp.type === 'success'){
                        thePost.liked.have = 0;
                        if(resp.status==='successfully'){
                            thePost.liked.count -= 1;
                        }
                    }
                }).always(() => {
                    thePost.is.unliking = false;
                    info.type==='success' || Swal.fire({
                        title: info.type[0].toUpperCase() + info.type.slice(1),
                        text: info.message,
                        position: 'bottom-start',
                        toast: true,
                        showConfirmButton: false,
                        timer: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
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
                        thePost.comments.count += 1;
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
            edit: (theComment) => {
                if(theComment.is.editing){ return; }
                theComment.is.editing = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤！',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/comment/edit')?>',
                    data: {
                        commentId: theComment.id,
                        content: theComment.preEditing.content,
                    },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    // 
                    info = {
                        type: resp.type,
                        status: resp.type,
                        data: resp.data,
                        message: resp.message,
                    };
                    // 
                    if(resp.type === 'success'){
                        if(resp.data){
                            theComment.content = resp.data.content;
                            theComment.edited.last_datetime = resp.data.edited.last_datetime;
                            theComment.edited.count = resp.data.edited.count;
                        }
                        theComment.is.preEditing = false;
                    }
                }).always(() => {
                    theComment.is.editing = false;
                    Swal.fire({
                        position: 'bottom-start',
                        icon: info.type,
                        title: info.message,
                        toast: true,
                        showConfirmButton: false,
                        timer: info.type==='success' ? 2000 : false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
            },
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
            like: (theComment) => {
                if(theComment.is.liking || theComment.is.unliking){ return; }
                theComment.is.liking = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/comment/like')?>',
                    data: { commentId: theComment.id },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    info = resp;
                    // 
                    if(resp.type === 'success'){
                        theComment.liked.have = 1;
                        if(resp.status==='successfully'){
                            theComment.liked.count += 1;
                        }
                    }
                }).always(() => {
                    theComment.is.liking = false;
                    info.type==='success' || Swal.fire({
                        icon: info.type,
                        title: info.type[0].toUpperCase() + info.type.slice(1),
                        text: info.message,
                        position: 'bottom-start',
                        toast: true,
                        showConfirmButton: false,
                        timer: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
                // 
            },
            unlike: (theComment) => {
                if(theComment.is.unliking || theComment.is.liking){ return; }
                theComment.is.unliking = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/comment/unlike')?>',
                    data: { commentId: theComment.id },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    info = resp;
                    // 
                    if(resp.type === 'success'){
                        theComment.liked.have = 0;
                        if(resp.status==='successfully'){
                            theComment.liked.count -= 1;
                        }
                    }
                }).always(() => {
                    theComment.is.unliking = false;
                    info.type==='success' || Swal.fire({
                        title: info.type[0].toUpperCase() + info.type.slice(1),
                        text: info.message,
                        position: 'bottom-start',
                        toast: true,
                        showConfirmButton: false,
                        timer: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
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
                        theComment.replies.count += 1;
                        resp.data = format.reply(resp.data);
                        theComment.replies.data.push(resp.data);
                        theComment.replies.is.visible = true;
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
            edit: (theReply) => {
                if(theReply.is.editing){ return; }
                theReply.is.editing = true;
                // 
                let info = {
                    type: 'error',
                    status: 'unexpected',
                    data: [],
                    message: '很抱歉，發生了非預期的錯誤！',
                };
                // 
                $.ajax({
                    type: "POST",
                    url: '<?=Uri::auth('forum/reply/edit')?>',
                    data: {
                        replyId: theReply.id,
                        content: theReply.preEditing.content,
                    },
                    dataType: 'json',
                }).fail((xhr, status, error) => {
                    console.error(xhr.responseText);
                }).done((resp) => {
                    console.log(resp);
                    if(!Resp.object(resp)){ return false; }
                    // 
                    info = {
                        type: resp.type,
                        status: resp.type,
                        data: resp.data,
                        message: resp.message,
                    };
                    // 
                    if(resp.type === 'success'){
                        if(resp.data){
                            theReply.content = resp.data.content;
                            theReply.edited.last_datetime = resp.data.edited.last_datetime;
                            theReply.edited.count = resp.data.edited.count;
                        }
                        theReply.is.preEditing = false;
                    }
                }).always(() => {
                    theReply.is.editing = false;
                    Swal.fire({
                        position: 'bottom-start',
                        icon: info.type,
                        title: info.message,
                        toast: true,
                        showConfirmButton: false,
                        timer: info.type==='success' ? 2000 : false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                });
            },
            delete: (theReply) => {
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
                    if(!result.isConfirmed || theReply.is.deleting){ return; }
                    theReply.is.deleting = true;
                    $.ajax({
                        type: "POST",
                        url: '<?=Uri::auth('forum/reply/delete')?>',
                        data: { replyId: theReply.id },
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
                            theReply.is.removed = true;
                        }else{
                            msg.icon = resp.type;
                            msg.title = resp.type[0].toUpperCase() + resp.type.slice(1);
                            msg.text = resp.message;
                        }
                    }).always(() => {
                        theReply.is.deleting = false;
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
                            //         noMore: theComment.replies.count ? false : true,
                            //     };
                            //     theComment.replies.data = [];
                            // });
                            resp.data = format.comment(resp.data);
                            resp.data = resp.data.reverse();
                            thePost.comments.data.unshift(...resp.data);
                            if(!resp.data[0]['id']){ thePost.comments.is.getError = true; }
                            if(resp.data.length < datas.limit || thePost.comments.count <= thePost.comments.data.length){ thePost.comments.is.noMore = true; }
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
                            if(resp.data.length < datas.limit || theComment.replies.count <= theComment.replies.data.length){ theComment.replies.is.noMore = true; }
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
        return { user, posts, post, setRef, getPosts, moment, getComments, getReplies, comment, reply, is };
    }})
    .directive("clickOutside", Directives.clickOutside)
    .directive("focus", Directives.focus)
    .mount('#Forum');
</script>

<?php
Inc::component('footer');