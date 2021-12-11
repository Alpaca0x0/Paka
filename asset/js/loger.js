window.Loger = new Array();

// sweetalert

// all of response types
Loger.Types = ["unknown", "error", "warning", "success", "message",];

// tables
Loger.Tables = new Array();
// response display table
Loger.Tables['Display'] = new Array(); // sweet alert 2
	// Example
	// { "content": "display", }

// check if have the type of log in logs
Loger.Check = function(logs=[], types=['unknown','error','warning']){
	if(!Array.isArray(logs)){ logs = [logs]; }
	if(!Array.isArray(types)){ types = [types]; }
	return types.some(type => {
		return logs.map(log => log[0]).includes(type);
	});
}

// check if have the content of log in logs
Loger.Have = function(logs=[], contents=[]){
	if(!Array.isArray(logs)){ logs = [logs]; }
	if(!Array.isArray(contents)){ contents = [contents]; }
	return contents.some(content => {
		return logs.map(log => log[1]).includes(content);
	});
}

Loger.Swal = function(logs, tables=[],custom={}){
return new Promise((resolve, reject) => {
	let contents=Object.keys(tables), dispalys=Object.values(tables);
	let config = {
		icon: 'info',
		title: 'info',
		html: false,
		icon: false,
		timer: false,
		timerProgressBar: true,
		// confirmButtonText: 'Got it',
	}

	// get the content of tables which is the smallest key and exist in logs
	// return: first = smallest key OR contents.length
	let first=contents.length, have;
	logs.forEach(function(log,key,arr){
		have = contents.indexOf(log[1]);
		if(have>-1 && have<first){
			first = have;
			config.icon = log[0];
		}
	});

	// not found any key, set it to be unexpected error
	if(first>=contents.length){
		custom.type = 'unknown';
		custom.html = 'Unexpected response';
	}else{
		config.html = dispalys[first];
	}

	// transform format for sweetalert
	let current = logs[first];
	if(['error','unknown'].includes(config.icon)){ config.icon = 'error'; }
	else if(['warning'].includes(config.icon)){ config.icon = 'warning'; }
	else if(['success'].includes(config.icon)){ config.icon = 'success'; }
	else if(['message'].includes(config.icon)){ config.icon = 'info'; }
	else{ config.icon = 'info'; }

	config.title = config.icon;

	// custom configuration
	Object.keys(custom).forEach((val)=>{
		config[val] = custom[val];
	});

	// display
	Swal.fire(config).then(function(value){
		resolve(value);
	});
});
}

// custom the log function
Loger.Log = function(type='log',title=false,msg=false){
	if(!msg){ msg=title; title="Log"; }
	start = '╭── '+title+'\n'; end = '\n'+'╰──────── ';
	if(type=='log'){ console.log(start,msg,end); }
	else if(type=='info'){ console.info(start,msg,end); }
	else if(type=='warning'){ console.warn(start,msg,end); }
	else if(type=='error'){ console.error(start,msg,end); }
	else { console.error('Err '+start,msg,end); }
}