window.Loger = new Array();

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

Loger.Display = function(logs, tables=Loger.Tables['Display']){
	let contents=Object.keys(tables), dispalys=Object.values(tables), type, first=contents.length-1;
	let title=false, text=false, icon=false, confirmButtonText=false, timer=false, timerProcessBar=false;

	logs.forEach(function(log,key,arr){
		have = contents.indexOf(log[1]);
		if(have>-1 && have<first){
			first = have;
			type = log[0];
		}
	});

	if(first<0){ return false; }
	let current = logs[first];
	if(['error','unknown'].includes(type)){ type = 'error'; }
	else if(['warning'].includes(type)){ type = 'warning'; }
	else if(['success'].includes(type)){ type = 'success'; }
	else if(['message'].includes(type)){ type = 'info'; }
	else{ type = 'info'; }

	Swal.fire({
		icon: type,
		title: type,
		html: dispalys[first],
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