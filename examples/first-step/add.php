<?php

/**
 * Для добавления объектов используется оператор add.
 * В данном примере добавим в проект одним запросом 3 включенных или выключенных группы и
 * выведем список добавленных групп со статусом вкл/выкл и общее число групп.
 *
 * https://topvisor.ru/api/v2/operators/get/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

$TVSession = new TV\Session(); // создание сессии: https://topvisor.ru/api/v2/sdk-php/session/

$projectId = 2121417; // введите id своего проекта

try{
	$groupsAdderData = [
		'project_id' => $projectId,
		'name' => ['Крокодилы', 'Бегемоты', 'Зелёные попугаи'],
		'on' => rand(0, 1),
	]; // массив с параметрами запроса
	
	// объект для построения запроса на добавление данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$groupsAdder = new TV\Pen($TVSession, 'add', 'keywords_2', 'groups');
	
	$groupsAdder->setData($groupsAdderData);
	
	$pageOfGroupsAdder = $groupsAdder->exec(); // выполнить обращение к API
	
	// метод getErrorsString() вернёт все возникшие ошибки в одной строке
	if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());
	
	// результат выполнения запроса, в данном случае это массив с количеством добавленных групп
	$resultOfAddedGroup = $pageOfGroupsAdder->getResult();
	
	// сохраним количество возвращённых объектов - добавленных групп
	$countOfAddedGroups = count($resultOfAddedGroup);
	
	echo "<b>Добавлено $countOfAddedGroups новые группы:</b><br>\n";
	
	// построчный вывод имени и активности каждой группы
	foreach($resultOfAddedGroup as $addedGroup){
		$switchedMessage = ($addedGroup->on)?'вкл.':'выкл.';
		echo "\"$addedGroup->name\" $switchedMessage<br>\n";
	}
	
	// УЗНАЕМ ОБЩЕЕ КОЛИЧЕСТВО ГРУПП В ПРОЕКТЕ
	$groupsSelectorData = ['project_id' => $projectId];
	$groupsSelectorFields = ['COUNT(*)'];
	
	$groupsSelector = new TV\Pen($TVSession, 'get', 'keywords_2', 'groups');
	$groupsSelector->setData($groupsSelectorData);
	$groupsSelector->setFields($groupsSelectorFields);
	
	$pageOfGroupsSelector = $groupsSelector->exec();
	
	if($pageOfGroupsSelector->getErrors()) throw new \Exception($pageOfGroupsSelector->getErrorsString());
	
	$resultOfGroupsSelector = $pageOfGroupsSelector->getResult();
	$amountOfAllGroups = $resultOfGroupsSelector[0]->{'COUNT(*)'};
	
	echo "Всего в проекте $amountOfAllGroups групп";
}catch(Exception $e){
	echo $e->getMessage();
}