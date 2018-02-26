<?

/**
 * Чтобы изменить порядок ключевых фраз в проекте, воспользуйтесь методом сортировки.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/edit-sort/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта
$groupId = 6198893; // введите id своей группы

try{
	$keywordsSelectorData = [
		'project_id' => $projectId,
		'group_id' => $groupId,
	];
	$keywordsFilters = ['id', 'name'];
	
	$keywordsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'keywords');
	$keywordsSelector->setData($keywordsSelectorData);
	$keywordsSelector->setFields($keywordsFilters);
	$pageOfKeywordsSelector = $keywordsSelector->exec();
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$selectedKeywords = $pageOfKeywordsSelector->getResult();
	echo "<b>Содержимое группы id$groupId до сортировки:</b><br>\n";
	foreach($selectedKeywords as $keyword){
		echo "$keyword->id $keyword->name<br>\n";
	}
	
	$direction = (rand(0, 1)==0)?'DESC':'ASC';
	$keywordsSorterData = [
		'project_id' => $projectId,
		'group_id' => $groupId,
	];
	$keywordsOrderData = [TV\Fields::genOrderData('name', $direction)]; // сортировка по ключевой фразе
	
	$keywordsSorter = new TV\Pen($Session, 'edit', 'keywords_2', 'keywords/sort');
	$keywordsSorter->setData($keywordsSorterData);
	$keywordsSorter->serOrders($keywordsOrderData);
	$pageOfKeywordsSorter = $keywordsSorter->exec();
	
	if($pageOfKeywordsSorter->getErrors()) throw new \Exception($pageOfKeywordsSorter->getErrorsString());
	
	$direction = ($keywordsOrderData[0]['direction'] == 'ASC')?'по возрастанию':'по убыванию';
	echo "<br>\n";
	echo "<b>Cортировка группы id$groupId $direction выполнена успешно!</b><br><br>\n\n";
	
	$pageOfKeywordsSelector = $keywordsSelector->exec();
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$selectedKeywords = $pageOfKeywordsSelector->getResult();
	
	echo "<b>Содержимое группы id$groupId после сортировки:</b><br>\n";
	foreach($selectedKeywords as $keyword){
		echo "$keyword->id $keyword->name<br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}