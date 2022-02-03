<?php require('../../init.php'); ?>
<?php header('Content-Type: application/javascript'); ?>


window.setCookie = function(name,value,minutes=60*7){
    var expires = "";
    if(minutes){
        var date = new Date();
        date.setTime(date.getTime() + minutes*60*1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=<?php echo ROOT; ?>";
}

window.getCookie = function (name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

window.delCookie = function(name) {   
    document.cookie = name +'=; Path=<?php echo ROOT; ?>; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

window.timeToString = function(datetime){
    let t = new Date(datetime*1000);
    let years = t.getFullYear().toString();
    let months = (t.getMonth() + 1).toString();
    let days = t.getDate();
    let hours = t.getHours();
    let minutes = t.getMinutes();
    let seconds = t.getSeconds();
    if (months<10) { months = "0"+months; }
    if (days<10) { days = "0"+days; }
    if (hours<10) { hours = "0"+hours; }
    if (minutes<10) { minutes = "0"+minutes; }
    if (seconds<10) { seconds = "0"+seconds; }
    return `${years}/${months}/${days} ${hours}:${minutes}:${seconds}`;
}

window.timeToStatus = function(datetime){
    let t = new Date(datetime*1000);
    let ct = new Date();
    let result = (ct - t)/1000;
    let ret = "-", unit='-';

    let i=60, h=i*60, d=h*24, w=d*7, m=30*d, y=365*d;
    // just
    if(result < i){ ret = ''; unit='Just'; }
    // minutes
    else if(result < h){ unit='Minutes age'; ret=result/i; }
    // hours
    else if(result < d){ unit='Hours ago'; ret=result/h; }
    // days
    else if(result < w){ unit='Days ago'; ret=result/d; }
    // weeks
    else if(result < m){ unit='Weeks ago'; ret=result/w; }
    // months
    else if(result < y/2){ unit='Months ago'; ret=result/m; }
    // half year
    else if(result < y){ unit='Half year ago'; ret=''; }
    // years
    else{ unit='Years ago'; ret=result/y; }
    //
    ret = ret!=''?parseInt(ret, 10):'';
    return (`${ret} ${unit}`).trim();
}