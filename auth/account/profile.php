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
    // remove all space char
    $nickname = preg_replace('/\s+/', '', $nickname);
    // make chinese word length to 1
    $preNickname = preg_replace("#[^\x{00}-\x{ff}]#u", '*', $nickname);
    if($preNickname === ''){ $nickname = null; }
    else if(!preg_match($config['nickname'], $preNickname)){ Resp::warning('nickname_format', $nickname, '暱稱格式不正確'); }
}

# birthday
$birthday = [
    'string' => false,
    'all' => false,
    'year' => -1,
    'month' => -1,
    'day' => -1,
];
if(Arr::includes($_POST, 'birthday')){
    $birthday['string'] = Type::string($_POST['birthday'], '');
    $birthday['string'] = trim($birthday['string']);
    if($birthday['string'] === ''){
        // set birthday to null
        $birthday['all'] = null;
    }else{
        // convert birthday to date format
        $birthday['all'] = explode('-',$birthday['string']);
        if(count($birthday['all']) < 3){ Resp::warning('birthday_format', $birthday['string'], '無法解析日期'); }
        $birthday['year'] = Type::int($birthday['all'][0], 0);
        $birthday['month'] = Type::int($birthday['all'][1], 0);
        $birthday['day'] = Type::int($birthday['all'][2], 0);
        // if format is incorrect
        if(!checkdate($birthday['month'], $birthday['day'], $birthday['year'])){
            Resp::warning('birthday_format', $birthday['string'], '生日格式不正確');
        }
        // if range is normal
        $ageRange = $config['birthday'];
        $age = 0;
        $currentDate = Array();
        $currentDate['string'] = date('Y-m-d');
        $currentDate['all'] = explode('-',$currentDate['string']);
        if(count($currentDate['all']) < 3){ Resp::error('cannot_get_current_day', $currentDate['string'], '發生致命錯誤，伺服器無法獲取日期'); }
        $currentDate['year'] = $currentDate['all'][0];
        $currentDate['month'] = $currentDate['all'][1];
        $currentDate['day'] = $currentDate['all'][2];
        // 
        $age = $currentDate['year'] - $birthday['year'];
        if($birthday['month'] > $currentDate['month']){ $age += 1; }
        else if($birthday['month'] === $currentDate['month'] && $birthday['day'] >= $currentDate['day']){ $age += 1; }
        // 
        if($age < $ageRange[0]){ Resp::warning('too_young', $age, "本站會員需年滿 {$ageRange[0]} 歲"); }
        else if($age > $ageRange[1]){ Resp::warning('too_old', $age, "嘿，您的年齡打破世界紀錄！？"); }
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
if(is_array($birthday['all'])){ $columns['birthday'] = join('-',$birthday['all']); }
else if(is_null($birthday['all'])){ $columns['birthday'] = null; }
// 'gender' => false
// 'avatar' => $avatar

# check if datas has been changed
foreach ($columns as $key => $val) {
    if(User::get($key, false) === $val){ unset($columns[$key]); }
}count($columns)>0 or Resp::warning('nothing_happend', '資料並沒有任何更動');

# start to update profile
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

Resp::success('successfully', $columns, '資料更新成功');
