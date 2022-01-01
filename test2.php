<?php @include_once('init.php'); ?>
<?php @include_once(Inc('header')); ?>


<!-- 
<script src="https://unpkg.com/cropperjs"></script>
<link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
 -->


<script type="text/javascript" src="<?php echo JS('cropper.min'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo CSS('cropper.min'); ?>">

<!-- <img class="ui medium circular image" src="user.png"> -->

<form action="" method="POST" enctype="multipart/form-data">
	<input id="author" type="file" name="image" accept="image/*" style="display: none;">
	<label for='author'>
		<a class="ui medium image" style="cursor:pointer;">
			<div class="ui small circular rotate left reveal image">
					<img id="authorCurrent" src="<?php echo IMG('default','png'); ?>" style="background-color: black;" class="visible content">
					<img src="<?php echo IMG('default','png'); ?>" class="hidden content">
			</div>
		</a>
	</label>
	<input type="submit" value="submit">
</form>


<div class="ui modal">
	<i class="close icon"></i>
	<div class="header">Profile Picture</div>
	<div class="image content">
		<div class="ui medium image" style="width: 50%">
			<img id="authorFull" class="ui fluid image">
		</div>
		<div id="authorPreview" class="ui circular image" style="overflow: hidden; width: 200px; height: 200px"></div>
	</div>

	<div class="actions">
		<div class="ui black deny button">Cancel</div>
		<div class="ui positive right labeled icon button">Crop<i class="checkmark icon"></i></div>
	</div>
</div>




<script type="text/javascript">

	let author = document.querySelector('input#author');
	let authorFull = document.querySelector('img#authorFull');
	let cropper = new Cropper(authorFull);
	let authorNow; // blob
	
	$('input#author').on('change', function(){
		console.log($(this).val());
		cropper.destroy();
		let image = author.files[0];
		if(!image){ return; }
		authorFull.src = URL.createObjectURL(image);
		cropper = new Cropper(authorFull,{
			viewMode: 2,
			aspectRatio: 4/4,
			preview: '#authorPreview,#authorCurrent',
		});
		$('.ui.modal').modal({
			// closable: false,
			onDeny: ()=>{
				let file = new File([authorNow], "",{type:image.type, lastModified:new Date().getTime()});
				let container = new DataTransfer();
				container.items.add(file);
				author.files = container.files;
			},
			onHide: ()=>{
				let file = new File([authorNow], "",{type:image.type, lastModified:new Date().getTime()});
				let container = new DataTransfer();
				container.items.add(file);
				author.files = container.files;
			},
			onApprove: ()=>{
				cropper.getCroppedCanvas({
					width: 320,
					height: 320,
				}).toBlob(function(blob){
					authorNow = blob;
					authorCurrent.src = URL.createObjectURL(blob);
					let file = new File([blob], image.name,{type:image.type, lastModified:new Date().getTime()});
					let container = new DataTransfer();
					container.items.add(file);
					author.files = container.files;
				},image.type,0.8);
			}
		}).modal('show');
	});
</script>

<hr>



<?php

$iniMaxSize = ini_get('upload_max_filesize');
$maxSize = 1024*1024*5; // 5mb

if(isset($_FILES['image']) && $_FILES['image']['tmp_name']!==""){
	$image = $_FILES['image'];
	$imageByte = @filesize($image['tmp_name']);
	$imageSize = @getimagesize($image['tmp_name']);
	$imageMime = @$imageSize['mime'];
	//
	var_dump($_FILES['image']);
	if($imageByte > $maxSize){ die('File is too big, must be smaller then '.$maxSize); }
	if(!$imageSize){ die('Not a image or file is too big, must be smaller then '.$iniMaxSize); }
	if(!in_array($imageMime, ['image/jpeg', 'image/png',])){ die('<br>this format is not allow'); }
	if($imageSize[0] !== $imageSize[1] || $imageSize[0]!=320){ die('Must be square'); }
	//
	$imageBin = file_get_contents($image['tmp_name']);
	$imageBase64 = base64_encode($imageBin);
	echo '<img src="data:image/png;base64, '.$imageBase64.'">';
}else{ echo "no"; }
// explode(',', $imgBase64)[1]; // 去除前綴

?>





<?php @include_once(Inc('footer')); ?>
