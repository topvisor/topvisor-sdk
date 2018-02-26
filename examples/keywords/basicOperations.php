<?

/**
 * Сервис Ключевые фразы создан для удобства работы с папками, группами и ключевыми словами.
 * В данном примере производится добавление новой папки, изменение её имени,
 * новой группы и добавление туда ключевой фразы.
 * https://topvisor.ru/api/v2-services/keywords_2/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

try{
    // добавление папки
    $foldersAdderData = [
        'project_id' => $projectId,
        'name' => 'My first folder'
    ];

    $foldersAdder = new TV\Pen($Session, 'add', 'keywords_2', 'folders');
    $foldersAdder->setData($foldersAdderData);
    $pageOfFoldersAdder = $foldersAdder->exec();

    if($pageOfFoldersAdder->getErrors()) throw new \Exception($pageOfFoldersAdder->getErrorsString());

    $resultOfFoldersAdder = $pageOfFoldersAdder->getResult();
    $folderId = $resultOfFoldersAdder->id;
    $folderName = $resultOfFoldersAdder->name;
    echo "Добавлена папка id$folderId с именем \"$folderName\".<br>";

    // изменим имя папки
    $foldersUpdaterData = [
        'project_id' => $projectId,
        'name' => 'My first renamed folder',
        'id' => $folderId
    ];

    $foldersUpdater = new TV\Pen($Session, 'edit', 'keywords_2', 'folders/rename');
    $foldersUpdater->setData($foldersUpdaterData);
    $resultOfFoldersUpdater = $foldersUpdater->exec();

    if($resultOfFoldersUpdater->getErrors()) throw new \Exception($resultOfFoldersUpdater->getErrorsString());

    $newFolderName = $foldersUpdaterData['name'];
    echo "Имя папки id$folderId изменено на \"$newFolderName\".<br>\n";

    // создадим группу в папке
    $groupsAdderData = [
        'project_id' => $projectId,
        'to_id' => $folderId,
        'name' => "My first group"
    ];

    $groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
    $groupsAdder->setData($groupsAdderData);
    $pageOfGroupsAdder = $groupsAdder->exec();

    if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());

    $resultOfGroupsAdder = $pageOfGroupsAdder->getResult(); // Тип возвращаемого значения - array
    $groupId = $resultOfGroupsAdder[0]->id;
    $groupName = $resultOfGroupsAdder[0]->name;
    echo "В папку id$folderId добавлена группа id$groupId с именем \"$groupName\".<br>\n";

    // добавим ключевое слово в группу
    $keywordsAdderData = [
        'project_id' => $projectId,
        'name' => 'My first added keyword',
        'to_id' => $groupId
    ];

    $keywordsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'keywords');
    $keywordsAdder->setData($keywordsAdderData);
    $pageOfKeywordsAdder = $keywordsAdder->exec();

    if($pageOfKeywordsAdder->getErrors()) throw new \Exception($pageOfKeywordsAdder->getErrorsString());

    $resultOfKeywordsAdder = $pageOfKeywordsAdder->getResult();
    $nameOfAddedKeyword = $resultOfKeywordsAdder->name;
    echo "В группу id$groupId добавлено ключевое слово \"$nameOfAddedKeyword\".";
}catch(Exception $e){
    echo $e->getMessage();
}
