<?php

namespace TopvisorSDK\V2;

class Pen{

	protected $TVSession;
	protected $oper;
	protected $moduleName;
	protected $methodName;
	protected $arrayData;
	protected $fieldsSelect = [];
	protected $fieldsFilter = [];
	protected $fieldsOrder = [];
	protected $limit = [];
	protected $offset = [];

	function __construct(Session $TVSession, $oper, $moduleName, $methodName = NULL, array $arrayData = NULL){
		$this->TVSession = $TVSession;

		switch($oper){
			case 'get':
			case 'add':
			case 'edit':
			case 'del': break;
			default: throw new \Exception('Invalid oper name');
		}

		$this->oper = $oper;
		$this->moduleName = $moduleName;
		$this->methodName = $methodName;
		$this->arrayData = $arrayData;
	}

	function setData(array $arrayData){
		$this->arrayData = $arrayData;
	}

	function setFields(array $fieldsSelect){
		$this->fieldsSelect = $fieldsSelect;
	}

	function setFilters(array $fieldsFilter){
		$this->fieldsFilter = $fieldsFilter;
	}

	function serOrders(array $fieldsOrder){
		$this->fieldsOrder = $fieldsOrder;
	}

	function setLimit($limit){
		$this->limit = (int)$limit;
	}

	function setOffset($offset){
		$this->offset = (int)$offset;
	}

	function exec(){
		$url = $this->TVSession->url."/$this->oper/$this->moduleName";
		if($this->methodName) $url .= "/$this->methodName";

		$headers = $this->TVSession->getHeadersForRequest();

		$arrayData = $this->arrayData;
		if($this->fieldsSelect) $arrayData['fields'] = $this->fieldsSelect;
		if($this->fieldsFilter) $arrayData['filters'] = $this->fieldsFilter;
		if($this->fieldsOrder) $arrayData['orders'] = $this->fieldsOrder;
		if($this->limit) $arrayData['limit'] = $this->limit;
		if($this->offset) $arrayData['offset'] = $this->offset;

		return new Page($url, $headers, $arrayData);
	}

}
