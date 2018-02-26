<?

/**
 * Чтобы подобрать выбрать слова, состветствующие регулярному выражению,
 * воспользуйтесь параметром filters в методе keywords.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/get/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try{
	$keywordsSelectorData = ['project_id' => $projectId];
	$keywordsSelectorFields = ['id', 'name', 'group_name'];
	$keywordsSelectorFilter = [
		TV\Fields::genFilterData('name', 'REGEXP', ['^фмл']),
	];
	
	$keywordsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'keywords');
	$keywordsSelector->setData($keywordsSelectorData);
	$keywordsSelector->setFields($keywordsSelectorFields);
	$keywordsSelector->setFilters($keywordsSelectorFilter);
	
	$pageOfKeywordsSelector = $keywordsSelector->exec();
	
	if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());
	
	$SelectedKeywords = $pageOfKeywordsSelector->getResult();
	
	echo "<b>Выбранные фразы:</b><br>\n";
	echo "<b>id</b>;<b>name</b>;<b>group_name</b><br>\n";
	foreach($SelectedKeywords as $keyword){
		echo "id$keyword->id;\"$keyword->name\";\"$keyword->group_name\"<br>\n";
	}
}catch(Exception $e){
	echo $e->getMessage();
}