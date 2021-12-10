<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
# Setting Rules
@include_once(Conf('account/register_format')); // $regex

# If have post data
$needed_datas = ['username','password','email',];
$have_datas = true;
foreach ($needed_datas as $val){ if(!isset($_POST[$val])){ $have_datas = false; break; } }
if ($have_datas) {
    # Catch Datas
    $username = @trim($_POST['username']);
    $password = @$_POST['password'];
    $email = @trim($_POST['email']);
    // $gender = (int)trim($_POST['gender']);
    // $username = 'alpaca';
    // $password = 'passw0rd';
    // $email = 'alpaca0x0@gmail.com';

    # Check
    if(!preg_match($regex['email'], $email)){ $Loger->Push('warning','email_format_not_match'); }
    if(!preg_match($regex['username'], $username)){ $Loger->Push('warning','username_format_not_match'); }
    if(!preg_match($regex['password'], $password)){ $Loger->Push('warning','password_format_not_match'); }
    if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

    # Transform
    $password = sha1($password);

    # Start to using database
    @include_once(Func('db'));
    # Check the user is not exist
    $result = $DB->Query(
        "SELECT `username`,`email` FROM `account` WHERE `username`=:username OR `email`=:email;",
        [':username'=>$username, ':email'=>$email]
    );
    $row = $DB->FetchAll($result,'assoc');
    if(in_array($username, array_column($row,'username'))){ $Loger->Push('warning','username_exist'); }
    if(in_array($email, array_column($row,'email'))){ $Loger->Push('warning','email_exist'); }
    if($Loger->Check()){ $Loger->Resp(); } // exist

    # Write into Database
    $result = $DB->Query(
        "INSERT INTO `account`(`username`,`password`,`email`) VALUES(:username,:password,:email);",
        [':username'=>$username, ':password'=>$password, ':email'=>$email]
    );

    # Check Result
    if(!$result){ $Loger->Push('error','db_cannot_insert'); $Loger->Resp(); }

    $Loger->Push('success','db_insert_successfully');
    $Loger->Resp(); // Success
} unset($have_datas);

?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('nav')); ?>

