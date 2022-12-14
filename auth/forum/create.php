<?php require('../../init.php'); ?>
<?php header('Content-Type: application/json; charset=utf-8'); ?>
<?php class_exists('Roger') or require_once(Local::Clas('roger')); ?>
<?php 
    class_exists('User') or require_once(Local::Clas('user'));
    User::is('login') or Roger::warning('is_logout');
?>

<?php
# needed datas
$needed_datas = ['title','content','columns'];
foreach ($needed_datas as $data) {
    if(!isset($_POST[$data])){ Roger::push('data_missing',$data); }
} Roger::warning();

# convert
class_exists('Format') or require_once(Local::Clas('format'));
$userId = User::get('id',0);
$title = Format::convert('string',$_POST['title'],null);
$content = Format::convert('string',$_POST['content'],null);
$columns = Format::convert('object',$_POST['columns'],[]);

# convert format
$title = preg_replace('/[\n\r\t]/', ' ', trim($title)); 
$content = preg_replace('/[\n\r\t]/', ' ', trim($content));
# remove multiple spaces
$title = preg_replace('/\s(?=\s)/', '', $title);
$content = preg_replace('/\s(?=\s)/', '', $content);

# check
if($userId < 1){ Roger::error('error'); }
$rules = require(Local::configig('post'));
if(mb_strlen($title) < $rules['title']['min']){ Roger::push('title_too_short'); }
else if(mb_strlen($title) > $rules['title']['max']){ Roger::push('title_too_long'); }
if(mb_strlen($content) < $rules['content']['min']){ Roger::push('content_too_short'); }
else if(mb_strlen($content) > $rules['content']['max']){ Roger::push('content_too_long'); }
Roger::warning();

# sql
class_exists('Forum') or require_once(Local::Clas('forum'));
$result = Forum::createPost($userId, $title, $content, $columns);
if(!is_array($result)){ Roger::error('error'); }
if($result[0] !== true){ Roger::warning($result[1]); }
else{ Roger::success($result[1]); }
