<?

/**
 * Чтобы изменить порядок ключевых фраз в проекте, воспользуйтесь методом сортировки.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/edit-sort/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try{
    // выведем содержимое группы "Школа"
    $groupName = 'Школа'; // введите имя своей группы
    $groupFilter = [TV\Fields::genFilterData('group_name', 'EQUALS', [$groupName])];
    $keywordsSelectorData = ['project_id' => $projectId];

    $keywordsSelector = new TV\Pen($Session, 'get', 'keywords_2', 'keywords');
    $keywordsSelector->setData($keywordsSelectorData);
    $keywordsSelector->setFilters($groupFilter);
    $pageOfKeywordsSelector = $keywordsSelector->exec();

    if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());

    $selectedKeywords = $pageOfKeywordsSelector->getResult();
    echo "<b>Содержимое группы \"Школа\" до сортировки:</b><br>\n";
    foreach ($selectedKeywords as $keyword) {
        echo "$keyword->name<br>\n";
    }

    $keywordsOrderData = [TV\Fields::genOrderData('name', 'DESC')]; // сортировка по ключевой фразе в обратном алфавитном порядке
    $keywordsSorterData = ['project_id' => $projectId];

    $keywordsSorter = new TV\Pen($Session, 'edit', 'keywords_2', 'keywords/sort');
    $keywordsSorter->setData($keywordsSorterData);
    $keywordsSorter->serOrders($keywordsOrderData);
    $keywordsSorter->setFilters($groupFilter);
    $pageOfKeywordsSorter = $keywordsSorter->exec();

    if($pageOfKeywordsSorter->getErrors()) throw new \Exception($pageOfKeywordsSorter->getErrorsString());
    
    $direction = ($keywordsOrderData[0]['direction']=='ASC') ? 'по возрастанию' : 'по убыванию';
    echo "<b>Cортировка группы \"$groupName\" $direction выполнена успешно!</b><br>\n";

    $pageOfKeywordsSelector = $keywordsSelector->exec();

    if($pageOfKeywordsSelector->getErrors()) throw new \Exception($pageOfKeywordsSelector->getErrorsString());

    $selectedKeywords = $pageOfKeywordsSelector->getResult();

    echo "<b>Содержимое группы \"Школа\" после сортировки:</b><br>\n";
    foreach ($selectedKeywords as $keyword) {
        echo "$keyword->name<br>\n";
    }
}catch(Exception $e){
    echo $e->getMessage();
}