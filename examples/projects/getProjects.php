<?php

/**
 * В данном примере получим и выведем 3 таблицы - все свои проекты,
 * гостевые и архивные.
 *
 * https://topvisor.ru/api/v2-services/projects_2/projects/get/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

$TVSession = new TV\Session();
$userId = $TVSession->getUserId();

try{
	$projectsFields = ['name', 'site', 'update', 'on'];
	$projectsFiltersMyOwn = [TV\Fields::genFilterData('user_id', 'EQUALS', [$userId])]; // фильтры для своих проектов
	$projectsFiltersGuest = [TV\Fields::genFilterData('user_id', 'NOT_EQUALS', [$userId])]; // фильтры для гостевых проектов
	$projectsFiltersArchive = [TV\Fields::genFilterData('on', 'EQUALS', [-1])]; // фильтры для архивных проектов
	
	$projectsSelector = new TV\Pen($TVSession, 'get', 'projects_2', 'projects');
	$projectsSelector->setFields($projectsFields);
	
	// Найдём и выведем свои проекты
	$projectsSelector->setFilters($projectsFiltersMyOwn);
	$pageOfProjectsSelector = $projectsSelector->exec();
	
	if($pageOfProjectsSelector->getErrors()) throw new \Exception($pageOfProjectsSelector->getErrorsString());
	
	$resultOfProjectsSelector = $pageOfProjectsSelector->getResult();
	echo "<b>Мои проекты:</b><br>\n";
	echo "<b>Имя проекта (сайт), время последней проверки позиций</b><br>\n";
	foreach($resultOfProjectsSelector as $project){
		echo "$project->name ($project->site), $project->update<br>\n";
	}
	
	// Найдём и выведем гостевые проекты
	$projectsSelector->setFilters($projectsFiltersGuest);
	$pageOfProjectsSelector = $projectsSelector->exec();
	
	if($pageOfProjectsSelector->getErrors()) throw new \Exception($pageOfProjectsSelector->getErrorsString());
	
	$resultOfProjectsSelector = $pageOfProjectsSelector->getResult();
	echo "<br>\n";
	echo "<b>Гостевые проекты:</b><br>\n";
	echo "<b>Имя проекта (сайт), время последней проверки позиций</b><br>\n";
	foreach($resultOfProjectsSelector as $project){
		echo "$project->name ($project->site), $project->update<br>\n";
	}
	
	// Найдём и выведем архивные проекты
	$projectsSelector->setFilters($projectsFiltersArchive);
	$pageOfProjectsSelector = $projectsSelector->exec();
	
	if($pageOfProjectsSelector->getErrors()) throw new \Exception($pageOfProjectsSelector->getErrorsString());
	
	$resultOfProjectsSelector = $pageOfProjectsSelector->getResult();
	echo "<br>\n";
	echo "<b>Архивные проекты:</b><br>\n";
	echo "<b>Имя проекта (сайт), время последней проверки позиций</b><br>\n";
	foreach($resultOfProjectsSelector as $project){
		echo "$project->name ($project->site), $project->update<br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}
