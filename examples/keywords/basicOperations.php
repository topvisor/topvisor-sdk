<?php

/**
 * Сервис Ключевые фразы создан для удобства работы с папками, группами и ключевыми словами.
 * В данном примере производится добавление новой папки, изменение её имени,
 * новой группы и добавление туда ключевой фразы.
 * https://topvisor.ru/api/v2-services/keywords_2/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try{
	// добавление папки
	$foldersAdderData = [
		'project_id' => $projectId,
		'name' => 'My first folder',
	];
	
	$foldersAdder = new TV\Pen($Session, 'add', 'keywords_2', 'folders');
	$foldersAdder->setData($foldersAdderData);
	$pageOfFoldersAdder = $foldersAdder->exec();
	
	if($pageOfFoldersAdder->getErrors()) throw new \Exception($pageOfFoldersAdder->getErrorsString());
	
	$addedFolder = $pageOfFoldersAdder->getResult();
	$folderId = $addedFolder->id;
	$folderName = $addedFolder->name;
	echo "Добавлена папка id$folderId с именем \"$folderName\"<br>\n";
	
	// изменим имя папки
	$foldersUpdaterData = [
		'project_id' => $projectId,
		'name' => 'My first renamed folder',
		'id' => $folderId,
	];
	
	$foldersUpdater = new TV\Pen($Session, 'edit', 'keywords_2', 'folders/rename');
	$foldersUpdater->setData($foldersUpdaterData);
	$pageOfFoldersUpdater = $foldersUpdater->exec();
	
	if($pageOfFoldersUpdater->getErrors()) throw new \Exception($pageOfFoldersUpdater->getErrorsString());
	
	$newFolderName = $foldersUpdaterData['name'];
	echo "Имя папки id$folderId изменено на \"$newFolderName\"<br>\n";
	
	// создадим группу в папке
	$groupsAdderData = [
		'project_id' => $projectId,
		'to_id' => $folderId,
		'name' => "My first group",
	];
	
	$groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
	$groupsAdder->setData($groupsAdderData);
	$pageOfGroupsAdder = $groupsAdder->exec();
	
	if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());
	
	$resultOfGroupsAdder = $pageOfGroupsAdder->getResult(); // тип возвращаемого значения - array
	$addedGroup = $resultOfGroupsAdder[0];
	echo "В папку id$folderId добавлена группа id$addedGroup->id с именем \"$addedGroup->name\"<br>\n";
	
	// добавим ключевое слово в группу
	$keywordsAdderData = [
		'project_id' => $projectId,
		'name' => 'My first added keyword',
		'to_id' => $addedGroup->id,
	];
	
	$keywordsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'keywords');
	$keywordsAdder->setData($keywordsAdderData);
	$pageOfKeywordsAdder = $keywordsAdder->exec();
	
	if($pageOfKeywordsAdder->getErrors()) throw new \Exception($pageOfKeywordsAdder->getErrorsString());
	
	$addedKeyword = $pageOfKeywordsAdder->getResult();
	$nameOfAddedKeyword = $addedKeyword->name;
	echo "В группу id$addedGroup->id добавлено ключевое слово \"$nameOfAddedKeyword\"\n";
}catch(Exception $e){
	echo $e->getMessage();
}