window.User = new Array();

User.Logout = function(url,token,callback={}){
	if(!$){ console.log('Needed include JQuery.'); return false; }
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'json',
		data: { token: token, },
		success: function(resp){ if(callback.success){ callback.success(resp); } },
		error: function(resp){ if(callback.error){ callback.error(resp); } },
		complete: function(resp){ if(callback.complete){ callback.complete(resp); } },
	});
}