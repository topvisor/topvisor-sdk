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
$importer = new TV\Pen($Session, 'add', 'keywords_2', 'keywords/import');
$importer->setData(['project_id' => $projectId, 'keywords' => "name;tags;target;group_folder_path;group_name\none;2;hello;Папка;Группа\ntwo;3;hello;Папка;Группа"]);
$resultOfImporter = $importer->exec()->getResult();

// вывод результатов
echo "количество отправленных ключевых фраз: $resultOfImporter->countSended<br>количество найденных дублей: $resultOfImporter->countDuplicated
<br>количество добавленных ключевых фраз: $resultOfImporter->countAdded<br>количество обновленных ключевых фраз: $resultOfImporter->countChanged";

