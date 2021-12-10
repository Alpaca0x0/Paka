<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<!-- <div class="ui segment">
    <p></p>
</div> -->

<div class="ui container" id="app">
	<h2 class="content-title">Index</h2>
	<input :type="isFocus?'input':'button'" value="testing text" @focus="isFocus=true" @blur="isFocus=false">
</div>

<script type="module">
	import { createApp } from '<?php echo Frame('vue/vue','js'); ?>';
	createApp({
		data(){return{
			isFocus: false,
		}},
	}).mount('#app');
</script>



<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
