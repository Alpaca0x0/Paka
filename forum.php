<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<!-- comments container -->
<div class="ui container" id="Comments">
	<h2 class="content-title">Forum</h2>

	<!-- write post form -->
	<form class="ui form" id="CreatePost" ref='CreatePost'>
		<div class="field"><input type="text" name="title" id="title" ref='title' placeholder="Title..."></div>
		<div class="field"><textarea name="content" id="content" ref='content' placeholder="Content..."></textarea></div>
		<div class="ui blue labeled submit icon button right floated"><i class="icon edit"></i> Post it</div>
	</form><!-- end write post form -->

	<h4 class="ui horizontal divider header"><i class="tag icon"></i> 今天想說點什麼? </h4>

	<!-- post -->
	<div class="ui piled teal segment" v-for="(post, post_key) in posts" :class="{ 'loading': isLoading, 'loading':post.isRemoving }">
		<!-- post title -->
		<div class="ui container">
			<!-- edit, remove -->
			<div class="ui small basic icon right floated buttons" v-if="post.poster.id=='<?php echo $User->Get('id','-'); ?>'">
				<button class="ui button"><i class="edit blue icon"></i></button>
				<button class="ui button" @click="removePost(post.id)"><i class="trash alternate red icon"></i></button>
			</div>

			<div class="center aligned author">
				<img class="ui avatar image" src="https://semantic-ui.com/images/avatar/small/jenny.jpg"> {{ post.poster.username }} ({{ timeToStatus(post.datetime) }})
			</div>
			
			<h2 class="ui left aligned header"> {{ post.title }}</h2>
		</div>
		<!-- post content -->
		<p>{{ post.content }}</p>
		<div class="ui right aligned container"><h5 class="ui grey header">(#{{ post.id }}) {{ timeToString(post.datetime) }}</h5></div>
		<!-- post comment -->
		<button class="fluid ui button" @click="getComments(post.id)" :class="{loading : post.gettingComments}" :disabled="post.gettingComments"><i class="eye icon" :class="{ slash : post.showComments }"></i> {{ post.showComments ? "隱藏留言區" : "看看大家說了什麼" }} </button>
		<div class="ui container">
			<div class="content">
				<!-- comments -->
				<div class="ui comments" :class="{ collapsed: !post.showComments }">
					<!-- comment form -->
					<form class="ui reply form" @submit="comment(post.id);" onsubmit="return false;">
						<div class="ui action input">
							<input type="text" placeholder="Comment..." v-model="post.commenting" :disabled="post.isCommenting">
							<button class="ui icon blue button" :disabled="post.isCommenting"><i class="icon" :class="post.isCommenting?'spinner loading':'edit'"></i>&nbsp;{{post.isCommenting?"Wait...":"Comment"}}</button>
						</div>
					</form><!-- end comment form -->
					<!-- comment -->
					
					<template v-if="post.gettingComments">
						<br><a class="ui label black"><i class="spinner loading icon"></i>&nbsp;Loading...</a>
					</template>
					<a v-if="(post.comments) && !(post.comments).length" class="ui teal pointing large label">Be a first one!</a>
					<template v-if="post.comments" v-for="comment in post.comments">
						<div class="comment" v-if="isReplyToPost(post.comments,comment.id)">
							<a class="avatar"><img src="https://semantic-ui.com/images/avatar/small/christian.jpg"></a>
							<div class="content">
								<a class="author">{{ comment.commenter.username }}</a>
								<div class="metadata"><span class="date">{{ timeToStatus(comment.datetime) }} ({{timeToString(comment.datetime)}})</span></div>
								<div class="text">{{ comment.content }} </div>
								<div class="actions"><a class="reply" @click="post.replyTartget=comment.id">Reply</a></div>
								<!-- reply form -->
								<form class="ui reply form" @submit="reply('post',post.id);" onsubmit="return false;" :class="{ loading : post.isReplying }" v-if="post.replyTartget==comment.id">
									<div class="ui mini action icon input">
										<input type="text" placeholder="Reply..." v-model="comment.replying">
										<button class="ui icon button"><i class="edit icon"></i>Reply</button>
									</div>
								</form>
								<!-- end reply form -->
							</div>
							<!-- replies -->
							<div class="comments">
								<!-- reply of replies -->
								<div class="comment" v-for="reply in filterReplies(post.comments, comment.id)">
									<a class="avatar"><img src="https://semantic-ui.com/images/avatar/small/elliot.jpg"></a>
									<div class="content">
										<a class="author">{{ reply.commenter.username }}</a>
										<div class="metadata"><span class="date">{{ timeToStatus(reply.datetime) }} ({{timeToString(reply.datetime)}})</span></div>
										<div class="text">{{ reply.content }}</div>
										<!-- <div class="actions"><a class="reply">Reply</a></div> -->
									</div>
								</div><!-- end reply of replies -->
							</div><!-- end replies -->
						</div><!-- end comment -->
					</template>
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
			posts:[],
			isLoading: true,
			skip: 0,
			limit: 16,
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
				return `${years}/${months}/${days} ${hours}:${minutes}:${seconds}`;
			},
			timeToStatus: (datetime)=>{
				let t = new Date(datetime*1000);
				let ct = new Date();
				let result = (ct - t)/1000;
				let ret = "-", unit='-';

				let i = 60, h = i*60, d = h*24, w = d*7;
				if(result < i){
					// just
					ret = ''; unit='剛剛';
				}
				else if(result < h){
					// minutes
					unit='分鐘前'; ret=result/i;
				}
				else if(result < d){
					// hours
					unit='小時前'; ret=result/h;
				}
				else if(result < w){
					// days
					unit='天前'; ret=result/d;
				}
				else{
					// days
					unit='周前'; ret=result/w;
				}
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
						Posts.posts[postKey].isRemoving = true;
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
									'no_access': 'Sorry, you cannot do it',
									'removed_post': `You removed the post! (#${postId})`,
									'failed_remove_post': 'Un... seems has some errors, sorry.',
								}
								Loger.Swal(resp,table);
								if(Loger.Check(resp,'success')){
									Posts.posts.splice(postKey,1);
									Posts.skip -= 1;
								}
							},
							error: (resp)=>{
								Loger.Log('error','Error Remove Post',resp);
							}
						}).then(()=>{ Posts.posts[postKey].isRemoving = false; });
					}
				});
			},
			getComments: (postId) => {
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				// done run again
				if(Posts.posts[postKey].showComments){ Posts.posts[postKey].showComments = false; return false; }
				else{ Posts.posts[postKey].showComments = true; }
				if(Posts.posts[postKey].comments){ return false; }
				// get comments
				Posts.posts[postKey].gettingComments = true;
				$.ajax({
					type: "GET",
					url: '<?php echo API('post/comment'); ?>',
					data: { postId: postId,  },
					dataType: 'json',
					success: (resp) => {
						Loger.Log('info','Comment API',resp);
						Posts.posts[postKey].comments = resp;
					},
				}).then(()=>{
					Posts.posts[postKey].gettingComments = false;
				});
			},
			comment: (postId)=>{
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				let content = Posts.posts[postKey].commenting===undefined?'':Posts.posts[postKey].commenting;
				if(content === undefined || content.length < 2){ Swal.fire({icon:'warning',title:'Wait',html:'You need more content.'});return false; }
				// start to comment
				Posts.posts[postKey].isCommenting = true;
				$.ajax({
					type: "POST",
					url: '<?php echo Page('post/comment'); ?>',
					data: { postId: postId, content: content, reply: ''},
					dataType: 'json',
					success: (resp)=>{
						Loger.Log('info','Reply',resp);
						let postKey = Posts.posts.findIndex(((post) => post.id === postId));
						let table = {
							'is_logout': 'Oh no, you are not login.',
							'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
							'content_too_short': `Your need to type more contnet!`,
							'type_incorrect': 'Un... seems has some errors, sorry.',
							'failed_reply': 'Sorry, seems we got the errors.',
							'replied': 'Done, you successfuly replied.',
						}
						if(Loger.Check(resp,'success')){
							Posts.posts[postKey].commenting = '';
							Posts.posts[postKey].comments.unshift(resp.find(r => r[0]==='success')[2]);
						}else{ Loger.Swal(resp,table); }
					},
				}).then(()=>{ Posts.posts[postKey].isCommenting = false; });
				return false;
			},
			// reply: (type, postId)=>{
			// 	let postKey = Posts.posts.findIndex(((post) => post.id === postId));
			// 	let content = Posts.posts[postKey].commenting===undefined?'':Posts.posts[postKey].commenting;
			// 	if(content === undefined || content.length < 2){ Swal.fire({icon:'warning',title:'Wait',html:'You need more content.'});return false; }
			// 	Posts.posts[postKey].isCommenting = true;
			// 	$.ajax({
			// 		type: "POST",
			// 		url: '<?php echo Page('post/reply'); ?>',
					// data: { postId: postId, content: content, reply: ''},
			// 		dataType: 'json',
			// 		success: (resp)=>{
			// 			Loger.Log('info','Reply',resp);
			// 			let postKey = Posts.posts.findIndex(((post) => post.id === postId));
			// 			let table = {
			// 				'is_logout': 'Oh no, you are not login.',
			// 				'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
			// 				'content_too_short': `Your need to type more contnet!`,
			// 				'type_incorrect': 'Un... seems has some errors, sorry.',
			// 				'failed_reply': 'Sorry, seems we got the errors.',
			// 				'replied': 'Done, you successfuly replied.',
			// 			}
			// 			if(Loger.Check(resp,'success')){
			// 				Posts.posts[postKey].commenting = '';
			// 				Posts.posts[postKey].comments.unshift(resp.find(r => r[0]==='success')[2]);
			// 			}else{ Loger.Swal(resp,table); }
			// 		},
			// 	}).then(()=>{ Posts.posts[postKey].isCommenting = false; });
			// 	return false;
			// },
			isReplyToPost: (comments, commentId)=>{
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
				data: {skip: this.skip, limit: this.limit, },
				dataType: 'json',
				success: (resp) => {
					this.posts = resp;
					this.skip += this.limit;
					Loger.Log('info','Post API',resp);
				},
			}).then(()=>{
				this.isLoading = false;
				// console.log
			});
					
		},
	}).mount('div#Comments');

	let form = new Array();
	form['CreatePost'] = $('form#CreatePost');

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
								// update new post into page
								if(Loger.Check(resp,'success')){
									form['CreatePost'].form('reset');
									Posts.posts.unshift(resp.find(r => r[0]==='success')[2]);
									this.skip += 1;
								}
								Loger.Swal(resp, table);

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
		fields: {
			title: {
				identifier: 'title',
				rules: [
					{
						type	 : 'minLength[2]',
						prompt : 'Title 必須至少 4 個字元長度(中文為2字元長度)'
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
		}
	});

	
</script>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
