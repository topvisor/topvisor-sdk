<?

/**
 * Описание работы с банком.
 * https://dev.topvisor.ru/api/v2-services/bank_2/
 **/

// локальный путь до topvisorSDK
include('../src/topvisorSDK.php');

use TopvisorSDK\V2 as TV;

$Session = new TV\Session(); // создание сессии

$projectId = 2121417; // id проекта

