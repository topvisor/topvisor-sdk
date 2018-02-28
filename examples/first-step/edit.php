<?php
/**
 * Для редактирования объектов используется оператор edit.
 * В данном примере отобразим количество включенных и выключенных групп проекта.
 * Затем включим все выключенные группы проекта и снова отобразим количество включенных и выключенных групп.
 *
 * https://topvisor.ru/api/v2/operators/edit/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

$TVSession = new TV\Session(); // создание сессии: https://topvisor.ru/api/v2/sdk-php/session/

$projectId = 2121417; // введите id своего проекта

// выводит количество включённых ($on = 1) или выключенных ($on = 0) групп
function showCountGroupsByOn($groupsSelector, int $on){
	// Массив с указанием фильтра: https://topvisor.ru/api/v2/basic-params/filters/
	$groupsSelectorFilters = [TV\Fields::genFilterData('on', 'EQUALS', [$on])];
	
	$groupsSelector->setFilters($groupsSelectorFilters); // https://topvisor.ru/api/v2/basic-params/filters/
	$pageOfGroupsSelector = $groupsSelector->exec(); // выполнить обращение к API
	
	// метод getErrorsString() вернёт все возникшие ошибки в одной строке
	if($pageOfGroupsSelector->getErrors()) throw new \Exception($pageOfGroupsSelector->getErrorsString());
	
	// результат выполнения запроса, в данном случае это массив с количеством выбранных групп
	$resultOfGroupsSelector = $pageOfGroupsSelector->getResult();
	if(count($resultOfGroupsSelector)){
		$amountOfGroups = $resultOfGroupsSelector[0]->{'COUNT(*)'};
	}else{
		$amountOfGroups = 0;
	}
	$switchMessage = ($on)?'Включено':'Выключено';
	
	echo "$switchMessage <b>$amountOfGroups</b> групп<br>\n";
};

try{
	// массив с параметрами запроса
	$groupsData = [
		'project_id' => $projectId, 'on' => 1,
	];
	$groupsSelectorFields = ['COUNT(*)']; // запрашиваемые поля
	
	// объект для построения запроса на получение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$groupsSelector = new TV\Pen($TVSession, 'get', 'keywords_2', 'groups');
	
	$groupsSelector->setData($groupsData);
	$groupsSelector->setFields($groupsSelectorFields); // https://topvisor.ru/api/v2/basic-params/fields/
	
	// выведем, сколько групп было включено и выключено до использования метода
	showCountGroupsByOn($groupsSelector, 1);
	showCountGroupsByOn($groupsSelector, 0);
	
	$groupsEditorFilters = [TV\Fields::genFilterData('on', 'EQUALS', [0])]; // массив с указанием фильтра
	
	// объект для построения запроса на изменение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$groupsEditor = new TV\Pen($TVSession, 'edit', 'keywords_2', 'groups/on');
	
	$groupsEditor->setData($groupsData);
	$groupsEditor->setFilters($groupsEditorFilters);
	$pageOfGroupsEditor = $groupsEditor->exec();
	
	if($pageOfGroupsEditor->getErrors()) throw new \Exception($pageOfGroupsEditor->getErrorsString());
	
	echo "<br>\n";
	echo "<b>Все группы включены</b><br><br>\n\n";
	
	showCountGroupsByOn($groupsSelector, 1);
	showCountGroupsByOn($groupsSelector, 0);
}catch(Exception $e){
	echo $e->getMessage();
}