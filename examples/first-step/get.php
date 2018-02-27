<?php

/**
 * Для получения объектов и других типов данных используется оператор get.
 * В данном примере выведем лог банка пользователя (баланс) за последние 3 месяца
 * с сортировкой по дате (DESC) в две таблицы - пополнения и списания.
 * https://dev.topvisor.ru/api/v2/operators/get/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

// создание сессии
$Session = new TV\Session($auth);

try{
	// поля, которые потребуются для таблицы пополнений
	$bankPaymentsFields = ['date', 'status', 'sum'];
	// размер страницы
	$bankLimit = 100;
	// фильтры для сортировки
	$bankFilters = [TV\Fields::genOrderData('date', 'DESC')];
	
	$bankPayments = new TV\Pen($Session, 'get', 'bank_2', 'payments');
	
	// Для любого запроса с оператором get необходимо указывать поля. Подробнее: https://dev.topvisor.ru/api/v2/basic-params/fields/
	$bankPayments->setFields($bankPaymentsFields);
	// Установим количество страниц, которое хотим получать. Подробнее: https://dev.topvisor.ru/api/v2/basic-params/paging/
	$bankPayments->setLimit($bankLimit);
	// Установим порядок следования
	$bankPayments->serOrders($bankFilters);
	
	// выведем шапку таблицы
	echo "<b>дата платежа;статус операции;сумма платежа</b><br>\n";
	
	// будем выбирать по 100 страниц, пока они не закончатся
	do{
		// выполнение запроса
		$pageOfBankPayments = $bankPayments->exec();
		
		if($pageOfBankPayments->getErrors()) throw new \Exception($pageOfBankPayments->getErrorsString());
		
		// сохраним результат выполнения запроса
		$resultOfBankPayments = $pageOfBankPayments->getResult(); // результат - массив с объектами
		
		// для каждого платежа из полученных в результате выведем интересующую информацию
		foreach($resultOfBankPayments as $payment){
			echo "$payment->date;$payment->status;$payment->sum<br>\n";
		}
		
		// Запомним место, начиная с которого будем делать следующую выборку. Подробнее: https://dev.topvisor.ru/api/v2/basic-params/paging/
		$nextOffset = $pageOfBankPayments->getNextOffset();
		if($nextOffset) $bankPayments->setOffset($nextOffset);
	}while($nextOffset);
	
	echo "<br>\n";
	
	// поля, которые потребуются для таблицы списаний
	$bankHistoryFields = ['date', 'target', 'sum'];
	
	$bankHistory = new TV\Pen($Session, 'get', 'bank_2', 'history');
	
	$bankHistory->setFields($bankHistoryFields);
	$bankHistory->setLimit($bankLimit);
	$bankHistory->serOrders($bankFilters);
	
	// выведем шапку таблицы
	echo "<b>дата списания;вид операции;сумма списания</b><br>\n";
	
	do{
		$pageOfBankHistory = $bankHistory->exec();
		
		if($pageOfBankHistory->getErrors()) throw new \Exception($pageOfBankHistory->getErrorsString());
		
		$resultOfBankHistory = $pageOfBankHistory->getResult();
		
		foreach($resultOfBankHistory as $debit){
			echo "$debit->date;$debit->target;$debit->sum<br>\n";
		}
		
		$nextOffset = $pageOfBankHistory->getNextOffset();
		if($nextOffset) $bankHistory->setOffset($nextOffset);
	}while($nextOffset);
}catch(Exception $e){
	echo $e->getMessage();
}