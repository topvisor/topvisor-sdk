<?php
/**
 * Для получения списка позиций используется оператор get.
 * В данном примере получим список регионов проекта, а затем выведем 10 последних
 * проверок по каждому из них.
 *
 * https://topvisor.ru/api/v2-services/positions_2/get-history/
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
	
	// массив индексов регионов ассоциативный, чтобы избежать повторений индексов
	$regionsArray = [];
	
	foreach($searchers as $searchSystem){
		foreach($searchSystem->regions as $region){
			$regionsArray[$region->name] = $region->index;
		}
	}
	
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
		
		$pageOfPositionsSelector = $positionsSelector->exec();
		
		if($pageOfPositionsSelector->getErrors()) throw new \Exception($pageOfPositionsSelector->getErrorsString());
		
		$resultOfPositionsSelector = $pageOfPositionsSelector->getResult();
		
		//var_dump($resultOfPositionsSelector);
		
		// если не было дат проверок, переходим к следующему региону
		if(!count($resultOfPositionsSelector->existsDates)) continue;
		
		foreach($resultOfPositionsSelector->headers->projects as $project){
			foreach($project->searchers as $searcher){
				$searcherName = $searcher->name;
				foreach($searcher->regions as $region){
					$regionName = $region->name;
					$regionLanguage = $region->lang;
					$regionDevice = $region->device_name;
				}
			}
		}
		
		// вывод шапки таблицы
		echo "<table border=\"1\">";
		echo "<caption> Проект \"$projectName\", регион $regionName, ПС $searcherName (язык $regionLanguage, устройство $regionDevice)";
		echo "<tr>";
		echo "<th>Ключевая фраза</th>";
		
		//var_dump($resultOfPositionsSelector->existsDates);
		
		// перевернём массив дат, чтобы вывести последние
		$dates = array_reverse($resultOfPositionsSelector->existsDates);
		
		// соберём вес поля в массив. чтобы потом выполнить один запрос со всеми полями
		$positionsFields = [];
		for($i = 0; (($i < 10) && ($i < count($dates))); $i++){
			echo "<td>$dates[$i]</td>";
			$positionField = "position:$dates[$i]:$projectId:$region->index";
			array_push($positionsFields, $positionField);
		}
		
		$positionsSelector->setFields($positionsFields);
		$pageOfPositionsSelector = $positionsSelector->exec();
		
		if($pageOfPositionsSelector->getErrors()) throw new \Exception($pageOfPositionsSelector->getErrorsString());
		
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
		echo "</table><br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}