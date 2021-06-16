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

include(__DIR__.'/../../vendor/autoload.php');

$TVSession = new TV\Session(); // создание сессии: https://topvisor.ru/api/v2/sdk-php/session/

$projectId = 2121417; // введите id своего проекта

// id региона можно узнать, выполнив запрос get/mod_common/regions
$spbRegionId = 2; // id Санкт-Петербурга
$mskRegionId = 213; // id Москвы
$rfRegionId = 225; // id России
$searcherKey = 0; // ключ ПС Яндекс
$volumeType = 6; // форма частоты "[!Ч]"

try{
	// описание поля volume и других полей можно посмотреть тут: https://topvisor.ru/api/v2/fields/
	$spbVolumeField = "volume:$spbRegionId:$searcherKey:$volumeType";
	$mskVolumeField = "volume:$mskRegionId:$searcherKey:$volumeType";
	$rfVolumeField = "volume:$rfRegionId:$searcherKey:$volumeType";

	$keywordsSelectorData = ['project_id' => $projectId]; // массив с параметрами запроса
	$keywordsSelectorFields = ['name', $spbVolumeField, $mskVolumeField, $rfVolumeField];
	$keywordsSelectorFilter = [TV\Fields::genFilterData('name', 'EQUALS', ['топвизор'])]; // массив с указанием фильтра

	// объект для построения запроса на получение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$keywordsSelector = new TV\Pen($TVSession, 'get', 'keywords_2', 'keywords');
	$keywordsSelector->setData($keywordsSelectorData);
	$keywordsSelector->setFields($keywordsSelectorFields); // https://topvisor.ru/api/v2/basic-params/fields/
	$keywordsSelector->setFilters($keywordsSelectorFilter); // https://topvisor.ru/api/v2/basic-params/filters/
	$pageOfKeywordsSelector = $keywordsSelector->exec(); // выполнить обращение к API

	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());

	$resultOfKeywordsSelector = $pageOfKeywordsSelector->getResult(); // результат выполнения запроса, массив полученных ключевых слов

	echo "<b>====== Санкт-Петербург / Москва / Россия</b><br>\n"; // вывод шапки таблицы
	foreach($resultOfKeywordsSelector as $selectedKeyword){
		echo "топвизор - $selectedKeyword->$spbVolumeField / $selectedKeyword->$mskVolumeField / $selectedKeyword->$rfVolumeField<br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}