<div class="uk-container">
    <form class="uk-form-stacked" id="Register">
        <div class="uk-grid-small" uk-grid>
            <legend class="uk-legend">Register</legend>

            <div class="uk-margin">
                <label class="uk-form-label" for="form-stacked-text">Username</label>
                <div class="uk-form-controls uk-inline">
                    <span class="uk-form-icon" uk-icon="icon: user"></span>
                    <input name="username" id="username" class="uk-input" type="text" placeholder="Username...">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="form-stacked-text">Password</label>
                <div class="uk-form-controls uk-inline">
                    <span class="uk-form-icon" uk-icon="icon: lock"></span>
                    <input name="password" id="password" class="uk-input" type="password" placeholder="Password..." autocomplete="new-password">
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="form-stacked-text">E-Mail</label>
                <div class="uk-form-controls uk-inline">
                    <span class="uk-form-icon" uk-icon="icon: lock"></span>
                    <input name="email" id="email" class="uk-input" type="email" placeholder="E-Mail...">
                </div>
            </div>

            <!-- <br>
            <hr class="uk-divider-vertical"> -->

            <div class="uk-margin">
                <label class="uk-form-label" for="form-stacked-select">Gender</label>
                <div class="uk-form-controls">
                    <select class="uk-select" name="gender" id="gender">
                        <option value="1">Male</option>
                        <option value="2">Female</option>
                        <option value="0">(I don't want to answer)</option>
                    </select>
                </div>
            </div>

            <!-- <div class="uk-margin">
                <div class="uk-form-label">Where did you know us? (Option)</div>
                <div class="uk-form-controls">
                    <label><input class="uk-checkbox" type="checkbox"> Search by myself</label>
                    <label><input class="uk-checkbox" type="checkbox"> My friends</label>
                    <label><input class="uk-checkbox" type="checkbox"> My families</label>
                    <label><input class="uk-checkbox" type="checkbox"> Othere</label>
                </div>
            </div> -->
        </div>
<!-- 
        <legend class="uk-legend">Tell us if you got the some suggestions. (Option)</legend>
        
        <div class="uk-margin">
            <textarea name="message" class="uk-textarea" rows="5" placeholder="Textarea"></textarea>
        </div>
 -->
        <button class="uk-button uk-button-secondary uk-width-1-1" type="submit">Submit</button>
    </form>
</div>

<script type="text/javascript">//$('html').css('background-image', 'url(<?php echo IMG('register/cover','jpg'); ?>)');</script>


<script type="text/javascript" src="<?php echo JS('loger'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('notification'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('sweetalert2'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('jquery/jquery-ui-1.13.0/jquery-ui.min'); ?>"></script>

<script type="text/javascript">
    var form = $('form#Register');
    form.find('input').prop('autocomplete','off');
    Loger.Types = '<?php echo $Loger->Types('json'); ?>';
    Notify.UK('primary','Welcome!','top-center',1000);

    form.submit(function(e){
        // let columns = $('form#Register input,select,button');
        // columns.prop('disabled',true);
        $('body').loading();
        let url = "#";
        Notify.Clear();
        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            success: function(resp){ try{
                Loger.Log('info','Register Response',resp.trim());
                // Notify the response
                Loger.Display(resp,{
                    'email_format_not_match' : "<b>Email</b> format incorrect.",
                    'username_format_not_match' : "<b>Username</b> format incorrect.",
                    'password_format_not_match' : "<b>Password</b> format incorrect.",
                    'db_insert_successfully' : "Register successfully!",
                    'db_cannot_insert' : "Have an error when creating datas of your account into database!",
                    'username_exist' : "Sorry, this username is exist!",
                    'email_exist' : "Sorry, this email has been used!",
                });
                // Input box status
                form.find('input').removeClass('uk-form-danger');
                if(Loger.Check(resp,'email_format_not_match')){ form.find('#email').switchClass('uk-form-success','uk-form-danger').focus(); }
                if(Loger.Check(resp,'email_exist')){ form.find('#email').switchClass('uk-form-success','uk-form-danger').focus(); block['email']=form.find('#email').val(); }
                if(Loger.Check(resp,'password_format_not_match')){ form.find('#password').switchClass('uk-form-success','uk-form-danger').focus(); }
                if(Loger.Check(resp,'username_format_not_match')){ form.find('#username').switchClass('uk-form-success','uk-form-danger').focus(); }
                if(Loger.Check(resp,'username_exist')){ form.find('#username').switchClass('uk-form-success','uk-form-danger').focus(); block['username']=form.find('#username').val(); }
                // Analyze response
                let title, text, icon, confirmButtonText='Ok!', timer=false;
                if(Loger.Check(resp,['email_format_not_match','username_format_not_match','password_format_not_match'])){
                    title = 'Warning!';
                    text = 'Seems like some <b>formats</b> of data are <b>incorrect</b>. <br>Please check your information.';
                    icon = 'warning';
                    confirmButtonText = 'Got it!';
                }else if(Loger.Check(resp,'db_insert_successfully')){
                    // form.find('input').switchClass('uk-form-danger','uk-form-success');
                    title = 'Successfully!';
                    text = '<b>Great!</b> Welcome to join us.';
                    icon = 'success';
                    confirmButtonText = 'Cool!';
                    timer = 3000;
                    timerProgressBar = true;
                }else if(Loger.Check(resp,'username_exist')){
                    form.find('#username').switchClass('uk-form-success','uk-form-danger').focus();
                    title = 'Sorry!';
                    text = 'This <b>username</b> is exist in database. <br>Please change it to another one.';
                    icon = 'warning';
                    confirmButtonText = 'Got it!';
                }else if(Loger.Check(resp,'email_exist')){
                    form.find('#email').switchClass('uk-form-success','uk-form-danger').focus();
                    title = 'Sorry!';
                    text = 'This <b>email</b> has been used. <br>Please change it to another one.';
                    icon = 'warning';
                    confirmButtonText = 'Got it!';
                }else if(Loger.Check(resp,'db_cannot_insert')){
                    form.find('input').switchClass('uk-form-success','uk-form-danger').focus();
                    title = 'Oops!';
                    text = 'You got a serious error. When <b>insert</b> datas into <b>database</b>, some errors have been made. <br>We are sorry about that... <br>Please report to the administrator of site.';
                    icon = 'error';
                    confirmButtonText = 'So sad...';
                }else{
                    title = 'Error!';
                    text = 'Seems like you got the some <b>unexpected errors</b>. <br>We are sorry about that... <br>Please report to the administrator of site.';
                    icon = 'error';
                    confirmButtonText = 'So sad...';
                }
                Swal.fire({
                    title: title,
                    html: text,
                    icon: icon,
                    confirmButtonText: confirmButtonText,
                    timer: timer,
                    timerProgressBar: true,
                }).then(function(){
                    if(Loger.Check(resp,'db_insert_successfully')){ location.href='<?php echo Page('account/login'); ?>'; }
                });
            }catch(err){
                Loger.Log('error','Successfully got Response but Unexpected Error',err);
                Swal.fire({
                    title: "Unexpected Error!",
                    html: "Successfully got Response but Unexpected Error, this is a serious error. <br>Please report to the administrator of site.",
                    icon: 'error',
                    confirmButtonText: 'Got it!'
                });
            } },
            error: function(resp){
                Loger.Log('error','Error In Register Response',resp);
                Swal.fire({
                    title: "Unexpected Error!",
                    html: "Got a unexpected error, this is a serious error. <br>Please report to the administrator of site.",
                    icon: 'error',
                    confirmButtonText: 'Got it!'
                });
                return false;
            },
        }).always(function(){
            $('body').loading('stop');
        });
        return false;
    });

    // Regex of Inputs
    window.regex = new Array(); // regex[Input_Nmae]
    regex['email'] = <?php echo $regex['email']; ?>;
    regex['username'] = <?php echo $regex['username']; ?>;
    regex['password'] = <?php echo $regex['password']; ?>;
    window.block = new Array(); // when response the info has been blocked
    block['username'] = '';
    block['email'] = '';

    // Automatically check input-datas when keyup
    var Timer_Register_Input = setTimeout(function(){},0); // Init
    function Call_Timer_Register_Input_Status(obj){
        window.Timer_Register_Input = setTimeout(function(){
            Register_Input_Status(obj);            
        },1600);
    }

    // Check the input-data if match regular
    function Check_Register_Form_Regular(val,regular){
        if(val.match(regular)){ return true; }
        else{ return false; }
    }

    function Register_Input_Status(obj){
        if(obj.val()==''){ return false; }
        let item = obj.attr('name');
        if(obj.val()==window.block[item]){ obj.switchClass('uk-form-success','uk-form-danger'); return false; }
        if(Check_Register_Form_Regular(obj.val(),regex[item])){ obj.switchClass('uk-form-danger','uk-form-success'); }
        else{ obj.switchClass('uk-form-success','uk-form-danger'); }
    }

    // Event while input-box on-input
    form.find('input').on('input',function(e){
        $(this).removeClass('uk-form-success uk-form-danger');
        clearTimeout(Timer_Register_Input);
        Call_Timer_Register_Input_Status($(this));
    });

    // Event while input-box is losing focus
    form.find('input').change(function(){
        Register_Input_Status($(this));
    });
</script>

<!-- <a class="uk-button uk-button-default" href="#modal-overflow" uk-toggle>Open</a> -->
<script type="text/javascript">//UIkit.modal.alert('UIkit confirm!');</script>

<?php
@include_once(Inc('footer'));
