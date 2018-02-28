<?php
/**
 * Чтобы получить список проектов, воспользуйтесь этим методом.
 *
 * https://topvisor.ru/api/v2-services/projects_2/projects/get/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

$userId = 9; // введите свой id

$TVSession = new TV\Session($auth);

try{
	$projectsFields = ['name', 'site', 'date', 'on'];
	$projectsFiltersMyOwn = [TV\Fields::genFilterData('user_id', 'EQUALS', [$userId])]; // фильтры для своих проектов
	$projectsFiltersGuest = [TV\Fields::genFilterData('user_id', 'NOT_EQUALS', [$userId])]; // Фильтры для гостевых проектов
	$projectsFiltersArchive = [TV\Fields::genFilterData('on', 'EQUALS', [-1])]; // фильтры для архивных проектов
	
	$projectsSelector = new TV\Pen($TVSession, 'get', 'projects_2', 'projects');
	
	// Найдём и выведем свои проекты
	$projectsSelector->setFields($projectsFields);
	$projectsSelector->setFilters($projectsFiltersMyOwn);
	$pageOfProjectsSelector = $projectsSelector->exec();
	
	if($pageOfProjectsSelector->getErrors()) throw new \Exception($pageOfProjectsSelector->getErrorsString());
	
	$resultOfProjectsSelector = $pageOfProjectsSelector->getResult();
	echo "<b>Мои проекты:</b><br>\n<b>name;site;date</b><br>\n";
	foreach($resultOfProjectsSelector as $project){
		echo "$project->name;$project->site;$project->date<br>\n";
	}
	
	// Найдём и выведем гостевые проекты
	$projectsSelector->setFilters($projectsFiltersGuest);
	$pageOfProjectsSelector = $projectsSelector->exec();
	
	if($pageOfProjectsSelector->getErrors()) throw new \Exception($pageOfProjectsSelector->getErrorsString());
	
	$resultOfProjectsSelector = $pageOfProjectsSelector->getResult();
	echo "<br>\n";
	echo "<b>Гостевые проекты:</b><br>\n<b>name;site;date</b><br>\n";
	foreach($resultOfProjectsSelector as $project){
		echo "$project->name;$project->site;$project->date<br>\n";
	}
	
	// Найдём и выведем архивные проекты
	$projectsSelector->setFilters($projectsFiltersArchive);
	$pageOfProjectsSelector = $projectsSelector->exec();
	
	if($pageOfProjectsSelector->getErrors()) throw new \Exception($pageOfProjectsSelector->getErrorsString());
	
	$resultOfProjectsSelector = $pageOfProjectsSelector->getResult();
	echo "<br>\n";
	echo "<b>Архивные проекты:</b><br>\n<b>name;site;date</b><br>\n";
	foreach($resultOfProjectsSelector as $project){
		echo "$project->name;$project->site;$project->date<br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}
