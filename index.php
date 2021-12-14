<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<!-- comments container -->
<div class="ui container" id="Comments">
	<h2 class="content-title">Index</h2>
	<p>Welcome to AlpacaTech.</p>

	<!-- write post form -->
	<form class="ui form" id="WritePost">
		<div class="field"><input type="text" name="title" id="title" ref="title" placeholder="Title..."></div>
		<div class="field"><textarea name="content" id="content" ref="content" placeholder="Content..."></textarea></div>
		<div class="ui blue labeled submit icon button right floated"><i class="icon edit"></i> Post it</div>
	</form><!-- end write post form -->

	<h4 class="ui horizontal divider header"><i class="tag icon"></i> 今天想說點什麼? </h4>

	<!-- post -->
	<div class="ui piled teal segment" v-for="(post, post_key) in posts" :class="{ loading: !isLoaded }">
		<!-- post title -->
		<div class="ui container">
			<div class="ui small basic icon right floated buttons">
				<button class="ui button"><i class="edit blue icon"></i></button>
				<button class="ui button" @click="removePost(post_key)"><i class="trash alternate red icon"></i></button>
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
		<button class="fluid ui button"><i class="dropdown icon"></i> 看看大家都說了些什麼 </button>
		<div class="ui container">
			<div class="content">
				<!-- comments -->
				<div class="ui comments collapsed">
					<!-- reply form -->
					<form class="ui reply form">
						<div class="two fields">
							<div class="field"><input type="text"></div>
							<div class="field"><div class="ui blue labeled submit icon button"><i class="icon edit"></i> Add Reply</div></div>
						</div>
					</form><!-- end reply form -->
					<!-- comment -->
					<div class="comment">
						<a class="avatar"><img src="https://semantic-ui.com/images/avatar/small/christian.jpg"></a>
						<div class="content">
							<a class="author">警察</a>
							<div class="metadata"><span class="date">2 天前</span></div>
							<div class="text">這裏是葛飾區龜有公園前派出所 </div>
							<div class="actions"><a class="reply">Reply</a></div>
						</div>
						<!-- replies -->
						<div class="comments">
							<!-- reply of replies -->
							<div class="comment">
								<a class="avatar"><img src="https://semantic-ui.com/images/avatar/small/elliot.jpg"></a>
								<div class="content">
									<a class="author">蔡依婷</a>
									<div class="metadata"><span class="date">1 天前</span></div>
									<div class="text">No, it wont</div>
									<div class="actions"><a class="reply">Reply</a></div>
								</div>
							</div><!-- end reply of replies -->
						</div><!-- end replies -->
					</div><!-- end comment -->
				</div><!-- end comments -->
			</div><!-- end content -->
		</div><!-- end post comment -->
	</div><!-- enc post -->
</div><!-- end container -->

<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	const Posts = createApp({
		data(){return{
			posts: [{
				id: '',
				title: '',
				content: '',
				poster: '',
				datetime: '',
			}],
			isLoaded: false,
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
			removePost: (key) => {
				// let postId = Posts.posts[key].id;
				// use postId better
				Swal.fire({
					icon: 'warning',
					title: 'Sure?',
					html: 'You sure want to remove the post?',
					showDenyButton: true,
					confirmButtonText: 'Remove it !',
					denyButtonText: `Don't !`,
				}).then((result)=>{
					if(result.isConfirmed){
						$.ajax({
							type: "POST",
							url: '<?php echo Page('post/remove'); ?>',
							date: { postId: postId },
							dataType: 'json',
							success: (resp)=>{
								Swal.fire({
									icon: 'success',
									title: 'Done',
									html: `You removed the post! (${postId})`,
								});
								Posts.posts.splice(key,1);
							},
						});
					}
				});
			}
		},
		mounted(){
			$.ajax({
				type: "POST",
				url: '<?php echo API('post'); ?>',
				data: {},
				dataType: 'json',
				success: (resp) => {
					this.$data.posts = resp;
					console.log(resp);
				},
			}).then(()=>{
				this.isLoaded = true;
			});
					
		},
	}).mount('div#Comments');

	let form = new Array();
	form['WritePost'] = $('form#WritePost');

	const WritePost = createApp({
		date(){return{
			//
		}},
		mounted(){
			
		},
	}).mount('form#WritePost');




	form['WritePost'].form({
		on: 'blur',
		inline: true,
		keyboardShortcuts: false,
		// delay: 800,
		onSuccess: function(event, fields){
			if(event){ // fix: in promise, event is undefined
				event.preventDefault();

				this.classList.add('loading');

				$.ajax({
					type: "POST",
					url: '<?php echo Page('post/create'); ?>',
					data: fields,
					dataType: 'json',
					success: function(resp){
						Loger.Log('info','Response',resp);
						// check if success
						// let isSuccess = Loger.Check(resp,'success');
						// let swal_config = isSuccess ? { timer:2000, } : {};
						// Loger.Swal(resp, tables['login'], swal_config).then((val)=>{
						// 	if(isSuccess){ window.location.replace('<?php echo ROOT; ?>'); }
						// 	// update the UI status
						// 	// it will call back to the onSuccess()
						// 	form['WritePost'].form('validate form'); // fix: in promise, event is undefined
						// });
					},
				}).then(()=>{
					this.classList.remove('loading');
				});
			}
			return false;
		},
		onFailure: function(formErrors, fields){
			if(!form['WritePost'].form('validate field','title')){ this.$refs.title.focus(); }
			else if(!form['WritePost'].form('validate field','content')){ this.$refs.content.focus(); }
			return false;
		},
		fields: {
			title: {
				identifier: 'title',
				rules: [
					{
						type	 : 'minLength[2]',
						prompt : 'Title 必須至少 4 個字'
					}
				]
			},
			content: {
				identifier: 'content',
				rules: [
					{
						type	 : 'minLength[4]',
						prompt : 'Content 必須至少 4 個字'
					},
				]
			}
		}
	});

	
</script>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
