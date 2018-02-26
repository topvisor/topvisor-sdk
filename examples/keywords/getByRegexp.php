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
    $keywordsSelectorFilterData = [
        TV\Fields::genFilterData('name', 'REGEXP', ['^фмл'])
    ];
    $keywordsSelectorData = ['project_id' => $projectId];

    $keywordsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'keywords');
    $keywordsSelector->setData($keywordsSelectorData);
    $keywordsSelector->setFilters($keywordsSelectorFilterData);
    $pageOfKeywordsSelector = $keywordsSelector->exec();

    if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());

    $SelectedKeywords = $pageOfKeywordsSelector->getResult();

    echo "<b>Выбранные фразы:</b><br>\n";
    foreach($SelectedKeywords as $keyword){
        echo "\"$keyword->name\"<br>\n";
    }
}catch(Exception $e){
    echo $e->getMessage();
}