<?php

/**
 * Некоторые поля объектов зависят от разных параметров. Например, позиции по фразе
 * будет отличаться в зависимости от id проекта. В данном примере показано,
 * как одним запросом к API получить частоту для запроса "топвизор".
 * Обратите внимание, что эта ключевая фраза должна существовать в вашем проекте.
 *
 * https://topvisor.ru/api/v2/fields/qualifiers/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

$TVSession = new TV\Session(); // создание сессии: https://topvisor.ru/api/v2/sdk-php/session/

$projectId = 2121417; // введите id своего проекта

try{
	echo "<b>====== Санкт-Петербург / Москва / Россия</b><br>\n"; // вывод шапки таблицы
	
	$keywordsSelectorData = ['project_id' => $projectId]; // массив с параметрами запроса
	
	// id региона можно узнать, выполнив запрос get/mod_common/regions
	$spbRegionId = 2; // id Санкт-Петербурга
	$mskRegionId = 213; // id Москвы
	$rfRegionId = 225; // id России
	$searcherKey = 0; // ключ ПС Яндекс
	$frequencyType = 6; // форма частоты "[!Ч]"
	
	$spbVolumeField = "volume:$spbRegionId:$searcherKey:$frequencyType";
	$mskVolumeField = "volume:$mskRegionId:$searcherKey:$frequencyType";
	$rfVolumeField = "volume:$rfRegionId:$searcherKey:$frequencyType";
	
	$keywordsSelectorFields = ['name', $spbVolumeField, $mskVolumeField, $rfVolumeField];
	$keywordsSelectorFilter = [TV\Fields::genFilterData('name', 'EQUALS', ['топвизор'])]; // массив с указанием фильтра
	
	// объект для построения запроса на получение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$keywordsSelector = new TV\Pen($TVSession, 'get', 'keywords_2', 'keywords');
	$keywordsSelector->setData($keywordsSelectorData);
	$keywordsSelector->setFields($keywordsSelectorFields); // https://topvisor.ru/api/v2/basic-params/fields/
	$keywordsSelector->setFilters($keywordsSelectorFilter); // https://topvisor.ru/api/v2/basic-params/filters/
	$pageOfKeywordsSelector = $keywordsSelector->exec(); // выполнить обращение к API
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$resultOfKeywordsSelector = $pageOfKeywordsSelector->getResult(); // результат выполнения запроса, массив выбранных ключевых слов
	
	foreach($resultOfKeywordsSelector as $selectedKeyword){
		echo "топвизор - $selectedKeyword->$spbVolumeField / $selectedKeyword->$mskVolumeField / $selectedKeyword->$rfVolumeField<br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}