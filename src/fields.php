<?php

namespace TopvisorSDK\V2;

class Fields{

	static function genFilterData($name, $operator, array $values = ['']){
		return ['name' => $name, 'operator' => $operator, 'values' => $values];
	}

	static function genOrderData($name, $direction){
		return ['name' => $name, 'direction' => $direction];
	}

}