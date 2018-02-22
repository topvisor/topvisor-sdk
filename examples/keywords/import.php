<?
/**
 * Для работы с проектом может потребоваться загрузка большого числа запросов.
 * В этом случае поможет метод keywords/import. Он добавит в нужную группу в папке ключевые слова, которые Вы укажете.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/add-import/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

// локальный путь до composer (или topvisorSDK)
include_once('/var/www/include/library/composer_libs/vendor/autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // id проекта

// создание объекта TV\Pen, установка данных, выполнение запроса
$CSVFile = "name;tags;target;group_folder_path;group_name\nkeyword1;2;https://your/target;Папка;Группа\nkeyword2;3;https://your/target;Папка;Группа";
$importerData = ['project_id' => $projectId, 'keywords' => $CSVFile];
$importer = new TV\Pen($Session, 'add', 'keywords_2', 'keywords/import');
$importer->setData($importerData);
$importedPage = $importer->exec();

// если возникло исключение -> ошибка
if($importedPage->getErrors()) throw new \Exception($importedPage->getErrorsString());

$resultOfImporter = $importedPage->getResult();

// вывод результатов
echo "количество отправленных ключевых фраз: $resultOfImporter->countSended<br>количество найденных дублей: $resultOfImporter->countDuplicated
<br>количество добавленных ключевых фраз: $resultOfImporter->countAdded<br>количество обновленных ключевых фраз: $resultOfImporter->countChanged";