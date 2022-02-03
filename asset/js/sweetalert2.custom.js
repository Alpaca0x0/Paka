window.Swalc = new Array();

Swalc.loading = (title='Waiting...', html='Please wait for the process to complete.')=>{
	return Swal.mixin({
		title: title,
		html: html,
		timerProgressBar: true,
		showCancelButton: false,
		showConfirmButton: false,
		allowOutsideClick: false,
		didOpen: () => { Swal.showLoading(); },
	});
}