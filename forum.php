<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<!-- comments container -->
<div class="ui container" id="Forum">
	<h2 class="content-title">Forum</h2>

	<!-- write post form -->
	<form class="ui form" id="CreatePost" ref='CreatePost'>
		<div class="field"><input type="text" name="title" id="title" ref='title' placeholder="Title..."></div>
		<div class="field"><textarea name="content" id="content" ref='content' placeholder="Content..." rows="2"></textarea></div>
		<div class="ui blue labeled submit icon button right floated"><i class="icon edit"></i> Post it</div>
	</form><!-- end write post form -->

	<h4 class="ui horizontal divider header"><i class="tag icon"></i> 今天想說點什麼? </h4>

	<!-- post -->
	<div class="ui piled teal segment" v-for="(post, post_key) in posts" :class="{ 'loading': isLoading, 'loading':post.isRemoving }">
		<!-- post info & action -->
		<div class="ui feed">
			<div class="event">
				<div class="label">
					<img :src="post.poster.avatar==null?'<?php echo IMG('default','png'); ?>':'data:image/jpeg;base64, '+post.poster.avatar">
				</div>
				<div class="content">
					<div class="summary">
						{{ post.poster.nickname }} <a class="user">{{ post.poster.username }}</a>
						<!-- post edit, remove -->
						<div class="ui small basic icon right floated buttons" v-if="post.poster.id==user.id">
							<button class="ui button label" @click="post.isEditing=true" :class="{ disabled: post.poster.id!=user.id}">
								<i class="edit blue icon"><template v-if="post.edited && post.edited.times>0">&nbsp;{{ post.edited.times }}</template></i>
							</button>
							<button class="ui button" @click="removePost(post.id)"><i class="trash alternate red icon"></i></button>
						</div>
					</div>
					<div class="meta">
						<div class="date"> 
							{{ timeToStatus(post.datetime) }}
							<span class="ui" :data-tooltip="timeToStatus(post.edited.last_time)" data-variation="mini" data-position="bottom left" v-if="post.edited && post.edited.times>0">, Edited post</span>
						</div>
						<!-- <a class="like"><i class="like icon"></i> 4 個讚 </a> -->
					</div>
				</div>
			</div>
		</div>

		<!-- post title & content -->
		<template v-if="post.isEditing">
			<form class="ui form" :id="'editPost_'+post.id" onsubmit="return false;">
				<div class="field">
					<input type="text" id="title" name="title" :placeholder="post.title" :value="post.title">
				</div>
				<div class="field">
					<textarea rows="2" :placeholder="post.content" id="content" name="content">{{ post.content }}</textarea>
				</div>
				<div class="ui right floated buttons">
					<button class="ui button" @click="post.isEditing=false" type="button">Cancel</button>
					<div class="or"></div>
					<button class="ui positive button" @click="editPost(post.id)" type="submit">Save</button>
				</div>
			</form>
			<h4 class="ui horizontal divider header"><i class="edit icon"></i>Editing...</h4>
		</template>
		<template v-else>
			<h2 class="ui left aligned header">{{ post.title }}</h2>
			<span style="white-space: pre-line"> {{ post.content }} </span>
		</template>
		<div class="ui right aligned container"><h5 class="ui grey header">(#{{ post.id }}) {{ timeToString(post.datetime) }}</h5></div>
		
		<!-- post comment -->
		<button class="fluid ui button" @click="getComments(post.id)" :class="{loading : post.gettingComments}" :disabled="post.gettingComments"><i class="eye icon" :class="{ slash : post.showComments }"></i> {{ post.showComments ? "隱藏留言區" : "看看大家說了什麼" }} </button>

		<br>

		<div class="ui container">
			<div class="content">
				<!-- comments -->
				<div class="ui comments" :class="{ collapsed: !post.showComments }">
					
					<template v-if="post.gettingComments">
						<br><a class="ui label black"><i class="spinner loading icon"></i>&nbsp;Loading...</a>
					</template>
					
					<template v-if="post.comments" v-for="comment in post.comments">
						<div class="comment" v-if="isNotReply(post.comments,comment.id)">
							<a class="avatar"><img :src="comment.commenter.avatar==null?'<?php echo IMG('default','png'); ?>':'data:image/jpeg;base64, '+comment.commenter.avatar"></a>
							<div class="content">
								{{ comment.commenter.nickname }}
								<a class="author">
									{{ comment.commenter.nickname!=null?" (":"" }}
									{{ comment.commenter.username }}
									{{ comment.commenter.nickname!=null?")":"" }}
								</a>
								<!-- comment edit, remove -->
								<div class="ui small basic icon right floated buttons" v-if="comment.commenter.id==user.id">
									<!-- <button class="ui button label" @click="comment.isEditing=true" :class="{ disabled: comment.commenter.id!=user.id}">
										<i class="edit blue icon"><template v-if="comment.edited && comment.edited.times>0">&nbsp;{{ post.edited.times }}</template></i>
									</button> -->
									<button class="ui button" @click="removeComment(comment.id)"><i class="trash alternate red icon"></i></button>
								</div>
								<div class="metadata"><span class="date">{{ timeToStatus(comment.datetime) }} ({{timeToString(comment.datetime)}})</span></div>

								<template v-if="comment.isEditing">
									<form class="ui form" :id="'editComment_'+comment.id" onsubmit="return false;">
										<div class="ui mini action icon fluid input field">
											<input type="text" placeholder="Reply..." :value="comment.content" id="content" name="content" v-focus>
											&nbsp;
											<div class="ui right floated buttons">
												<button class="ui button" @click="comment.isEditing=false" type="button">Cancel</button>
												<div class="or"></div>
												<button class="ui positive button" @click="editComment(comment.id)" type="submit">Save</button>
											</div>
										</div>
									</form>
								</template>
<!-- <template v-if="post.isEditing">
			<form class="ui form" :id="'editPost_'+post.id">
				<div class="field">
					<input type="text" id="title" name="title" :placeholder="post.title" :value="post.title">
				</div>
				<div class="field">
					<textarea rows="2" :placeholder="post.content" id="content" name="content">{{ post.content }}</textarea>
				</div>
				<div class="ui right floated buttons">
					<button class="ui button" @click="post.isEditing=false" type="button">Cancel</button>
					<div class="or"></div>
					<button class="ui positive button" @click="editPost(post.id)" type="submit">Save</button>
				</div>
			</form>
			<h4 class="ui horizontal divider header"><i class="edit icon"></i>Editing...</h4>
		</template> -->
								<template v-else><div class="text"> {{ comment.content }} </div></template>
								<div class="actions"><a class="reply" @click="(post.replyTartget=comment.id)">Reply</a></div>

							</div>
							<!-- replies -->
							<div class="comments">
								<!-- reply of replies -->
								<div class="comment" v-for="reply in filterReplies(post.comments, comment.id)">
									<a class="avatar"><img :src="reply.commenter.avatar==null?'<?php echo IMG('default','png'); ?>':'data:image/jpeg;base64, '+reply.commenter.avatar"></a>
									<div class="content">
										{{ reply.commenter.nickname }}
										<a class="author">
											{{ reply.commenter.nickname!=""?" (":"" }}
											{{ reply.commenter.username }}
											{{ reply.commenter.nickname!=""?")":"" }}
										</a>
										<div class="metadata"><span class="date">{{ timeToStatus(reply.datetime) }} ({{timeToString(reply.datetime)}})</span></div>
										<div class="text">{{ reply.content }}</div>
										<!-- <div class="actions"><a class="reply">Reply</a></div> -->
									</div>
								</div><!-- end reply of replies -->
								<!-- reply form -->
								<form class="ui reply form" @submit="createComment(post.id,comment.id);" onsubmit="return false;" :class="{ loading : comment.isReplying }" v-if="post.replyTartget==comment.id">
									<div class="ui mini action icon fluid input">
										<input type="text" placeholder="Reply..." v-model="comment.replying" :disabled="comment.isReplying" v-focus>
										<button class="ui icon button" :disabled="comment.isReplying"><i class="icon" :class="comment.isReplying?'spinner loading':'edit'"></i>Reply</button>
									</div>
								</form>
								<!-- end reply form -->
							</div><!-- end replies -->
						</div><!-- end comment -->
					</template>

					<!-- comment form -->
					<form class="ui reply form" @submit="createComment(post.id);" onsubmit="return false;">
						<div class="ui action fluid input">
							<input type="text" placeholder="Comment..." v-model="post.commenting" :disabled="post.isCommenting">
							<button class="ui icon blue button" :disabled="post.isCommenting"><i class="icon" :class="post.isCommenting?'spinner loading':'edit'"></i>&nbsp;{{post.isCommenting?"Wait...":"Comment"}}</button>
						</div>
					</form><!-- end comment form -->
					<a v-if="(post.comments) && !(post.comments).length" class="ui teal pointing large label">Be a first one!</a>

					<!-- comment -->
				</div><!-- end comments -->
			</div><!-- end content -->
		</div><!-- end post comment -->
	</div><!-- enc post -->
</div><!-- end container -->

<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	const Posts = createApp({
		data(){return{
			user:{
				'id': '<?php echo $User->Get('id','-'); ?>',
			},
			posts:[],
			isLoading: true,
			postsLimit: 16,
		}},
		methods:{
			timeToString: (datetime)=>{
				let t = new Date(datetime*1000);
				let years = t.getFullYear().toString();
				let months = (t.getMonth() + 1).toString();
				let days = t.getDate();
				let hours = t.getHours();
				let minutes = t.getMinutes();
				let seconds = t.getSeconds();
				if (months<10) { months = "0"+months; }
				if (days<10) { days = "0"+days; }
				if (hours<10) { hours = "0"+hours; }
				if (minutes<10) { minutes = "0"+minutes; }
				if (seconds<10) { seconds = "0"+seconds; }
				return `${years}/${months}/${days} ${hours}:${minutes}:${seconds}`;
			},
			timeToStatus: (datetime)=>{
				let t = new Date(datetime*1000);
				let ct = new Date();
				let result = (ct - t)/1000;
				let ret = "-", unit='-';

				let i=60, h=i*60, d=h*24, w=d*7, m=30*d, y=365*d;
				// just
				if(result < i){ ret = ''; unit='Just'; }
				// minutes
				else if(result < h){ unit='Minutes age'; ret=result/i; }
				// hours
				else if(result < d){ unit='Hours ago'; ret=result/h; }
				// days
				else if(result < w){ unit='Days ago'; ret=result/d; }
				// weeks
				else if(result < m){ unit='Weeks ago'; ret=result/w; }
				// months
				else if(result < y/2){ unit='Months ago'; ret=result/m; }
				// half year
				else if(result < y){ unit='Half year ago'; ret=''; }
				// years
				else{ unit='Years ago'; ret=result/y; }
				//
				ret = ret!=''?parseInt(ret, 10):'';
				return (`${ret} ${unit}`).trim();
			},
			removePost: (postId) => {
				Swal.fire({
					icon: 'warning',
					title: 'Sure?',
					html: `You sure want to remove the post? (#${postId})`,
					showDenyButton: true,
					confirmButtonText: 'Remove it !',
					denyButtonText: `Don't !`,
					focusDeny: true,
				}).then((result)=>{
					if(result.isConfirmed){
						let postKey = Posts.posts.findIndex(((post) => post.id === postId));
						let post = Posts.posts[postKey];
						post.isRemoving = true;
						$.ajax({
							type: "POST",
							url: '<?php echo Page('post/remove'); ?>',
							data: { postId: postId },
							dataType: 'json',
							success: (resp)=>{
								Loger.Log('info','Remove Post',resp);
								let table = {
									'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
									'is_logout': 'Oh no, you are not login.',
									'post_id_format_incorrect': 'Send the wrong data request.',
									'cannot_select': 'We got the error when sql query...',
									'access_denied': 'Sorry, you cannot do it',
									'removed_post': `You removed the post! (#${postId})`,
									'unexpected': 'Un... seems has some errors, sorry.',
								}
								let config = [];
								if(Loger.Check(resp,'success')){
									Posts.posts.splice(postKey,1);
									// Posts.skipPosts -= 1;
									config.timer = 1600;
								}
								Loger.Swal(resp,table, config);
							},
							error: (resp)=>{
								Loger.Log('error','Error Remove Post',resp);
							}
						}).then(()=>{ post.isRemoving = false; });
					}
				});
			},
			removeComment: (commentId) => {
				Swal.fire({
					icon: 'warning',
					title: 'Sure?',
					html: `You sure want to remove the comment? (#${commentId})`,
					showDenyButton: true,
					confirmButtonText: 'Remove it !',
					denyButtonText: `Don't !`,
					focusDeny: true,
				}).then((result)=>{
					if(result.isConfirmed){
						let postKey, commentKey;
						postKey = Posts.posts.findIndex((post) => {
							commentKey = post.comments.findIndex((comment) => comment.id === commentId );
							if(commentKey>-1){ return true }
							else{ return false; }
						});
						let post = Posts.posts[postKey];
						let comments = post.comments;
						let comment = comments[commentKey];
						comment.isRemoving = true;
						$.ajax({
							type: "POST",
							url: '<?php echo Page('post/remove'); ?>',
							data: { commentId: commentId },
							dataType: 'json',
							success: (resp)=>{
								Loger.Log('info','Remove Comment',resp);
								let table = {
									'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
									'is_logout': 'Oh no, you are not login.',
									'comment_id_format_incorrect': 'Send the wrong data request.',
									'cannot_select': 'We got the error when sql query...',
									'access_denied': 'Sorry, you cannot do it',
									'removed_comment': `You removed the comment! (#${commentId})`,
									'unexpected': 'Un... seems has some errors, sorry.',
								}
								let config = [];
								if(Loger.Check(resp,'success')){
									// remove comment
									comments.splice(commentKey,1);
									// remove reply
									let preDelReplies = comments.filter((comment)=> comment.reply===commentId );
									let preDelReplyKey;
									preDelReplies.forEach((preDelReply)=>{
										preDelReplyKey = comments.findIndex((comment) => comment.id === preDelReply.id );
										comments.splice(preDelReplyKey,1);
									});

									config.timer = 1600;
								}
								Loger.Swal(resp,table, config);
							},
							error: (resp)=>{
								Loger.Log('error','Error Remove Comment',resp);
							}
						}).then(()=>{ post.isRemoving = false; });
					}
				});
			},
			editPost: (postId)=>{
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				let post = Posts.posts[postKey];
				let form = $('form.form#editPost_'+postId);
				let title = form.find('#title').val();
				let content = form.find('#content').val();
				form.form({
					on: 'submit',
					inline: true,
					keyboardShortcuts: false,
					// delay: 800,
					onSuccess: function(event, fields){
						if(event){ // fix: in promise, event is undefined
							event.preventDefault();
							fields.postId = postId;
							Swal.fire({
								icon: 'info',
								title: 'Sure?',
								html: `You sure wnat to save the changes?`,
								showDenyButton: true,
								confirmButtonText: 'Save the changes',
								denyButtonText: `Wait`,
								focusDeny: true,
							}).then((result)=>{
								if(result.isConfirmed){
									this.classList.add('loading');
									$.ajax({
										type: "POST",
										url: '<?php echo Page('post/edit'); ?>',
										data: fields,
										dataType: 'json',
										success: function(resp){
											Loger.Log('info','Edit Post',resp);
											let table = {
												'is_logout': 'Oh no, you are not login.',
												'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
												'title_too_short': `Title is too short!`,
												'content_too_short': 'Content is too short!',
												'access_denied': 'Permission denied!',
												'edited_post': 	'successfully edited!',
											}
											// update new post into page
											if(Loger.Have(resp,'edited_post')){
												form.form('reset');
												let newInfo = resp.find(r => r[0]==='success')[2];
												post.title = newInfo['title'];
												post.content = newInfo['content'];
												post.edited = post.edited || {};
												post.edited.times = post.edited.times+1 || 1;
												post.edited.last_time = newInfo.last_time;
												post.isEditing = false;
											}else if(Loger.Have(resp,'chnaged_nothing')){
												post.isEditing = false;
											}else{ Loger.Swal(resp, table); }
										},
									}).then(()=>{
										this.classList.remove('loading');
									});
								}
							}); // end swal
						}
						return false;
					},
					onFailure: (formErrors, fields)=>{
						if(!form.form('validate field','title')){ form.find('#title').focus(); }
						else if(!form.form('validate field','content')){ form.find('#content').focus(); }
						return false;
					},
					fields: postRule,
				});
			},
			editComment: (commentId)=>{
				let postKey, commentKey;
				postKey = Posts.posts.findIndex((post) => {
					commentKey = post.comments.findIndex((comment) => comment.id === commentId );
					if(commentKey>-1){ return true }
					else{ return false; }
				});
				let post = Posts.posts[postKey];
				let comment = post.comments[commentKey];
				let form = $('form.form#editComment_'+commentId);
				let content = form.find('#content').val();
				return false;
				// form.form({
				// 	on: 'submit',
				// 	inline: true,
				// 	keyboardShortcuts: false,
				// 	// delay: 800,
				// 	onSuccess: function(event, fields){
				// 		if(event){ // fix: in promise, event is undefined
				// 			event.preventDefault();
				// 			fields.postId = postId;
				// 			Swal.fire({
				// 				icon: 'info',
				// 				title: 'Sure?',
				// 				html: `You sure wnat to save the changes?`,
				// 				showDenyButton: true,
				// 				confirmButtonText: 'Save the changes',
				// 				denyButtonText: `Wait`,
				// 				focusDeny: true,
				// 			}).then((result)=>{
				// 				if(result.isConfirmed){
				// 					this.classList.add('loading');
				// 					$.ajax({
				// 						type: "POST",
				// 						url: '<?php echo Page('post/edit'); ?>',
				// 						data: fields,
				// 						dataType: 'json',
				// 						success: function(resp){
				// 							Loger.Log('info','Edit Post',resp);
				// 							let table = {
				// 								'is_logout': 'Oh no, you are not login.',
				// 								'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
				// 								'title_too_short': `Title is too short!`,
				// 								'content_too_short': 'Content is too short!',
				// 								'access_denied': 'Permission denied!',
				// 								'edited_post': 	'successfully edited!',
				// 							}
				// 							// update new post into page
				// 							if(Loger.Have(resp,'edited_post')){
				// 								form.form('reset');
				// 								let newInfo = resp.find(r => r[0]==='success')[2];
				// 								post.title = newInfo['title'];
				// 								post.content = newInfo['content'];
				// 								post.edited = post.edited || {};
				// 								post.edited.times = post.edited.times+1 || 1;
				// 								post.edited.last_time = newInfo.last_time;
				// 								post.isEditing = false;
				// 							}else if(Loger.Have(resp,'chnaged_nothing')){
				// 								post.isEditing = false;
				// 							}else{ Loger.Swal(resp, table); }
				// 						},
				// 					}).then(()=>{
				// 						this.classList.remove('loading');
				// 					});
				// 				}
				// 			}); // end swal
				// 		}
				// 		return false;
				// 	},
				// 	onFailure: (formErrors, fields)=>{
				// 		if(!form.form('validate field','content')){ form.find('#content').focus(); }
				// 		return false;
				// 	},
				// 	fields: commentRule,
				// });
			},
			getComments: (postId) => {
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				let post = Posts.posts[postKey];
				// dont run again
				if(post.showComments){ post.showComments = false; return false; }
				else{ post.showComments = true; }
				if(post.comments){ return false; }
				// get comments
				post.gettingComments = true;
				$.ajax({
					type: "GET",
					url: '<?php echo API('post/comment'); ?>',
					data: { postId: postId,	},
					dataType: 'json',
					success: (resp) => {
						Loger.Log('info','Comment API',resp);
						post.comments = resp;
					},
				}).then(()=>{
					post.gettingComments = false;
				});
			},
			createComment: (postId,replyTarget=false)=>{
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				let post = Posts.posts[postKey];
				let commentKey, content, comment;
				if(replyTarget){
					//reply 
					commentKey = post.comments.findIndex((comment) => comment.id === replyTarget);
					comment = post.comments[commentKey];
					content = comment.replying===undefined?'':comment.replying;
				}else{
					//comment
					content = post.commenting===undefined?'':post.commenting;
				}
				if(content === undefined || content.length < 2)
					{ Swal.fire({icon:'warning',title:'Wait',html:'You need more content.'}); return false; }
				// start to comment
				if(replyTarget){ comment.isReplying = true; }
				else{ post.isCommenting = true; }
				
				$.ajax({
					type: "POST",
					url: '<?php echo Page('post/comment'); ?>',
					data: { postId: postId, content: content, reply: (replyTarget?replyTarget:'')},
					dataType: 'json',
					success: (resp)=>{
						Loger.Log('info',(replyTarget)?'Reply':'Comment',resp);
						if(Loger.Check(resp,'success')){
							if(replyTarget){ comment.replying = ''; }
							else{ post.commenting = ''; }
							post.comments.push(resp.find(r => r[0]==='success')[2]); //unshift
						}else{
							Loger.Swal(resp,{
								'is_logout': 'Oh no, you are not login.',
								'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
								'content_too_short': `Your need to type more contnet!`,
								'type_incorrect': 'Un... seems has some errors, sorry.',
								'failed_reply': 'Sorry, seems we got the errors.',
								'commented': 'Done, you successfully commented.',
								'access_denied': 'Access denied!',
								'cannot_select': 'Something error when sql select...',
							});
						}
					},
				}).always(()=>{
					if(replyTarget){ comment.isReplying = false; }
					else{ post.isCommenting = false; }
				});
				return false;
			},
			isNotReply: (comments, commentId)=>{
				let commentKey = comments.findIndex((comment) => comment.id === commentId);
				return comments[commentKey].reply === null;
			},
			filterReplies: (comments, commentId)=>{
				let ret = comments.filter(comment => { if(comment.reply===commentId){ return comment; } });
				return ret;
			},
		},
		mounted(){
			// get posts
			$.ajax({
				type: "GET",
				url: '<?php echo API('post/post'); ?>',
				data: {skip: (this.posts).length, limit: this.postsLimit, },
				dataType: 'json',
				success: (resp) => {
					this.posts = resp;
					// this.skipPosts += this.postsLimit;
					Loger.Log('info','Post API',resp);
				},
			}).then(()=>{
				this.isLoading = false;
				// console.log
			});

			setInterval(() => {
				Posts.posts = Posts.posts;
			},1000);
		},
	}).directive('focus', {
		mounted(el) { el.focus(); }
	}).mount('div#Forum');

	let form = new Array();
	form['CreatePost'] = $('form#CreatePost');

	let postRule = {
		title: {
			identifier: 'title',
			rules: [
				{
					type	 : 'minLength[2]',
					prompt : 'Title 必須至少 2 個字元長度(中文為1字元長度)'
				}
			]
		},
		content: {
			identifier: 'content',
			rules: [
				{
					type	 : 'minLength[4]',
					prompt : 'Content 必須至少 4 個字元長度(中文為2字元長度)'
				},
			]
		}
	};

	let commentRule = {
		content: {
			identifier: 'content',
			rules: [
				{
					type	 : 'minLength[4]',
					prompt : 'Content 必須至少 4 個字元長度(中文為2字元長度)'
				},
			]
		}
	};

	// create post board
	const CreatePost = createApp({
		date(){return{
			//
		}},
		mounted(){
			
		},
	}).mount('form#CreatePost');


	form['CreatePost'].form({
		on: 'submit',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				Swal.fire({
					icon: 'info',
					title: 'Sure?',
					html: `You sure done the write, and ready to post?`,
					showDenyButton: true,
					confirmButtonText: 'Post it',
					denyButtonText: `Wait`,
					focusDeny: true,
				}).then((result)=>{
					if(result.isConfirmed){
						this.classList.add('loading');
						$.ajax({
							type: "POST",
							url: '<?php echo Page('post/create'); ?>',
							data: fields,
							dataType: 'json',
							success: function(resp){
								Loger.Log('info','Create Post',resp);
								let table = {
									'is_logout': 'Oh no, you are not login.',
									'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
									'created_post': `You created the post!`,
									'failed_create_post': 'Un... seems has some errors, sorry.',
								}
								let config = [ ];
								// update new post into page
								if(Loger.Check(resp,'success')){
									form['CreatePost'].form('reset');
									Posts.posts.unshift(resp.find(r => r[0]==='success')[2]);
									// this.skipPosts += 1;
									config.timer = 1600;
								}
								Loger.Swal(resp, table, config);

							},
						}).then(()=>{
							this.classList.remove('loading');
						});
					}
				}); // end swal
			}
			return false;
		},
		onFailure: (formErrors, fields)=>{
			if(!form['CreatePost'].form('validate field','title')){ form['CreatePost'].find('#title').focus(); }
			else if(!form['CreatePost'].form('validate field','content')){ form['CreatePost'].find('#content').focus(); }
			return false;
		},
		fields: postRule,
	});

	
</script>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
