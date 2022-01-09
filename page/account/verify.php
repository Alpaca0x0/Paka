<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function L($label) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<?php
	$html = 'The account can not be verify...';
	$icon = 'error';

	if(isset($_GET['token'])){
		$token = trim($_GET['token']);

		# Start to using database
		@include_once(Func('db'));

		# Check if the token is correct
		$sql = "
			SELECT `account_event`.`datetime`,
					`account`.`id`, `account`.`username`, `account`.`email`, `account`.`status` 
			FROM `account_event` 
			JOIN `account` ON (`account`.`id`=`account_event`.`account`) 
			WHERE `account_event`.`action`=:action AND `account_event`.`detail`=:token
			LIMIT 1;
		";
		$DB->Query($sql);
		$result = $DB->Execute([':action'=>'register', ':token'=>$token]);
		if($result===false){ $icon = 'error'; $html = 'Sorry, we got the error when verifying your account...'; }
		// check
		else{
			$row = $DB->Fetch($result,'assoc');
			if(!$row){ $icon = 'error'; $html = 'We cannot found this account, please go to register.'; }
			else{
				// catch the target
				$id = (int)$row['id'];
				$username = $row['username'];
				$email = $row['email'];
				$status = trim($row['status']);
				// check if have permission
				if($status==='review'){ $icon = 'error'; $html = 'The account is reviewing...'; }
				else if($status==='removed'){ $icon = 'error'; $html = 'The account can not be verify...'; }
				else if($status==='alive'){ $icon = 'success'; $html = "Hello ${username}, your account already verified<br>You can go to login now"; }
				else if($status==='unverified'){
					$regex = include(Conf('account/regex')); $regex = $regex['verify'];
					// timeout
					if(time()-$row['datetime'] > $regex['timeout']){
						$icon = 'error';
						$html = 'Sorry, the token is timeout...<br>Please register again :(';
						$status = "removed";
					}else{
						$icon = 'success';
						$html = "Hello ${username}, your email 「${email}」 is successfully verified.<br>You can go to login now.";
						$status = "alive";
					}
					// update, timeout or success
					$sql = "UPDATE `account` SET `status`=:status WHERE `id`=:id;";
					$DB->Query($sql);
					$result = $DB->Execute([':status'=>$status, ':id'=>$id]);
					if($result===false){ $icon = 'error'; $html = 'Sorry, we got the error when verifying your account...'; }
				}
			}
		}
	}else{
		$icon = 'error'; $html = 'Token missing';
	}
?>

<script type="text/javascript">
	Swal.fire({
		icon: '<?php echo $icon; ?>',
		showCancelButton: false,
		showConfirmButton: <?php echo $icon==='success'?'true':'false'; ?>,
		allowOutsideClick: false,
		html: '<?php echo $html; ?>',
		confirmButtonText: 'Login now'
	}).then(()=>{
		window.location.replace('<?php echo Page('account/index'); ?>');
	});
</script>


<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>