<?
/**
 * Описание работы с ядром.
 * Ссылка на документацию: https://topvisor.ru/api/v2-services/keywords_2/
**/

// локальный путь до topvisorSDK
include('../src/topvisorSDK.php');

use TopvisorSDK\V2 as TV;

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // id проекта

$folderAdder = new TV\Pen($Session, 'add', 'keywords_2', 'folders'); // создание запроса
$folderAdder -> setData(['project_id' => $projectId, 'name' => 'new folder']); //
$pageOfAddedFolder = $folderAdder->exec(); // выполнение запроса

// в случае успешного выполнения продолжим операции
if (!$pageOfAddedFolder->getErrors()){
    $folderId = $pageOfAddedFolder->getResult()->id;

    // изменим имя папки
    $folderUpdater = new TV\Pen($Session, 'edit', 'keywords_2', 'folders/rename');
    $folderUpdater -> setData(['project_id'=>$projectId, 'name'=>'I can rename folder']);
    $folderUpdater ->setFilters([
        TV\Fields::genFilterData('id', 'EQUALS', [$folderId])
    ]);
    $folderUpdater -> exec();

    // создадим группу в папке
    $groupAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
    $groupAdder -> setData(['project_id'=>$projectId, 'to_id'=>$folderId]);
    $pageOfAddedGroup = $groupAdder->exec();
    $groupId = $pageOfAddedGroup->getResult()[0]->id;

    // добавим ключевое слово в группу
    if (!$pageOfAddedGroup->getErrors()) {
        $keywordAdder = new TV\Pen($Session, 'add', 'keywords_2', 'keywords');
        $keywordAdder->setData(['project_id' => $projectId, 'name' => 'new keyword', 'to_id' => $groupId]);
        $keywordAdder->exec();
    }
}