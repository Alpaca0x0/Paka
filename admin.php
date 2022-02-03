<?php require('init.php'); ?>

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
		<!-- <div class="ui grid">
			<div class="four wide column">
				<button class="ui button black statistic" :class="{active:filter.current.status.includes('alive')}" @click="filter.status('alive').toggle()">
					<div class="ui pink inverted statistic">
						<div class="value"><i class="icon mini heartbeat"></i> {{ Count.alive }}</div>
						<div class="label">存活</div>
					</div>
				</button>
			</div>
			<div class="four wide column"></div>
			<div class="four wide column"></div>
			<div class="four wide column"></div>
		</div>
		<br> -->
		<div class="ui four mini statistics">
			<button class="ui compact button mini black statistic" :class="{active:filter.isEmpty()}" @click="filter.init()">
				<div class="ui grey inverted statistic">
					<div class="value"><i class="icon users"></i> {{ Count.total }}</div>
					<div class="label">全部</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.identity.includes('admin')}" @click="filter.identity('admin').toggle()">
				<div class="ui red inverted statistic">
					<div class="value"><i class="icon gavel"></i> {{ Count.admin }}</div>
					<div class="label">管理員</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.identity.includes('member')}" @click="filter.identity('member').toggle()">
				<div class="ui green inverted statistic">
					<div class="value"><i class="icon user"></i> {{ Count.member }}</div>
					<div class="label">會員</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.identity.includes('vip')}" @click="filter.identity('vip').toggle()">
				<div class="ui yellow inverted statistic">
					<div class="value"><i class="icon gem"></i> {{ Count.vip }}</div>
					<div class="label">VIP</div>
				</div>
			</button>
		</div>
		<br>
		<div class="ui five mini statistics">
			<button class="ui compact button mini black statistic" :class="{active:filter.current.status.includes('alive')}" @click="filter.status('alive').toggle()">
				<div class="ui pink inverted statistic">
					<div class="value"><i class="icon heartbeat"></i> {{ Count.alive }}</div>
					<div class="label">存活</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.status.includes('unverified')}" @click="filter.status('unverified').toggle()">
				<div class="ui grey inverted statistic">
					<div class="value"><i class="icon heart outline"></i> {{ Count.unverified }}</div>
					<div class="label">未驗證</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.status.includes('review')}" @click="filter.status('review').toggle()">
				<div class="ui orange inverted statistic">
					<div class="value"><i class="icon eye"></i> {{ Count.review }}</div>
					<div class="label">審查</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.status.includes('invalid')}" @click="filter.status('invalid').toggle()">
				<div class="ui red inverted statistic">
					<div class="value"><i class="icon lock"></i> {{ Count.invalid }}</div>
					<div class="label">無效</div>
				</div>
			</button>
			<button class="ui compact button mini black statistic" :class="{active:filter.current.status.includes('removed')}" @click="filter.status('removed').toggle()">
				<div class="ui red inverted statistic">
					<div class="value"><i class="icon ban"></i> {{ Count.removed }}</div>
					<div class="label">移除</div>
				</div>
			</button>
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
				<template v-for="(User,User_key) in Show.Users">
					<template v-if="User_key >= Show.page.rows_number*(Show.page.current_number-1) && User_key < Show.page.rows_number*(Show.page.current_number-1)+Show.page.rows_number">
						<tr style="white-space: nowrap; overflow:scroll;">
							<td>{{ User.id }}</td>
							<td>{{ identities[User.identity] || User.identity }}</td>
							<td>
								<div>
									<img class="ui avatar image" :src="User.avatar==null?'<?php echo IMG('default','png'); ?>':'data:image/jpeg;base64, '+User.avatar">
									<span>{{ User.username }}</span>
								</div>
							</td>
							<td>{{ User.email }}</td>
							<td :title="timeToString(User.register_time)">{{ timeToStatus(User.register_time) }}</td>
							<td>{{ statuses[User.status] || User.status }}</td>
						</tr>
					</template>
				</template>
			</tbody>
		</table>
	</div>

	<div class="ui divider"></div>

	<div class="seven ui buttons black">
		<button class="ui button" @click="Show.page.current_number-1<1?false:Show.page.current_number-=1"><i class="left chevron icon"></i></button>
		<div class="five ui buttons" style="overflow-x: scroll;">
			<template v-for="page_number in Show.page.number">
				<button class="ui button" @click="Show.page.current_number=page_number">{{ page_number }}</button>
			</template>
		</div>
		<button class="ui button" @click="Show.page.current_number+1>Show.page.number?false:Show.page.current_number+=1"><i class="right chevron icon"></i></button>
	</div>

