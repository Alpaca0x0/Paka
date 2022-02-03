<?php defined('INIT') or die('NO INIT'); ?>

<?php
// ini_set('upload_max_filesize', "20M");
// ini_set('post_max_size', "20M");
@include_once(Conf('imgur'));

class Imgur{
	private $api = Imgur['api'],
			$timeout = 6,
			$url, $datas, $method, $response, $result;

	function __construct(){ }

	function Clear(){
		$this->url = Imgur['url'];
		$this->method = 'POST';
		$this->datas = [];
		$this->response = false;
		$this->result = [];
	}

	private function Request(){
		// echo $this->url."<br>";
		// echo $this->timeout."<br>";
		// echo $this->api['id']."<br>";
		// echo $this->datas."<br>";
		// echo $this->method."<br>";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $this->api['id']));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->datas);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$this->response = curl_exec($curl);
		// var_dump(curl_error($curl));
		curl_close ($curl);


		$this->result = json_decode($this->response, true);
		return $this->result;
		// if($this->result['success'] === true){ return $this->result; }
		// else{ return $this->result['data']['error']; }
	}

	function Upload($image, $title='testing'){
		$this->Clear();
		$this->method = 'POST';
		$this->datas = [
			// 'album' => 'aJNK6LR', // profile
			'image' => $image,
			'title' => $title,
		];
		return $this->Request();
	}

	function Delete($deletehash){
		$this->Clear();
		$this->method = 'DELETE';
		$this->url = $this->url.$deletehash;
		$this->datas = [];
		return $this->Request();
	}
}

