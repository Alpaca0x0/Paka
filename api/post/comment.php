<?php @include_once('../../init.php'); ?>

<?php
header('Content-Type: application/json; charset=utf-8');
@include_once(Func('post'));
@include_once(Func('loger'));
?>

<?php
// check
$needed_datas = ['postId',];
foreach ($needed_datas as $data){
    if( !isset($_GET[$data]) ){
        $Loger->Push('warning','data_missing',$data);
    }
} if($Loger->Check()){ $Loger->Resp(); }

// get datas
$postId = (int)(@$_GET['postId']);

// filter
if($postId < 1){ $Loger->Push('warning','post_id_incorrect',$postId); }
// if($skip<0 || $skip>2147483647){ $skip = 0; }
// if($limit<1 || $limit>99){ $limit = 8; }
if($Loger->Check()){ $Loger->Resp(); }

// query
$ret = $Post->Get('comment',$postId,[0,99]);
if(!$ret){ $ret = []; }
else if(!is_array($ret)){ $ret = [$ret]; }
foreach ($ret as $key => $val) {
	$tmep = $ret[$key]['commenter'];
	$ret[$key]['commenter'] = Array();
	$ret[$key]['commenter']['id'] = $tmep;
	$ret[$key]['commenter']['username'] = $ret[$key]['commenter_username'];
	$ret[$key]['commenter']['identity'] = $ret[$key]['commenter_identity'];

	unset($ret[$key]['commenter_username'], $ret[$key]['commenter_identity']);
}
echo json_encode($ret);