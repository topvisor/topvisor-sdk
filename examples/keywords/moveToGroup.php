<?

/**
 * В ходе составления семантического ядра зачастую приходится перемещать ключевые фразы.
 * В данном примере перемещаются все слова, начинающиеся с буквы А в группу под названием А.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/edit-move/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include_once('/var/www/include/library/composer_libs/vendor/autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

// создание группы А
$groupsAdderData = [
    'project_id' => $projectId,
    'name' => ['A']
];

$groupsAdder = new TV\Pen($Session, 'add', 'keywords_2', 'groups');
$groupsAdder->setData($groupsAdderData);
$pageOfGroupsAdder = $groupsAdder->exec(); // Тип возвращаемого значения - array

if($pageOfGroupsAdder->getErrors()) throw new \Exception($pageOfGroupsAdder->getErrorsString());

$resultOfGroupsAdder = $pageOfGroupsAdder->getResult();
$groupName = $resultOfGroupsAdder[0]->name;
echo "Группа $groupName создана,<br>\n";

$groupId = $resultOfGroupsAdder[0]->id;
$moverFilterData = [TV\Fields::genFilterData('name', 'STARTS_WITH', ['а'])];
$moverData = [
    'project_id' => $projectId,
    'to_id' => $groupId
];

$mover = new TV\Pen($Session, 'edit', 'keywords_2', 'keywords/move');
$mover->setData($moverData);
$mover->setFilters($moverFilterData);
$pageOfMover = $mover->exec();

if($pageOfMover->getErrors()) throw new \Exception($pageOfMover->getErrorsString());

$resultOfMover = $pageOfMover->getResult();
echo "Перемещено $resultOfMover ключевых слов.";