<?php

/**
 * Для добавления объектов используется оператор add.
 * В данном примере добавим в проект одним запросом 3 включенных или выключенных группы и
 * выведем список добавленных групп со статусом вкл/выкл и общее число групп.
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// Создание сессии. Подробнее: https://dev.topvisor.ru/api/v2/sdk-php/session/
$Session = new TV\Session();

// введите id своего проекта
$projectId = 2121417;

try{
	// параметры запроса
	$groupsAdderData = [
		'project_id' => $projectId,
		'name' => ['Крокодилы', 'Бегемоты', 'Зелёный попугай'],
		'on' => rand(0, 1),
	];
	
	// Объект для построения запроса. Подробнее: https://dev.topvisor.ru/api/v2/sdk-php/pen/
	$groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
	
	// установка параметров запроса
	$groupsAdder->setData($groupsAdderData);
	
	// выполнение запроса
	$pageOfGroupsAdder = $groupsAdder->exec();
	
	// метод getErrorsString() вернёт все возникшие ошибки в одной строке
	if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());
	
	// сохраним результат выполнения запроса - массив объектов
	$resultOfAddedGroup = $pageOfGroupsAdder->getResult();
	// сохраним количество возвращённых объектов - добавленных групп
	$countOfAddedGroups = count($resultOfAddedGroup);
	
	echo "<b>Добавлено $countOfAddedGroups новые группы:</b><br>\n";
	
	// для каждой добавленной группы выведем её имя и активность
	foreach($resultOfAddedGroup as $addedGroup){
		$switchedMessage = ($addedGroup->on)?'вкл.':'выкл.';
		echo "\"$addedGroup->name\" $switchedMessage<br>\n";
	}
	
	// узнаем общее количество групп в проекте
	
	$groupsSelectorData = ['project_id' => $projectId];
	// так как нас интересует только количество групп, никакие поля не понадобятся
	$groupsSelectorFields = [];
	
	$groupsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'groups');
	
	$groupsSelector->setData($groupsSelectorData);
	// Для любого запроса с оператором get необходимо указывать поля. Подробнее: https://dev.topvisor.ru/api/v2/basic-params/fields/
	$groupsSelector->setFields($groupsSelectorFields);
	
	$pageOfGroupsSelector = $groupsSelector->exec();
	
	if($pageOfGroupsSelector->getErrors()) throw new \Exception($pageOfGroupsSelector->getErrorsString());
	
	$resultOfGroupsSelector = $pageOfGroupsSelector->getResult();
	$amountOfAllGroups = count($resultOfGroupsSelector);
	
	echo "Всего в проекте $amountOfAllGroups групп";
}catch(Exception $e){
	echo $e->getMessage();
}