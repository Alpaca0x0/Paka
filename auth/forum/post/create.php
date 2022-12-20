<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '尚未登入');

# needed datas
$needs = ['content'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$content = Type::string($_POST['content'], '');

# check format
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }
// 
$config = Inc::config('forum/post');
$content = preg_replace('/[\f\r\t]+/', ' ', $content);
$content = preg_replace('/\n[\s+]*\n+/', "\n", $content);
$content = trim($content);
# count chinese as one length
$PreContent = preg_replace("#[^\x{00}-\x{ff}]#u", '?', $content);
// 
if(!preg_match($config['content'], $PreContent)){ Resp::warning('content_format', '內文的格式錯誤'); }

# create the post
Inc::clas('forum');
$pid = Forum::createPost($uid, $content);
if($pid === false){ Resp::error('sql_insert', 'SQL 語法執行錯誤'); }

# return new post
$post = Forum::allFields()::isHtml()::getPost($pid);
if(!$post){ Resp::error('unexpected', '發生非預期錯誤，無法返回新發布的文章'); }

# sort

Resp::success('successfully', $post, '已成功發表貼文');
