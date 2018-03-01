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
	
	// Запришиваемые поля. id Санкт-Петербурга - 2, Москвы - 213, России - 225.
	// id региона можно узнать, выполнив запрос get/mod_common/regions
	$keywordsSelectorFields = ['name', "volume:2:0:6", "volume:213:0:6", "volume:225:0:6"];
	$keywordsSelectorFilter = [TV\Fields::genFilterData('name', 'EQUALS', ['топвизор'])]; // массив с указанием фильтра
	
	// объект для построения запроса на получение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$keywordsSelector = new TV\Pen($TVSession, 'get', 'keywords_2', 'keywords');
	$keywordsSelector->setData($keywordsSelectorData);
	$keywordsSelector->setFields($keywordsSelectorFields); // https://topvisor.ru/api/v2/basic-params/fields/
	$keywordsSelector->setFilters($keywordsSelectorFilter); // https://topvisor.ru/api/v2/basic-params/filters/
	$pageOfKeywordsSelector = $keywordsSelector->exec(); // выполнить обращение к API
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$resultOfKeywordsSelector = $pageOfKeywordsSelector->getResult(); // результат выполнения запроса, массив выбранных ключевых слов
	$selectedKeyword = $resultOfKeywordsSelector[0];
	$frequencySPb = $selectedKeyword->{'volume:2:0:6'};
	$frequencyMsk = $selectedKeyword->{'volume:213:0:6'};
	$frequencyRF = $selectedKeyword->{'volume:225:0:6'};
	echo "топвизор $frequencySPb / $frequencyMsk / $frequencyRF<br>\n";
}catch(Exception $e){
	echo $e->getMessage();
}