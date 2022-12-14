<?php require('../../init.php'); ?>
<?php header('Content-Type: application/json; charset=utf-8'); ?>
<?php class_exists('Roger') or require_once(Local::Clas('roger')); ?>
<?php 
    class_exists('User') or require_once(Local::Clas('user'));
    User::is('login') or Roger::warning('is_logout');
?>

<?php
# needed datas
$needed_datas = ['postId'];
foreach ($needed_datas as $data) {
    if(!isset($_POST[$data])){ Roger::push('data_missing',$data); }
} Roger::warning();

# convert
class_exists('Format') or require_once(Local::Clas('format'));
$userId = User::get('id',false);
$postId = Format::convert('int',$_POST['postId'],false);

# check format
if(!$postId){ Roger::warning('post_id_format_incorrect'); }

# check permission
class_exists('Forum') or require_once(Local::Clas('forum'));
$columns = Format::convert('object',[
    'account' => ['id']
],[]);
$poster = Forum::getPost($postId, $columns);
if($poster[0] !== true){ Roger::warning('cannot_get_post', $poster[1]); }
$poster = $poster[1]['poster'];
if($poster['id'] !== User::get('id')){ Roger::warning('permission_denied'); }

# sql
$result = Forum::removePost($postId);

if(!isset($result[1])){ Roger::error('remove_post_error'); }
else if($result[0]===true){ Roger::success('successfully'); }
else{ Roger::warning('cannot_remove_post',$result[1]); }
