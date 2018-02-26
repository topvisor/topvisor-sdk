<?

/**
 * Для работы с проектом может потребоваться загрузка большого числа запросов.
 * В этом случае поможет метод keywords/import. Он добавит в нужную группу в папке ключевые слова из указаного файла.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/add-import/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try{
    // данные для вставки должны быть в .csv формате
    $CSVFile = file_get_contents(__DIR__.'/yourCSVFile.csv');
    $keywordsImporterData = [
        'project_id' => $projectId,
        'keywords' => $CSVFile
    ];

    $keywordsImporter = new TV\Pen($Session, 'add', 'keywords_2', 'keywords/import');
    $keywordsImporter->setData($keywordsImporterData);
    $pageOfKeywordsImporter = $keywordsImporter->exec();

    if($pageOfKeywordsImporter->getErrors()) throw new \Exception($pageOfKeywordsImporter->getErrorsString());

    $resultOfKeywordsImporter = $pageOfKeywordsImporter->getResult();

    echo "
        Количество отправленных ключевых фраз: $resultOfKeywordsImporter->countSended.<br>\n
        Количество найденных дублей: $resultOfKeywordsImporter->countDuplicated.<br>\n
        Количество добавленных ключевых фраз: $resultOfKeywordsImporter->countAdded.<br>\n
        Количество обновленных ключевых фраз: $resultOfKeywordsImporter->countChanged.
    ";
}catch(Exception $e){
    echo $e->getMessage();
}