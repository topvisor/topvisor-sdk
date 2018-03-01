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
	echo "<b>Регион: \"ключевое слово\" - частота</b><br>\n"; // Вывод шапки таблицы
	
	$keywordsSelectorData = ['project_id' => $projectId]; // массив с параметрами запроса
	
	// id региона можно узнать запросом get/mod_common/regions
	$keywordsSelectorMoscowFields = ['name', "volume:213:0:6"]; // Запрашиваемые поля. id региона Москва - 213
	$keywordsSelectorSPbFields = ['name', "volume:2:0:6"]; // id региона Санкт-Петербург - 2
	$keywordsSelectorRussiaFields = ['name', "volume:225:0:6"]; // id региона Россия - 225
	$keywordsSelectorFilter = [TV\Fields::genFilterData('name', 'EQUALS', ['топвизор'])]; // массив с указанием фильтра
	
	// объект для построения запроса на получение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$keywordsSelector = new TV\Pen($TVSession, 'get', 'keywords_2', 'keywords');
	$keywordsSelector->setData($keywordsSelectorData);
	
	// получаем данные для Москвы
	$keywordsSelector->setFields($keywordsSelectorMoscowFields); // https://topvisor.ru/api/v2/basic-params/fields/
	$keywordsSelector->setFilters($keywordsSelectorFilter); // https://topvisor.ru/api/v2/basic-params/filters/
	$pageOfKeywordsSelector = $keywordsSelector->exec(); // выполнить обращение к API
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$resultOfKeywordsSelector = $pageOfKeywordsSelector->getResult(); // результат выполнения запроса, массив выбранных ключевых слов
	$selectedKeyword = $resultOfKeywordsSelector[0];
	$frequency = $selectedKeyword->{'volume:213:0:6'};
	echo "Москва: \"$selectedKeyword->name\" - $frequency <br>\n";
	
	// получаем данные для Санкт-петербурга
	$keywordsSelector->setFields($keywordsSelectorSPbFields);
	$pageOfKeywordsSelector = $keywordsSelector->exec();
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$resultOfKeywordsSelector = $pageOfKeywordsSelector->getResult();
	$selectedKeyword = $resultOfKeywordsSelector[0];
	$frequency = $selectedKeyword->{'volume:2:0:6'};
	echo "Санкт-Петербург: \"$selectedKeyword->name\" - $frequency<br>\n";
	
	// получаем данные для России
	$keywordsSelector->setFields($keywordsSelectorRussiaFields);
	$pageOfKeywordsSelector = $keywordsSelector->exec();
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$resultOfKeywordsSelector = $pageOfKeywordsSelector->getResult();
	$selectedKeyword = $resultOfKeywordsSelector[0];
	$frequency = $selectedKeyword->{'volume:225:0:6'};
	echo "Россия: \"$selectedKeyword->name\" - $frequency<br>\n";
}catch(Exception $e){
	echo $e->getMessage();
}