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
    $keywordsOrderData = [TV\Fields::genOrderData('name', 'DESC')]; // сортировка по ключевой фразе в обратном алфавитном порядке
    $keywordsSorterData = ['project_id' => $projectId];

    $keywordsSorter = new TV\Pen($Session, 'edit', 'keywords_2', 'keywords/sort');
    $keywordsSorter->setData($keywordsSorterData);
    $keywordsSorter->serOrders($keywordsOrderData);
    $pageOfKeywordsSorter = $keywordsSorter->exec();

    if($pageOfKeywordsSorter->getErrors()) throw new \Exception($pageOfKeywordsSorter->getErrorsString());

    echo 'Cортировка выполнена успешно!';
}catch(Exception $e){
    echo $e->getMessage();
}