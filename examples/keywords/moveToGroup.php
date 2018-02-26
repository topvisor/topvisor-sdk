<?

/**
 * В ходе составления семантического ядра зачастую приходится перемещать ключевые фразы.
 * В данном примере перемещаются все слова, начинающиеся с буквы А в группу под названием А.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/edit-move/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try{
	$groupsData = [
		'project_id' => $projectId,
		'name' => ['A'],
	];
	$groupsSelectorFields = ['name', 'id', 'project_id'];
	$groupsSelectorFilters = [
		TV\Fields::genFilterData('name', 'EQUALS', ['A']),
	];
	
	$groupsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'groups');
	$groupsSelector->setData($groupsData);
	$groupsSelector->setFields($groupsSelectorFields);
	$groupsSelector->setFilters($groupsSelectorFilters);
	$pageOfGroupsSelector = $groupsSelector->exec();
	
	if($pageOfGroupsSelector->getErrors()) throw new \Exception($pageOfGroupsSelector->getErrorsString());

	$resultOfGroupsSelector = $pageOfGroupsSelector->getResult();
	
	// если не нашлось группы с именем А, создадим её
	if(!count($resultOfGroupsSelector)){
		$groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
		$groupsAdder->setData($groupsData);
		$pageOfGroupsAdder = $groupsAdder->exec();
		
		if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());
		
		$resultOfGroupsAdder = $pageOfGroupsAdder->getResult(); // тип возвращаемого значения - array
		$addedGroup = $resultOfGroupsAdder[0];
		echo "Группа $addedGroup->id создана.<br>\n";
		$group = $addedGroup;
	}else{
		$group = $resultOfGroupsSelector[0];
		echo "Группа id$group->id с именем 'A' уже существовала<br>\n";
	}
	
	$keywordsMoverData = [
		'project_id' => $projectId,
		'to_id' => $group->id,
	];
	$keywordsMoverFilterData = [TV\Fields::genFilterData('name', 'STARTS_WITH', ['а'])];
	
	$keywordsMover = new TV\Pen($Session, 'edit', 'keywords_2', 'keywords/move');
	$keywordsMover->setData($keywordsMoverData);
	$keywordsMover->setFilters($keywordsMoverFilterData);
	$pageOfKeywordsMover = $keywordsMover->exec();
	
	if($pageOfKeywordsMover->getErrors()) throw new \Exception($pageOfKeywordsMover->getErrorsString());
	
	$resultOfKeywordsMover = $pageOfKeywordsMover->getResult();
	echo "Перемещено $resultOfKeywordsMover ключевых слов.";
}catch(Exception $e){
	echo $e->getMessage();
}