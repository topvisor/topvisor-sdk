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
	// получение списка регионов проекта (regions_indexes)
	$regions_indexes = [];
	
	$regionsSelectorData = [
		'id' => $projectId,
		'show_searchers_and_regions' => 1,
	];
	
	$regionsSelector = new TV\Pen($TVSession, 'get', 'projects_2', 'projects');
	
	$regionsSelector->setData($regionsSelectorData);
	$pageOfRegionsSelector = $regionsSelector->exec();
	
	if($pageOfRegionsSelector->getErrors()) throw new \Exception($pageOfRegionsSelector->getErrorsString());
	
	$projects = $pageOfRegionsSelector->getResult();
	if(!$projects) return; // проекта нет
	
	// единственно запрашиваемый проект
	$project = $projects[0];
	
	foreach($project->searchers as $searcher){
		foreach($searcher->regions as $region){
			$regions_indexes[] = $region->index;
		}
	}
	
	// получаем позиции для всех регионов проекта
	$positionSelectorData = [
		'project_id'        => $projectId,
		'regions_indexes' => $regions_indexes,
		'date1' => '0001-01-01',
		'date2' => date('Y-m-d'),
		'show_exists_dates' => 1,
		'show_headers' => 1,
		'count_dates' => 10,
	];
	
	$positionsSelector = new TV\Pen($TVSession, 'get', 'positions_2', 'history');
	$positionsSelector->setData($positionSelectorData);
	$pageOfPositionsSelector = $positionsSelector->exec();
	
	if($pageOfPositionsSelector->getErrors()) throw new \Exception($pageOfPositionsSelector->getErrorsString());
	
	$dates = $pageOfPositionsSelector->getResult()->headers->dates;
	$projects = $pageOfPositionsSelector->getResult()->headers->projects;
	$keywords = $pageOfPositionsSelector->getResult()->keywords;
	
	echo '<table border="1">';
	echo '<tr>';
	echo '<th>Ключевое слово</th>';
	foreach($dates as $date){
		echo "<th>$date</th>";
	}
	echo '</tr>';
	
	foreach($projects as $project){
		$projectName = $project->name;
		foreach($project->searchers as $searcher){
			foreach($searcher->regions as $searcherRegion){
				echo '<tr>';
				echo '<td colspan="11" align="center">';
				echo "Проект \"$projectName\", $searcherRegion->name, $searcher->name ($searcherRegion->lang, $searcherRegion->device_name)";
				echo '</td></tr>';
				foreach($keywords as $keyword){
					echo '<tr>';
					echo "<td>$keyword->name</td>";
					for($i = 0; $i < 10; $i++){
						$positionField = "$dates[$i]:$project->id:$searcherRegion->index";
						if(isset($keyword->positionsData->$positionField->position)){
							$pos = ($keyword->positionsData->$positionField->position)?$keyword->positionsData->$positionField->position:'--';
						}else{
							$pos = '';
						}
						echo "<td>$pos</td>";
					}
					echo '</tr>';
				}
			}
		}
		echo '</table>';
	}
}catch(Exception $e){
	echo $e->getMessage();
}