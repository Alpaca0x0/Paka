<?php if (!isset($_SESSION)){ session_start(); } ?>
<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Resp('warning','is_logout'); }
@include_once(Func('image'));
?>

<?php

# If have post data
$needed_datas = ['nickname','gender','birthday'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) ){
        $Loger->Resp('warning','data_missing',$data);
        break;
    }
}
if(!isset($_FILES['avatar'])){ 
    $Loger->Resp('warning','data_missing','avatar');
}


# Catch Datas
$nickname = trim($_POST['nickname']); $nickname = preg_replace('/\s(?=\s)/', '', $nickname);
$gender = strtolower(trim($_POST['gender']));
$birthday = Array();
$birthday['origin'] = trim($_POST['birthday']);
$birthday['full'] = strtotime($birthday['origin']);
if(empty($_FILES["avatar"]["name"]) || !file_exists($_FILES['avatar']['tmp_name']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])){
    $avatar = false;
}else{ $avatar = $_FILES['avatar']; $Image->Image($avatar); }

# Check
$regex = @include_once(Conf('account')); // Setting Rules
// nickname
if(!preg_match($regex['nickname'], $nickname)){ $Loger->Push('warning','nickname_format_not_match'); }
// gender
if(!in_array($gender, $regex['gender'])){ $Loger->Push('warning','gender_format_not_match'); }
// avatar
if($avatar){
    if(!in_array($Image->Get('mime'), $regex['avatar']['formats']) || $Image->Get('width') !== $Image->Get('height') || $Image->Get('width')!=$regex['avatar']['width']){
        // $Loger->Push('warning','avatar_format_not_match'); 
        $avatar = false;
    }else{ $avatar = $Image->Get('blob'); }
}

if($birthday['full']){
    // check if date is real
    $birthday['years'] = date('Y', $birthday['full']);
    $birthday['months'] = date('m', $birthday['full']);
    $birthday['days'] = date('d', $birthday['full']);
    if(!checkdate($birthday['months'], $birthday['days'], $birthday['years'])){ $Loger->Push('warning','birthday_format_not_match'); }
}
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Transform
if($nickname==''){ $nickname = null; }
if(!$birthday['full']){ $birthday['origin'] = null; } // does not set the birthday, set it null

# Start to using database
@include_once(Func('db'));
$id = $User->Get('id',0); $id = (int)$id;
# Check if the user is not exist

if(!$avatar){
    $DB->Query("UPDATE `profile` SET `nickname`=:nickname,`gender`=:gender, `birthday`=:birthday WHERE `id`=:id");
    $result = $DB->Execute([':id'=>$id, ':nickname'=>$nickname, ':gender'=>$gender, ':birthday'=>$birthday['origin'], ]);
}else{
    $DB->Query("UPDATE `profile` SET `nickname`=:nickname,`gender`=:gender, `birthday`=:birthday, `avatar`=:avatar WHERE `id`=:id");
    $result = $DB->Execute([':id'=>$id, ':nickname'=>$nickname, ':gender'=>$gender, ':birthday'=>$birthday['origin'], ':avatar'=>$avatar, ]);
}
if(!$result){ $Loger->Push('error','db_cannot_update'); $Loger->Resp(); }

# response
$Loger->Push('success','update_successfully');
$Loger->Resp(); 

