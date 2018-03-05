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

$TVSession = new TV\Session();

$projectId = 1733522; // введите id своего проекта

try{
	$regionsSelectorData = ['show_searchers_and_regions' => 1];
	$regionsSelectorFilters = [TV\Fields::genFilterData('id', 'EQUALS', [$projectId])]; // массив с указанием фильтра
	
	$regionsSelector = new TV\Pen($TVSession, 'get', 'projects_2', 'projects');
	
	$regionsSelector->setData($regionsSelectorData);
	$regionsSelector->setFilters($regionsSelectorFilters);
	$pageOfRegionsSelector = $regionsSelector->exec();
	
	if($pageOfRegionsSelector->getErrors()) throw new \Exception($pageOfRegionsSelector->getErrorsString());
	
	$resultOfRegionsSelector = $pageOfRegionsSelector->getResult();
	foreach($resultOfRegionsSelector as $region){
		$selectedRegion = $region;
	}
	
	$projectName = $selectedRegion->name;
	$searchers = $selectedRegion->searchers;
	
	// массив индексов регионов ассиативный, чтобы избежать повторений индексов
	$regionsArray = [];
	
	foreach($searchers as $searchSystem){
		foreach($searchSystem->regions as $region){
			$regionsArray[$region->name] = $region->index;
		}
	}
	$positionsSelectorFields = ['name'];
	
	$positionsSelector = new TV\Pen($TVSession, 'get', 'positions_2', 'history');
	
	// вывод таблицы для каждого региона
	foreach($regionsArray as $region){
		$positionSelectorData = [
			'project_id'   => $projectId,
			'regions_indexes' => [$region],
			'date1' => '0001-01-01',
			'date2' => '2999-01-01',
			'show_exists_dates' => 1,
			'show_headers' => 1,
		];
		
		$positionsSelector->setData($positionSelectorData);
		$positionsSelector->setFields($positionsSelectorFields);
		$pageOfPositionsSelector = $positionsSelector->exec();
		
		if($pageOfPositionsSelector->getErrors()) throw new \Exception($pageOfPositionsSelector->getErrorsString());
		
		$resultOfPositionsSelector = $pageOfPositionsSelector->getResult();
		
		// если не было дат проверок, переходим к следующему региону
		if(!count($resultOfPositionsSelector->existsDates)) continue;
		
		foreach($resultOfPositionsSelector->headers->projects as $project){
			foreach($project->searchers as $searcher){
				$searcherName = $searcher->name;
				foreach($searcher->regions as $region){
					$regionName = $region->name;
				}
			}
		}
		
		// вывод шапки таблицы
		echo "<table border=\"1\">";
		echo "<caption> Проект \"$projectName\", регион $regionName, ПС $searcherName";
		echo "<tr>";
		echo "<th>Ключевая фраза</th>";
		
		// перевернём массив дат, чтобы вывести последние
		$dates = array_reverse($resultOfPositionsSelector->existsDates);
		
		// соберём вес поля в массив. чтобы потом выполнить один запрос со всеми полями
		$positionsFields = [];
		for($i = 0; $i < 10; $i++){
			echo "<td>$dates[$i]</td>";
			$positionField = "position:$dates[$i]:$projectId:$region->index";
			array_push($positionsFields, $positionField);
		}
		
		$positionsSelector->setFields($positionsFields);
		$pageOfPositionsSelector = $positionsSelector->exec();
		$resultOfPositionsSelector = $pageOfPositionsSelector->getResult();
		$keywords = $resultOfPositionsSelector->keywords;
		
		foreach($keywords as $keyword){
			echo "<tr>";
			echo "<td>$keyword->name</td>";
			foreach($positionsFields as $dateField){
				$positionsOnDate = $keyword->$dateField;
				echo "<td>$positionsOnDate</td>";
			}
		}
		echo "</table>";
	}
}catch(Exception $e){
	echo $e->getMessage();
}