</div>


<script type="module">
	import { createApp, ref, reactive, onMounted, watch } from '<?php echo Frame('vue/vue','js'); ?>';
	const Users = createApp({
		setup(){
			let timeToStatus = window.timeToStatus;
			let timeToString = window.timeToString;

			let isLoading = ref(true);
			let statuses = {
				'alive': '存活',
				'removed': '已刪除',
				'unverified': '未驗證',
			};
			let identities = {
				'admin': '管理員',
				'member': '會員',
				'vip': 'VIP',
			};

			let filter = reactive({
				current: {},
				temp: {},
				init: ()=>{
					filter.current = {
						status: [],
						identity: [],
					};
					filter.reset();
				},
				reset: ()=>{
					filter.temp = {
						status: [],
						identity: [],
					}
					return filter;
				},
				status: (values)=>{
					if(!Array.isArray(values)){ values = [values]; }
					(filter.temp.status).push(...values);
					filter.temp.status = (filter.temp.status).filter((item, key, arr) => arr.indexOf(item) === key )
					return filter;
				},
				identity: (values)=>{
					if(!Array.isArray(values)){ values = [values]; }
					(filter.temp.identity).push(...values);
					filter.temp.identity = (filter.temp.identity).filter((item, key, arr) => arr.indexOf(item) === key )
					return filter;
				},
				add: ()=>{
					(filter.current.status).push(...filter.temp.status);
					(filter.current.identity).push(...filter.temp.identity);
					//
					Object.values(filter.current).forEach((current,key,arr)=>{
						arr[key] = (arr[key]).filter((item, key, arr) => arr.indexOf(item) === key );
					});
					filter.reset();
					return filter;
				},
				remove: (all=false)=>{
					Object.keys(filter.current).forEach(type => {
						filter.current[type] = (filter.current[type]).filter(val => {
							return !(filter.temp[type]).includes(val);
						} );
					});
					filter.reset();
					return filter;
				},
				toggle: ()=>{
					Object.keys(filter.temp).forEach(type=>{
						filter.temp[type].forEach(val=>{
							let index = filter.current[type].indexOf(val);
							if(index>-1){ filter.current[type].splice(index,1); }
							else{ filter.current[type].push(val); }
						})
					});
					filter.reset();
					return filter;
				},
				isEmpty: ()=>{
					return Object.values(filter.current).every(val => val.length<1 );
				},
				filter: (users)=>{
					if(filter.isEmpty()){ return users; }
					//
					return (users).filter(user => {
						// user = JSON.parse(JSON.stringify(user));
						return ((filter.current.identity).includes(user.identity)||(filter.current.identity).length<1) && 
						((filter.current.status).includes(user.status)||(filter.current.status).length<1);
					});
				},
			});
			filter.init();

			let Count = <?php echo json_encode($Admin->Count()); ?>;
			let Users = [];

			let Show = reactive({
				page: {
					rows_number: 9, // number of colums in one page
					number: 1, // page number
					current_number: 1, // current page number
				},
				Users: [],
			});

			watch(filter,()=>{
				Show.Users = filter.filter(Users);
				Show.page.number = Math.ceil(Show.Users.length/Show.page.rows_number);
			});

			onMounted(()=>{
				$.ajax({
					type: "GET",
					url: '<?php echo API('admin/users'); ?>',
					data: {order: 'DESC', limit: 99, after:0 },
					dataType: 'json',
					success: (resp) => {
						Array.prototype.push.apply(Users, resp);
						Loger.Log('info','Users API',resp);
					},
				}).then(()=>{
					filter.status('alive').add();
					isLoading.value = false;
				});
			});

			return {
				isLoading, statuses, identities, Count, timeToString, timeToStatus, filter, Show
			};
		},
	}).mount('div#Users');
</script>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
