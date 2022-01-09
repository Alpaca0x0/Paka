<?php @include_once('../../init.php'); ?>

function setCookie(name,value,minutes=60*7){
    var expires = "";
    if(minutes){
        var date = new Date();
        date.setTime(date.getTime() + minutes*60*1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=<?php echo ROOT; ?>";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function delCookie(name) {   
    document.cookie = name +'=; Path=<?php echo ROOT; ?>; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}