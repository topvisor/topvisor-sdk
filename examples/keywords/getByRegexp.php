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
    $selectorFilterData = [
        TV\Fields::genFilterData('name', 'REGEXP', ['^фмл'])
    ];
    $selectorData = ['project_id' => $projectId];

    $selector = new TV\Pen($Session, 'get', 'keywords_2', 'keywords');
    $selector->setData($selectorData);
    $selector->setFilters($selectorFilterData);
    $pageOfSelector = $selector->exec();

    if($pageOfSelector->getErrors()) throw new \Exception($pageOfSelector->getErrorsString());

    $resultOfSelector = $pageOfSelector->getResult();

    echo "<b>Выбранные фразы:</b><br>\n";
    foreach($resultOfSelector as $res){
        echo "$res->name<br>\n";
    }
}catch(Exception $e){
    echo $e->getMessage();
}