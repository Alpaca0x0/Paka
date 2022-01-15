<?php @include_once('../../init.php'); ?>

<?php
header('Content-Type: application/json; charset=utf-8');
@include_once(Func('post'));
@include_once(Func('loger'));
?>

<?php
// check
$needed_datas = ['commentId',];
foreach ($needed_datas as $data){
    if( !isset($_GET[$data]) || !is_string($_GET[$data]) ){
        $Loger->Push('warning','data_missing',$data);
    }
} if($Loger->Check()){ $Loger->Resp(); }

// get datas
$commentId = (int)(@$_GET['commentId']);

// filter
if($commentId < 1){ $Loger->Push('warning','post_id_incorrect',$commentId); }
// if($skip<0 || $skip>2147483647){ $skip = 0; }
// if($limit<1 || $limit>99){ $limit = 8; }
if($Loger->Check()){ $Loger->Resp(); }

// query
$ret = $Post->Get('reply+',$commentId,[0,99]);
if(!$ret){ $ret = []; }
else if(!is_array($ret)){ $ret = [$ret]; }
foreach ($ret as $key => $val) {
	$tmep = $ret[$key]['commenter'];
	$ret[$key]['commenter'] = Array();
	$ret[$key]['commenter']['id'] = $tmep;
	$ret[$key]['commenter']['username'] = $ret[$key]['commenter_username'];
	$ret[$key]['commenter']['identity'] = $ret[$key]['commenter_identity'];
	$ret[$key]['commenter']['nickname'] = $ret[$key]['profile_nickname'];
	$ret[$key]['commenter']['gender'] = $ret[$key]['profile_gender'];
	$ret[$key]['commenter']['birthday'] = $ret[$key]['profile_birthday'];
	$ret[$key]['commenter']['avatar'] = $ret[$key]['profile_avatar'];
	if(!is_null($ret[$key]['commenter']['avatar'])){ $ret[$key]['commenter']['avatar'] = base64_encode($ret[$key]['commenter']['avatar']); }

	unset(
		$ret[$key]['commenter_username'],
		$ret[$key]['commenter_identity'],
		$ret[$key]['profile_nickname'],
		$ret[$key]['profile_gender'],
		$ret[$key]['profile_birthday'],
		$ret[$key]['profile_avatar'],
	);
}
echo json_encode($ret);
