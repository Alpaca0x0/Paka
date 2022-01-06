<?php @include_once('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<!-- <div class="ui segment">
    <p></p>
</div> -->

<div class="ui container">
	<h2 class="content-title">Index</h2>
	<p>Welcome to the <?php echo L('AlpacaTech'); ?>.</p>
</div>

<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
