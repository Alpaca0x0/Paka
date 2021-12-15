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
	<div class="ui piled teal segment" v-for="(post, post_key) in posts" :class="{ 'loading': !isLoaded, }">
		<!-- post title -->
		<div class="ui container">
			<div class="ui small basic icon right floated buttons">
				<button class="ui button"><i class="edit blue icon"></i></button>
				<button class="ui button" @click="removePost(post.id)"><i class="trash alternate red icon"></i></button>
			</div>

			<div class="center aligned author">
				<img class="ui avatar image" src="https://semantic-ui.com/images/avatar/small/jenny.jpg"> {{ post.poster_username }}
			</div>
			<h5 class="ui grey header">(#{{ post.id }}) {{ timeToString(post.datetime) }}</h5>
			<h2 class="ui left aligned header"> {{ post.title }}</h2>
		</div>
		<!-- post content -->
		<p>{{ post.content }}</p>
		<!-- <div class="ui right aligned container"><h5 class="ui grey header">{{ timeToString(post.datetime) }}</h5></div> -->
		<!-- post comment -->
		<button class="fluid ui button" @click="getReply(post.id)"><i class="dropdown icon"></i> 看看大家都說了些什麼 </button>
		<div class="ui container">
			<div class="content">
				<!-- comments -->
				<div class="ui comments" :class="{ collapsed: !post.showReply }">
					<!-- reply form -->
					<form class="ui reply form" @submit="reply('post',post.id);" onsubmit="return false;">
						<div class="two fields">
							<div class="field"><input type="text" v-model="post.reply"></div>
							<div class="field"><div class="ui blue labeled submit icon button" @click="reply('post',post.id);"><i class="icon edit"></i> Add Reply</div></div>
						</div>
					</form><!-- end reply form -->
					<!-- comment -->
					<template v-if="post.replies" v-for="reply in post.replies">
						<div class="comment" v-if="isReplyToPost(post.replies,reply.id)">
							<a class="avatar"><img src="https://semantic-ui.com/images/avatar/small/christian.jpg"></a>
							<div class="content">
								<a class="author">{{ reply.replier_username }}</a>
								<div class="metadata"><span class="date">? 天前</span></div>
								<div class="text">{{ reply.content }} </div>
								<div class="actions"><a class="reply">Reply</a></div>
							</div>
							<!-- replies -->
							<div class="comments">
								<!-- reply of replies -->
								<div class="comment" v-for="secondReply in getAllSecondReply(post.replies, reply.id)">
									<a class="avatar"><img src="https://semantic-ui.com/images/avatar/small/elliot.jpg"></a>
									<div class="content">
										<a class="author">{{ secondReply.replier_username }}</a>
										<div class="metadata"><span class="date">? 天前</span></div>
										<div class="text">{{ secondReply.content }}</div>
										<div class="actions"><a class="reply">Reply</a></div>
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
			// [{
			// 	id: '',
			// 	title: '',
			// 	content: '',
			// 	poster: '',
			// 	datetime: '',
			//  showReply: false,
			//  replies: []
			// }],
			isLoaded: false,
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
						$.ajax({
							type: "POST",
							url: '<?php echo Page('post/remove'); ?>',
							data: { postId: postId },
							dataType: 'json',
							success: (resp)=>{
								Loger.Log('info','Remove Post',resp);
								let postKey = Posts.posts.findIndex(((post) => post.id === postId));
								let table = {
									'is_logout': 'Oh no, you are not login.',
									'data_missing': 'Sorry, we lose the some datas. <br>Please refresh the site and try again.',
									'removed_post': `You removed the post! ${postId}`,
									'failed_remove_post': 'Un... seems has some errors, sorry.',
								}
								Loger.Swal(resp,table);
								if(Loger.Check(resp,'success')){
									Posts.posts.splice(postKey,1);
									Posts.skip -= 1;
								}
							},
						});
					}
				});
			},
			getReply: (postId) => {
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				// done run again
				if(Posts.posts[postKey].showReply){ Posts.posts[postKey].showReply = false; return false; }
				else{ Posts.posts[postKey].showReply = true; }
				if(Posts.posts[postKey].replies){ return false; }
				// get replies
				$.ajax({
					type: "GET",
					url: '<?php echo API('post/reply'); ?>',
					data: { postId: postId,  },
					dataType: 'json',
					success: (resp) => {
						Loger.Log('info','Reply API',resp);
						Posts.posts[postKey].replies = resp;
					},
				}).then(()=>{
					// this.isLoaded = true;
					// console.log
				});
			},
			reply: (type, postId)=>{
				let postKey = Posts.posts.findIndex(((post) => post.id === postId));
				let content = Posts.posts[postKey].reply===undefined?'':Posts.posts[postKey].reply;
				if(content === undefined || content.length < 2){ Swal.fire({icon:'warning',title:'Wait',html:'You need more content.'});return false; }
				$.ajax({
					type: "POST",
					url: '<?php echo Page('post/reply'); ?>',
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
						Loger.Swal(resp,table);
						if(Loger.Check(resp,'success')){
							Posts.posts[postKey].reply = '';
							Posts.posts[postKey].replies.unshift(resp.find(r => r[0]==='success')[2]);
						}
					},
				});
				return false;
			},
			isReplyToPost: (replies, replyId)=>{
				let replyKey = replies.findIndex(((reply) => reply.id === replyId));
				return replies[replyKey].reply === null;
			},
			getAllSecondReply: (replies, replyId)=>{
				let ret = replies.filter(reply => { if(reply.reply===replyId){ return reply; } });
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
				this.isLoaded = true;
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

				this.classList.add('loading');

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
					}else{
						this.classList.remove('loading');
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
