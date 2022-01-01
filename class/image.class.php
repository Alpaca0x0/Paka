<?php defined('INIT') or die('NO INIT'); ?>

<?php

class Image{
	private $image;

	function __construct($image=""){ $this->image = $image; }

	function Image($image){ $this->image = $image; }

	function Get($what){
		$what = strtolower(trim($what));
		switch ($what) {
			case 'image':
				return $this->image;

			break;case 'byte':
				return @filesize($this->image['tmp_name']) || false;
			
			break;case 'size':
				$ret = @getimagesize($this->image['tmp_name']);
				return $ret;

			break;case 'width':
				$ret = @getimagesize($this->image['tmp_name'])[0];
				return $ret;

			break;case 'height':
				$ret = @getimagesize($this->image['tmp_name'])[1];
				return $ret;
			
			break;case 'mime':
				return @getimagesize($this->image['tmp_name'])['mime'] || false;
			
			break;case 'bin': case 'blob':
				return file_get_contents($this->image['tmp_name']);

			break;case 'base64':
				return base64_encode($this->Get('bin'));
			
			break;default:
				return 'error';
			break;
		}
	}

	function Is($what){
		$what = strtolower(trim($what));
		switch ($what) {
			case 'image': case 'picture':
				$ret = @getimagesize($this->image['tmp_name']);
				return $ret ? true : false;
			break;case '':
				//
			break;default:
				// code...
				break;
		}
	}
}



// if($imageByte > $maxSize){ die('File is too big, must be smaller then '.$maxSize); }
//         if(!$imageSize){ die('Not a image or file is too big, must be smaller then '.$iniMaxSize); }
//         if(!in_array($imageMime, ['image/jpeg', 'image/png',])){ die('<br>this format is not allow'); }
//         if($imageSize[0] !== $imageSize[1] || $imageSize[0]!=320){ die('Must be square'); }

//         echo '<img src="data:image/png;base64, '.$imageBase64.'">';