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
	// создание группы А
	$groupsAdderData = [
		'project_id' => $projectId,
		'name' => ['A'],
	];
	
	$groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
	$groupsAdder->setData($groupsAdderData);
	$pageOfGroupsAdder = $groupsAdder->exec();
	
	if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());
	
	$resultOfGroupsAdder = $pageOfGroupsAdder->getResult(); // Тип возвращаемого значения - array
	$addedGroup = $resultOfGroupsAdder[0];
	echo "Группа \"$addedGroup->name\" создана.<br>\n";
	
	$keywordsMoverFilterData = [TV\Fields::genFilterData('name', 'STARTS_WITH', ['а'])];
	$keywordsMoverData = [
		'project_id' => $projectId,
		'to_id' => $addedGroup->id,
	];
	
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