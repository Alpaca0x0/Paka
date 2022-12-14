<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
if(!User::isLogin()){ Resp::warning('is_logout','當前尚未登入任何帳戶，可能是 Token 過期了，請重新登入'); }

# Get and filter datas
$config = Inc::config('account');

#nickname
$nickname = false;
if(Arr::includes($_POST, 'nickname')){
    $nickname = Type::string($_POST['nickname'], false);
}
if($nickname !== false){
    $nickname = preg_replace('/\s+/', '', $nickname);
    if($nickname === ''){ $nickname = null; }
    else if(!preg_match($config['nickname'], $nickname)){ Resp::warning('nickname_format', $nickname, '暱稱格式不正確'); }
}

# birthday
$birthday = [
    'origin' => false,
    'full' => false,
    'years' => -1,
    'months' => -1,
    'days' => -1,
];
if(Arr::includes($_POST, 'birthday')){
    $birthday['origin'] = Type::string($_POST['birthday'], '');
    $birthday['origin'] = trim($birthday['origin']);
    if($birthday['origin'] === ''){
        // set birthday to null
        $birthday['full'] = null;
    }else{
        // convert birthday to date format
        $birthday['full'] = strtotime($birthday['origin']);
        if($birthday['full']){
            $birthday['years'] = date('Y', $birthday['full']);
            $birthday['months'] = date('m', $birthday['full']);
            $birthday['days'] = date('d', $birthday['full']);
        }
        // if format is incorrect
        if(!$birthday['full'] || !checkdate($birthday['months'], $birthday['days'], $birthday['years'])){
            Resp::warning('birthday_format', $birthday['origin'], '生日格式不正確');
            $birthday['full'] = false;
        }
    }
}

#avatar
// class_exists('Image') or require_once(Local::Clas('image'));
// if(isset($_FILES['avatar'])){
//     if(empty($_FILES["avatar"]["name"]) || 
//     !file_exists($_FILES['avatar']['tmp_name']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])){
//         $avatar = false;
//     }else{ 
//         $avatar = $_FILES['avatar'];
//         Image::image($avatar);
//         //
//         if(!in_array(Image::get('mime'), $config['avatar']['formats']) || 
//         Image::get('width') !== Image::get('height') || Image::get('width') != $config['avatar']['width']){
//             Roger::warning('avatar_format_not_match');
//             $avatar = false;
//         }else{ $avatar = Image::get('blob'); }
//     }
// }else{ $avatar = false; }


#
Inc::clas('db');
DB::connect() or Resp::error('database_cannot_connect', '資料庫連接失敗');

$id = User::get('id',false);
$id or Resp::error('cannot_get_user_id','發生致命錯誤，無法獲取當前用戶的資訊');

$columns = [];
if($nickname !== false){ $columns['nickname'] = $nickname; }
if($birthday['full'] !== false){ $columns['birthday'] = $birthday['full']; }
// 'gender' => false
// 'avatar' => $avatar
count($columns)>0 or Resp::warning('nothing_happend', '資料並沒有任何更動');

$sql = "UPDATE `profile` SET ";
$sqlSet = "";
$sqlValue = [':id' => $id];
foreach ($columns as $column => $value){
    $sqlSet .= "`$column`=:$column,";
    $sqlValue[":$column"] = $value;
} 
$sqlSet = trim($sqlSet,',');
if($sqlSet=='' || count($sqlValue)<1){ Resp::error('cannot_build_sql_sentence', '發生致命錯誤，無法創建 SQL 語法'); }
#
$sql .= $sqlSet . " WHERE `id`=:id";
$result = DB::query($sql)::execute($sqlValue);
if(DB::error()){ Resp::error('sql_cannot_update', 'SQL 語法執行失敗'); }

# return new datas
unset($columns['avatar']);
# birthday
if($columns['birthday'] && !is_null($columns['birthday'])){
    $columns['birthday'] = date('Y-m-d', $columns['birthday']);
}

Resp::success('successfully', $columns, '資料更新成功');
