<?

/**
 * Для работы с проектом может потребоваться загрузка большого числа запросов.
 * В этом случае поможет метод keywords/import. Он добавит в нужную группу в папке ключевые слова из указаного файла.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/add-import/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include_once('/var/www/include/library/composer_libs/vendor/autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try {
// данные для вставки должны быть в .csv формате
    $CSVFile = file_get_contents('yourCSVFile.csv');
    $importerData = [
        'project_id' => $projectId,
        'keywords' => $CSVFile
    ];

    $importer = new TV\Pen($Session, 'add', 'keywords_2', 'keywords/import');
    $importer->setData($importerData);
    $pageOfImporter = $importer->exec();

// если возникло исключение -> ошибка
    if ($pageOfImporter->getErrors()) throw new \Exception($pageOfImporter->getErrorsString());

    $resultOfImporter = $pageOfImporter->getResult();

    echo "
    Количество отправленных ключевых фраз: $resultOfImporter->countSended.<br>\n
    Количество найденных дублей: $resultOfImporter->countDuplicated.<br>\n
    Количество добавленных ключевых фраз: $resultOfImporter->countAdded.<br>\n
    Количество обновленных ключевых фраз: $resultOfImporter->countChanged.
";
} catch (Exception $e) {
    echo $e->getMessage();
}