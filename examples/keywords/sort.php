<?

/**
 * Чтобы изменить порядок ключевых фраз в проекте, воспользуйтесь методом сортировки.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/edit-sort/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include_once('/var/www/include/library/composer_libs/vendor/autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // введите id своего проекта

$orderData = [TV\Fields::genOrderData('name', 'DESC')]; // сортировка по ключевой фразе в обратном алфавитном порядке
$sorterData = ['project_id'=>$projectId];

$sorter = new TV\Pen($Session, 'edit', 'keywords_2', 'keywords/sort');
$sorter->setData($sorterData);
$sorter->serOrders($orderData);
$pageOfSorter = $sorter->exec();

if($pageOfSorter->getErrors()) throw new \Exception($pageOfSorter->getErrorsString());

echo 'Cортировка выполнена успешно!';