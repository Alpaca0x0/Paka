
// function Notify(){}

window.Notify = new Array();

Notify.UK = function(type, msg, pos='bottom-right', time=5000){
	let allows = new Array();
	allows['type'] = ['primary', 'success', 'warning', 'danger'];
	allows['pos'] = ['top-left', 'top-center', 'top-right', 'bottom-left', 'bottom-center', 'bottom-right'];

	let icon = "info";
	if(type=='success'){ icon='check'; }
	else if(type=='warning'){ icon='warning'; }
	else if(type=='danger'){ icon='ban'; }
	msg = `<span uk-icon="icon: ${icon}"></span>&nbsp;${msg}`;

	UIkit.notification({
	    message: msg,
	    status: type,
	    pos: pos,
	    timeout: time
	});
}

Notify.Clear = function(){ UIkit.notification.closeAll(); }