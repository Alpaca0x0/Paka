window.Loger = new Array();

// all of response types
Loger.Types = ["unknown", "error", "warning", "success", "message",];

// tables
Loger.Tables = new Array();
// response display table
Loger.Tables['Display'] = new Array(); // sweet alert 2
	// Example
	// {"content":"display",}

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
	let contents=Object.keys(tables), first=tables.length-1;
	let title=false, text=false, icon=false, confirmButtonText=false, timer=false, timerProcessBar=false;

	logs.forEach(function(log,key,arr){
		have = contents.indexOf(log[1]);
		if(have>-1){ first = (have<first)?have:first; }
	});

	// Swal.fire({
	// 	title: tables
	// });
}