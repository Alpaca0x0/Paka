<?php include_once('init.php'); ?>

<?php
@include_once(Func('user'));
$User->Update();
if($User->Get('identity',false)!=='admin'){ header("Location:".ROOT); die('Permission Denied.'); }
?>
<?php @include_once(Func('admin')); ?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<div class="ui container" id="Users">
	<div class="ui inverted segment">
		<div class="ui four statistics">
			<div class="ui grey inverted statistic">
				<div class="value"><i class="icon users"></i> {{ Count.total }}</div>
				<div class="label">總會員數</div>
			</div>
			<div class="ui red inverted statistic">
				<div class="value"><i class="icon gavel"></i> {{ Count.admin }}</div>
				<div class="label">管理員</div>
			</div>
			<div class="ui green inverted statistic">
				<div class="value"><i class="icon user"></i> {{ Count.member }}</div>
				<div class="label">成員</div>
			</div>
			<div class="ui yellow inverted statistic">
				<div class="value"><i class="icon gem"></i> {{ Count.vip }}</div>
				<div class="label">VIP</div>
			</div>
		</div>
	</div>

	<div class="ui container" :class="isLoading?'segment loading':false" style="overflow-x: auto">
		<table class="ui celled green table unstackable">
			<thead>
				<tr>
					<th class="one wide">#</th>
					<th class="one wide">Identity</th>
					<th class="three wide">Username</th>
					<th class="five wide">E-Mail</th>
					<th class="two wide">RegisterTime</th>
					<th class="one wide">Status</th>
				</tr>
			</thead>
			<tbody>
				<!-- positive, negative -->
				<!-- checkmark, close -->
				<tr v-for="User in Users">
					<td>{{ User.id }}</td>
					<td>{{ identities[User.identity] || User.identity }}</td>
					<td>
						<img class="ui avatar image" :src="User.avatar==null?'<?php echo IMG('default','png'); ?>':'data:image/jpeg;base64, '+User.avatar">
						<span>{{ User.username }}</span>
					</td>
					<td>{{ User.email }}</td>
					<td @mouseenter="" @mouseout="">{{ User.register_time }}</td>
					<td>{{ statuses[User.status] || User.status }}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="ui divider"></div>

	<div class="ui right floated pagination menu">
		<a class="icon item">
			<i class="left chevron icon"></i>
		</a>
		<a class="item">1</a>
		<a class="item">2</a>
		<a class="item">3</a>
		<a class="item">4</a>
		<a class="icon item">
			<i class="right chevron icon"></i>
		</a>
	</div>
</div>


<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	const Users = createApp({
		data(){return{
			isLoading: true,
			statuses: {
				'alive': '存活',
				'removed': '已刪除',
			},
			identities: {
				'admin': '管理員',
				'member': '會員',
				'vip': 'VIP',
			},
			Count: <?php echo json_encode($Admin->Count()); ?>,
			Users: {},
			
		}},
		methods:{
			timeToStatus: window.timeToStatus,
			timeToString: window.timeToString,
		},
		mounted(){
			// get posts
			$.ajax({
				type: "GET",
				url: '<?php echo API('admin/users'); ?>',
				data: {order: 'DESC', limit: 99, after:0 },
				dataType: 'json',
				success: (resp) => {
					this.Users = resp;
					// this.skipPosts += this.postsLimit;
					Loger.Log('info','Users API',resp);
				},
			}).then(()=>{
				this.isLoading = false;
			});

			// this.Users.forEach((User)=>{
			// 	let temp = User.register_time;
			// 	User.register_time = {
			// 		time: temp,
			// 		status: timeToStatus(User.register_time),
			// 		string: timeToString(User.register_time),
			// 		display: '-'
			// 	};
			// 	User.register_time.display = User.register_time.status;
			// });
		}
	}).mount('div#Users');
</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
