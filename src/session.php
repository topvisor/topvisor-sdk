<?php

namespace TopvisorSDK\V2;

class Session{

	var $url = 'https://api.topvisor.com/v2/json';
	protected $userId = NULL;
	protected $accessToken = NULL;

	function __construct(array $auth = NULL){
		$this->setAuth($auth);
	}

	protected function setAuth(array $auth = NULL){
		if(is_null($auth)){
			$auth = [];

			include(__DIR__.'/config.php');
			$auth = [
				'userId' => (isset($userId))?$userId:'',
				'accessToken' => (isset($accessToken))?$accessToken:''
			];
		}

		if(isset($auth['userId'])) $this->userId = $auth['userId'];
		if(isset($auth['accessToken'])) $this->accessToken = $auth['accessToken'];

		if(!$this->userId) throw new \Exception("Auth config: 'userId' is missing");
		if(!$this->accessToken) throw new \Exception("Auth config: 'accessToken' is missing");
	}

	// only utf-8 is supported
	function getHeadersForRequest(){
		$headers = [
			"Authorization: Bearer $this->accessToken",
			"User-Id: $this->userId",
			'Content-Type: application/json; charset=utf-8'
		];

		return $headers;
	}

}
