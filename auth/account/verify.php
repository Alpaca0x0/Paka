<?php require('../../init.php'); ?>
<?php class_exists('User') or require(Local::Clas('user')); User::clear(); ?>

<?php include_once(Local::Inc('header')); ?>

<?php
	$swal = [
		'icon' => 'warning',
		'title' => 'Warning',
		'html' => 'Token format incorrect.',
		'link' => false
	];
	function setSwal($icon, $title, $html, $link=false){
		global $swal;
		$swal['icon'] = $icon;
		$swal['title'] = $title;
		$swal['html'] = $html;
		$swal['link'] = $link;
	}
	//
	if(isset($_GET['token']) && is_string($_GET['token']) && strlen(trim($_GET['token']))==64){
		setSwal(
			'error',
			'Error',
			'Sorry, We got the some errors, the account can not be verify now.'
		);

		#
		$token = trim($_GET['token']);
		
		# Start to use database
		class_exists('DB') or require(Local::Clas('db'));
		
		# Check if the token is correct
		$result = DB::query(
			'SELECT `account_event`.`expire`,
			`account`.`id`, `account`.`username`, `account`.`email`, `account`.`status` 
			FROM `account_event` 
			JOIN `account` ON (`account`.`id`=`account_event`.`account`) 
			WHERE `account_event`.`action`="register" AND `account_event`.`target`=:token AND `account`.`status`<>"removed"
			LIMIT 1;'
		)::execute([':token'=>$token]);
		
		# query error
		if($result===false){ 
			setSwal(
				'error',
				'Error',
				'Sorry, we got the error when verifying your account...'
			); 
		}
		# query success
		else{
			$row = DB::fetch('assoc');
			# not found
			if(!$row){ 
				setSwal(
					'warning',
					'Not Found',
					'We cannot found this account<br>It is probably because of timeout<br>please go to register it',
					Root::Page('account/register')
				);
			}
			# have found 
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
						Root::Page('account/login')
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

<?php include_once(Local::Inc('footer')); ?>