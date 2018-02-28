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

// Создание сессии. Подробнее: https://topvisor.ru/api/v2/sdk-php/session/
$session = new TV\Session();

// введите id своего проекта
$projectId = 2121417;

// выводит количество включённых или выключенных групп
function showAmountOfGroups($switchedOn){
	global $groupsSelector;

	// Создадим фильтр, проверяющий, включена ли группа. Подробнее о фильтрах: https://topvisor.ru/api/v2/basic-params/filters/
	$groupsFilters = [TV\Fields::genFilterData('on', 'EQUALS', [$switchedOn])];

	// установим фильтры
	$groupsSelector->setFilters($groupsFilters);

	// выполнение запроса
	$pageOfGroupsSelector = $groupsSelector->exec();

	// метод getErrorsString() вернёт все возникшие ошибки в одной строке
	if($pageOfGroupsSelector->getErrors()) throw new \Exception($pageOfGroupsSelector->getErrorsString());

	// сохраним результат выполнения запроса - массив объектов
	$resultOfGroupsSelector = $pageOfGroupsSelector->getResult();
	$countOfGroups = count($resultOfGroupsSelector);

	$switchMessage = ($switchedOn)?'Включено':'Выключено';

	echo "$switchMessage <b>$countOfGroups</b> групп<br>\n";
};

try{
	$groupsData = [
		'project_id' => $projectId, 'on' => 1,
	]; // параметры запроса
	$groupsFields = ['on']; // поля, которые потребуются
	$groupsFilters = [TV\Fields::genFilterData('on', 'EQUALS', [0])]; // фильтры, которые "найдут" выключенные группы

	// Объект для построения запроса. Подробнее: https://topvisor.ru/api/v2/sdk-php/pen/
	$groupsSelector = new TV\Pen($session, 'get', 'keywords_2', 'groups');

	// установка параметров запроса
	$groupsSelector->setData($groupsData);

	// Для любого запроса с оператором get необходимо указывать поля. Подробнее: https://topvisor.ru/api/v2/basic-params/fields/
	$groupsSelector->setFields($groupsFields);

	// выведем, сколько групп было включено и выключено до использования метода
	showAmountOfGroups(1);
	showAmountOfGroups(0);

	$groupsEditor = new TV\Pen($session, 'edit', 'keywords_2', 'groups/on');

	$groupsEditor->setData($groupsData);
	$groupsEditor->setFields($groupsFields);
	$groupsEditor->setFilters($groupsFilters);

	$pageOfGroupsEditor = $groupsEditor->exec();

	if($pageOfGroupsEditor->getErrors()) throw new \Exception($pageOfGroupsEditor->getErrorsString());

	echo "<br>\n";
	echo "<b>Все группы включены</b><br><br>\n\n";

	showAmountOfGroups(1);
	showAmountOfGroups(0);
}catch(Exception $e){
	echo $e->getMessage();
}