<?php require('init.php'); ?>

<?php
@include_once(Func('lang')); # Using the function T($id) to return text in current language
?>

<?php @include_once(Inc('header')); ?>
<?php @include_once(Inc('menu/header')); ?>

<!-- <div class="ui segment">
    <p></p>
</div> -->

<div class="ui container">
	<h2 class="content-title">Announcement</h2>
	<p>Welcome to <?php L('Project_Name'); ?>.</p>
</div>






<?php @include_once(Inc('menu/footer')); ?>
<?php @include_once(Inc('footer')); ?>
