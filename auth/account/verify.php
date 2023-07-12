<?php
Inc::component('header');

$GLOBALS['swal'] = [];
$swal = $GLOBALS['swal'];

function setSwal($icon, $html, $link=false){
	global $swal;
	$swal['icon'] = $icon;
	$swal['title'] = ucfirst($icon);
	$swal['html'] = $html;
	$swal['link'] = $link;
}

# default
setSwal('error', '很抱歉，發生了致命且非預期的錯誤！');

do{
	if(!Arr::includes($_GET, 'token')){ setSwal('warning', '需要給予 Token 以進行驗證'); break; }
	$token = trim(Type::string($_GET['token'], ''));
	if(strlen($token) !== 64){ setSwal('warning', 'Token 格式錯誤'); break; }

	# connect db
	Inc::Clas('db');
	if(!DB::connect()){ setSwal('error', '很抱歉，資料庫連接失敗！'); break; }
	# start to check token if correct
	$result = DB::query(
		'SELECT `account_event`.`expire`,
		`account`.`id`, `account`.`username`, `account`.`email`, `account`.`status` 
		FROM `account_event` 
		JOIN `account` ON (`account`.`id`=`account_event`.`uid`) 
		WHERE `account_event`.`commit`="register" AND `account_event`.`token`=:token AND `account`.`status`<>"removed"
		ORDER BY `account_event`.`id` DESC
		LIMIT 1;'
	)::execute([':token' => $token]);
	# query error
	if(DB::error()){ setSwal('error', '很抱歉，SQL 語法查詢時發生錯誤！'); break; }
	# if token is not found
	$row = DB::fetch();
	if($row === false){ 
		setSwal('warning', '找不到該用戶或 Token...<br>可能是驗證時間超時，煩請重新註冊。', Uri::page('account/register'));
		break;
	}
	# found, check status 
	$id = $row['id'];
	$username = $row['username'];
	$email = $row['email'];
	$status = trim($row['status']);
	$datetime = time();
	// 
	if($status === 'alive'){ setSwal('success', "該帳號已通過驗證，不需要再驗證囉。",Uri::page('account/login')); break; }
	if($status === 'review'){ setSwal('warning', '暫時無法驗證，該帳號目前受審查當中...'); break; }
	if($status !== 'unverified'){ setSwal('warning', '很抱歉，該帳戶暫時無法被驗證。'); break; }
	# update status of account
	# if timeout
	if($datetime > $row['expire']){
		setSwal('warning', '很抱歉，驗證時間已經超時，煩請重新註冊。', Uri::page('account/register'));
		$status = "invalid";
	}
	# successfully
	else{
		setSwal('success',"您好呀 {$username} !<br>您的 E-Mail 驗證成功，可以開始登入囉！", Uri::page('account/login'));
		$status = "alive";
	}
	# sql query
	$result = DB::query(
		'UPDATE `account` SET `status`=:status WHERE `id`=:id;'
	)::execute([':status'=>$status, ':id'=>$id]);
	# if sql error
	if(DB::error()){ setSwal('error', '很抱歉，標記帳戶驗證狀態的過程發生了非預期的錯誤。'); break; }
}while(false);

$swal = $GLOBALS['swal'];
?>

<script type="text/javascript">
	Swal.fire({
		icon: '<?=$swal['icon']?>',
		title: '<?=$swal['title']?>',
		html: '<?=$swal['html']?>',
		showConfirmButton: <?=$swal['link']?'true':'false'; ?>,
		showCancelButton: false,
		allowOutsideClick: false,
		confirmButtonText: 'Okay'
	}).then(()=>{
		window.location.replace('<?=$swal['link']?$swal['link']:'#!'?>');
	});
</script>

<?php Inc::component('footer'); ?>