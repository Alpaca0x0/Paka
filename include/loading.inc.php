<?php defined('INIT') or die('NO INIT'); ?>

<div class="ui active dimmer" id="Loader"><div class="ui text loader">Loading...</div></div>
<script type="text/javascript">
	// all dom onload
	document.addEventListener("DOMContentLoaded", function(event) { 
		document.querySelector('div#Loader').classList.remove('active');
	});
</script>