<?php
Inc::component('header');

$swal = [];
function setSwal($icon, $html, $link=false){
	global $swal;
	$swal['icon'] = $icon;
	$swal['title'] = ucfirst($icon);
	$swal['html'] = $html;
	$swal['link'] = $link;
}

# default
setSwal(
	'error',
	'很抱歉，發生了致命且非預期的錯誤！'
);

do{
	if(!Arr::includes($_GET, 'token')){ setSwal('warning', '需要有 Token 以進行驗證'); break; }
	$token = trim(Type::string($_GET['token'], ''));
	if(strlen(trim($_GET['token'])) !== 64){ setSwal('warning', 'Token 格式錯誤'); break; }

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
		LIMIT 1;'
	)::execute([':token' => $token]);
	# query error
	if(DB::error()){ setSwal('error', '很抱歉，SQL 語法查詢時發生錯誤！'); break; }
	# check if token is found
	$row = DB::fetch();
	# not found
	if($row === false){ 
		setSwal('warning', '', Uri::page('account/register'));
		break;
	}
	
}while(false);

		# found 
		else{
			$id = (int)$row['id'];
			$username = $row['username'];
			$email = $row['email'];
			$status = trim($row['status']);

			# check status
			if($status==='alive'){ 
				setSwal(
					'success',
					'Success',
					"Hello {$username}<br>Your account has already been verified.<br>You can go to login now.",
					Uri::page('account/login')
				);
			}
			else if($status==='review'){ 
				setSwal(
					'warning',
					'Warning',
					'The account is reviewing...'
				);
			}
			else if($status==='unverified'){
				# update account status
				$status = 'error';

				# timeout
				if(time() > $row['expire']){
					setSwal(
						'warning',
						'Warning',
						'Sorry, the token is timeout...<br>Please register again :('
					);
					$status = "invalid";
				}
				# successfuly
				else{
					setSwal(
						'success',
						'Successfully',
						"Hello {$username}, your email 「{$email}」 is successfully verified.<br>You can go to login now."
					);
					$status = "alive";
				}

				# sql query
				$result = DB::query(
					'UPDATE `account` SET `status`=:status WHERE `id`=:id;'
				)::execute([':status'=>$status, ':id'=>$id]);
				# if sql error
				if($result===false){ 
					setSwal(
						'error',
						'Error',
						'Sorry, we got the error when verifying your account...'
					); 
				}
			}
			else{
				setSwal(
					'warning',
					'Warning',
					'Sorry, this account has some problems, we can not verify it now.'
				);
			}
		}
	}
}
?>

<script type="text/javascript">
	Swal.fire({
		icon: '<?=$swal['icon']?>',
		title: '<?=$swal['title']?>',
		html: '<?=$swal['html']?>',
		showConfirmButton: <?=$swal['link']?'true':'false'; ?>,
		showCancelButton: false,
		allowOutsideClick: false,
		confirmButtonText: 'Got it'
	}).then(()=>{
		window.location.replace('<?=$swal['link']?$swal['link']:'#!'?>');
	});
</script>

<?php Inc::component('footer'); ?>