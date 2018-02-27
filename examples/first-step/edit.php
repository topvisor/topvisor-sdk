<?php

/**
 * Для редактирования объектов используется оператор edit.
 * В данном примере отобразим количество включенных и выключенных групп проекта.
 * Затем включим все выключенные группы проекта и снова тобразим количество включенных и выключенных групп.
 * https://dev.topvisor.ru/api/v2/operators/edit/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session($auth);

// введите id своего проекта
$projectId = 2121417;

// выводит количество включённых или выключенных групп
function showCountOfSwitchedGroups($switch){
	global $groupsSelector;
	
	// создадим фильтр, проверяющий, включена ли группа
	$groupsFilters = [TV\Fields::genFilterData('on', 'EQUALS', [$switch])];
	
	$groupsSelector->setFilters($groupsFilters);
	
	$pageOfGroupsSelector = $groupsSelector->exec();
	
	if($pageOfGroupsSelector->getErrors()) throw new \Exception($pageOfGroupsSelector->getErrorsString());
	
	$resultOfGroupsSelector = $pageOfGroupsSelector->getResult();
	$countOfSwitchedOnGroups = count($resultOfGroupsSelector);
	
	$switchMessage = ($switch == 1)?'включено':'выключено';
	
	echo "$switchMessage <b>$countOfSwitchedOnGroups</b> групп<br>\n";
}

try{
	$groupsData = [
		'project_id' => $projectId, 'on' => 1,
	];
	$groupsFields = ['on'];
	$groupsFilters = [TV\Fields::genFilterData('on', 'EQUALS', [0])];
	
	$groupsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'groups');
	
	$groupsSelector->setData($groupsData);
	$groupsSelector->setFields($groupsFields);
	
	// выведем, сколько групп было включено и выключено до использования метода
	showCountOfSwitchedGroups(1);
	showCountOfSwitchedGroups(0);
	
	$groupsEditor = new TV\Pen($Session, 'edit', 'keywords_2', 'groups/on');
	
	$groupsEditor->setData($groupsData);
	$groupsEditor->setFields($groupsFields);
	$groupsEditor->setFilters($groupsFilters);
	
	$resultOfGroupsEditor = $groupsEditor->exec();
	
	if($resultOfGroupsEditor->getErrors()) throw new \Exception($resultOfGroupsEditor->getErrorsString());
	
	echo "<br>\n";
	echo "<b>Все группы включены</b><br><br>\n\n";
	
	showCountOfSwitchedGroups(1);
	showCountOfSwitchedGroups(0);
}catch(Exception $e){
	echo $e->getMessage();
}