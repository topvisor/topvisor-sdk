<?php

/**
 * Описание работы с банком.
 * https://dev.topvisor.ru/api/v2-services/bank_2/
 **/

// локальный путь до topvisorSDK
include('../src/topvisorSDK.php');

use TopvisorSDK\V2 as TV;

$Session = new TV\Session(); // создание сессии

$projectId = 2121417; // id проекта

// создание запроса
$balanceGetter = new TV\Pen($Session, 'get', 'bank_2', 'balance');
$balancePage = $balanceGetter->exec();
echo $balancePage->getResult();

$paymentsGetter = new TV\Pen($Session, 'get', 'bank_2', 'payments');
$paymentsPage = $paymentsGetter->exec();

foreach ($paymentsPage->getResult() as $string){
    echo "ID операции: $string->id Сумма пополнения: $string->sum <br>";
}