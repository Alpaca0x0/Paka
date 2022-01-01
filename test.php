<?php @include_once('init.php'); ?>

<script type="text/javascript" src="<?php echo Frame('jquery/jquery@3.6.0.min','js'); ?>"></script>
<script type="text/javascript" src="<?php echo JS('cropper.min'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo CSS('cropper.min'); ?>">

<p>author show</p>
<div style="width: 300px; height: 300px;">
	<img id="authorShow" src="" style="max-width: 100%;">
</div>

<p>author preview</p>
<div id="authorPreview" style="overflow: hidden; width: 200px; height: 200px"></div>


<form action="" method="POST" enctype="multipart/form-data" onsubmit="submitForm(); return false;">
	<input id="author" type="file" name="image" accept="image/*">
	<input type="submit" value="submit">
</form>

<script type="text/javascript">
	let author = document.querySelector('input#author');
	let authorShow = document.querySelector('img#authorShow');
	let cropper;
	
	$('input#author').on('change', function(){
		let [image] = author.files;
		if(!image){ return; }
		authorShow.src = URL.createObjectURL(image);
		cropper = new Cropper(authorShow,{
			viewMode: 2,
			aspectRatio: 4/4,
			preview: '#authorPreview',
		});
	});

	function submitForm(){
		cropper.getCroppedCanvas().toBlob(function(blob){
			author.files = blob;
		});
		return true;
	}

</script>

<?php

$iniMaxSize = ini_get('upload_max_filesize');
$maxSize = 1024*1024*5; // 5mb

if(isset($_FILES['image'])){
	$image = $_FILES['image'];
	$imageByte = filesize($image['tmp_name']);
	$imageSize = getimagesize($image['tmp_name']);
	$imageMime = $imageSize['mime'];
	//
	if($imageByte > $maxSize){ die('File is too big, must be smaller then '.$maxSize); }
	if(!$imageSize){ die('File is too big, must be smaller then '.$iniMaxSize); }
	if(!in_array($imageMime, ['image/jpeg', 'image/png',])){ die('<br>this format is not allow'); }
	//
	$imageBin = file_get_contents($image['tmp_name']);
	$imageBase64 = base64_encode($imageBin);
	echo '<img src="data:image/png;base64, '.$imageBase64.'">';
}else{ echo "no"; }
// explode(',', $imgBase64)[1]; // 去除前綴