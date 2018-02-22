<?
/**
 * Описание метода keywords/sort для сервиса Ключевые фразы.
 * https://dev.topvisor.ru/api/v2-services/keywords_2/keywords/edit-sort/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

// локальный путь до composer (или topvisorSDK)
include_once('/var/www/include/library/composer_libs/vendor/autoload.php');

// создание сессии
$Session = new TV\Session();

$projectId = 2121417; // id проекта

$sorter = new TV\Pen($Session, 'add', 'keywords_2', 'keywords/sort');
$sorter->setData(['project_id'=>$projectId, 'orders'=>[
    TV\Fields::genOrderData('group_id', 'DESC')
]]);
