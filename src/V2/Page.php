<?php

namespace Topvisor\TopvisorSDK\V2;

class Page{

	protected $result = NULL;
	protected $nextOffset = NULL;
	protected $total = NULL;
	protected $messages = [];
	protected $errors = [];
	protected $headers = [];

	function __construct($url, array $headers, array $arrayData = NULL){
		$this->sendRequest($url, $headers, $arrayData);
	}

	protected function sendRequest($url, array $headers, array $arrayData = NULL){
		$this->headers = [];
		$this->errors = [];
		$this->messages = [];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Topvisor_API');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // так работает

		if($arrayData){
			$jsonData = json_encode($arrayData);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		}

		$response = curl_exec($ch);

		if(curl_errno($ch)){
			$this->addError(curl_error($ch), NULL, -curl_errno($ch));
		}else{
			$this->headers = preg_split("/\r\n/", trim(substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE))));
			$response = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

			$headersString = implode("\n", $this->headers);
			switch(true){
				case preg_match('/^content-disposition: attachment/m', strtolower($headersString)):
					$this->setFileResult($response);

					break;
				default:
					$this->setResult($response);
			}
		}

		curl_close($ch);
	}

	protected function setResult($response){
		$responseObject = json_decode($response);

		if(is_null($responseObject)){
			$this->addError('Response is broken data', "Response data: $response");
			return;
		}

		if(isset($responseObject->result)) $this->result = $responseObject->result;
		if(isset($responseObject->nextOffset)) $this->nextOffset = $responseObject->nextOffset;
		if(isset($responseObject->total)) $this->total = $responseObject->total;
		if(isset($responseObject->messages)) $this->messages = $responseObject->messages;
		if(isset($responseObject->errors)) $this->errors = $responseObject->errors;
	}

	protected function setFileResult($response){
		$this->result = new \stdClass();
		$this->result->data = $response;
		$this->result->filename = NULL;
		$this->result->type = NULL;

		foreach($this->headers as $header){
			if(preg_match('/filename="([^"]*)"/', $header, $matches)){
				$this->result->filename = $matches[1];
				break;
			}
		}

		foreach($this->headers as $header){
			if(preg_match('/Content-Type: ([^;]*)/', $header, $matches)){
				$this->result->type = $matches[1];
				break;
			}
		}
	}

	function addError($string = '', $datail = NULL, $code = 0){
		$error = new \stdClass();

		$error->string = $string;
		$error->detail = $datail;
		$error->code = $code;

		$this->errors[] = $error;
	}

	function addMessage($message){
		$this->messages[] = $message;
	}

	function getResult(){
		return $this->result;
	}

	function getNextOffset(){
		return $this->nextOffset;
	}

	function getTotal(){
		return $this->total;
	}

	function getHeaders(){
		return $this->headers;
	}

	function getErrors(){
		return $this->errors;
	}

	function getMessages(){
		return $this->messages;
	}

	function getErrorsString(){
		$errorsString = [];
		foreach($this->errors as $error){
			$errorsString[] = $error->string.($error->code?" ($error->code)":'').($error->detail?": $error->detail":'');
		}
		$errorsString = implode("<br>", $errorsString);

		return $errorsString;
	}

	function getMessagesString(){
		return implode("<br>", $this->messages);
	}

	function throwException(){
		if($this->errors){
			throw new \Exception($this->errors[0]->string, $this->errors[0]->code);
		}else{
			throw new \Exception("Undefined error, please check request");
		}
	}

}
