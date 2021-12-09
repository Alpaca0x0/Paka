window.A = new Array();

A.setCookie = function(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

A.getCookie = function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

A.qsa = function(target){
	eles = document.querySelectorAll(target)
	eles.forEach((ele) => {
	//
		ele.attr = ele.getAttribute;
		ele.hasAttr = ele.hasAttribute;
		ele.setAttr = ele.setAttribute;
		ele.removeAttr = ele.removeAttribute;
	//
	});
	return eles;
}

A.qs = function(target){
	let ele = document.querySelector(target);
	//
	ele.attr = ele.getAttribute;
	ele.hasAttr = ele.hasAttribute;
	ele.setAttr = ele.setAttribute;
	ele.removeAttr = ele.removeAttribute;
	// ele.listen = $.listen(ele);
	// ele.prototype.prop
	//
	return ele;
}

A.listen = new Array();
A.listen.attr = function(ele,callback){
	let observer = new MutationObserver(callback);
	observer.observe(ele, {attributes:true});
	return observer;
}


