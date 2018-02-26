<?

use Topvisor\TopvisorSDK\V2 as TV;

/**
 * Сервис Ключевые фразы создан для удобства работы с папками, группами и ключевыми словами.
 * В данном примере производится добавление новой папки, изменение её имени,
 * новой группы и добавление туда ключевых слов.
 * https://topvisor.ru/api/v2-services/keywords_2/
 * */

include_once('/var/www/include/library/composer_libs/vendor/autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

// добавление папки
$foldersAdderData = [
    'project_id' => $projectId,
    'name' => 'new folder'
];

$foldersAdder = new TV\Pen($Session, 'add', 'keywords_2', 'folders');
$foldersAdder->setData($foldersAdderData);
$pageOfFoldersAdder = $foldersAdder->exec();

// если возникло исключение -> ошибка
if($pageOfFoldersAdder->getErrors()) throw new \Exception($pageOfFoldersAdder->getErrorsString());
echo "Добавлена папка с id: {$pageOfFoldersAdder->getResult()->id}.<br>";

// изменим имя папки
$folderId = $pageOfFoldersAdder->getResult()->id;
$foldersUpdaterData = [
    'project_id' => $projectId,
    'name' => 'I can rename folder',
    'id' => $folderId
];

$foldersUpdater = new TV\Pen($Session, 'edit', 'keywords_2', 'folders/rename');
$foldersUpdater->setData($foldersUpdaterData);
$resultOfFoldersUpdater = $foldersUpdater->exec();

if($resultOfFoldersUpdater->getErrors()) throw new \Exception($resultOfFoldersUpdater->getErrorsString());
echo 'Имя папки изменено.<br>';

// создадим группу в папке
$groupsAdderData = [
    'project_id' => $projectId,
    'to_id' => $folderId
];

$groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
$groupsAdder->setData($groupsAdderData);
$pageOfGroupsAdder = $groupsAdder->exec(); // Тип возвращаемого значения - array

// если возникло исключение -> ошибка
if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());
echo "Добавлена группа с id: {$pageOfGroupsAdder->getResult()[0]->id}.<br>";

// добавим ключевое слово в группу
$groupId = $pageOfGroupsAdder->getResult()[0]->id;
$keywordsAdderData = [
    'project_id' => $projectId,
    'name' => 'new keyword',
    'to_id' => $groupId
];

$keywordsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'keywords');
$keywordsAdder->setData($keywordsAdderData);
$pageOfKeywordsAdder = $keywordsAdder->exec();

if($pageOfKeywordsAdder->getErrors()) throw new \Exception($pageOfKeywordsAdder->getErrorsString());

$nameOfAddedKeyword = $pageOfKeywordsAdder->getResult()->name;
echo "В группу добавлено ключевое слово: $nameOfAddedKeyword.";
