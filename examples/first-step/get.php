<?php

/**
 * Для получения объектов и других типов данных используется оператор get.
 * В данном примере выведем лог банка пользователя (баланс) за последние 3 месяца
 * с сортировкой по дате (от последней к старой) в две таблицы - пополнения и списания.
 *
 * https://topvisor.ru/api/v2/operators/get/
 * */

use Topvisor\TopvisorSDK\V2 as TV;

include(__DIR__.'/../../autoload.php');

$TVSession = new TV\Session(); // создание сессии: https://topvisor.ru/api/v2/sdk-php/session/

try{
	$bankPaymentsFields = ['date', 'status', 'sum']; // запрашиваемые поля для лога пополнений
	$bankLimit = 100; // количестве элементов, возвращаемых одним запросом API
	$bankOrders = [TV\Fields::genOrderData('date', 'DESC')]; // массив с указанием полей для сортировки

	// объект для построения запроса на получение данных: https://topvisor.ru/api/v2/sdk-php/pen/
	$bankPayments = new TV\Pen($TVSession, 'get', 'bank_2', 'payments');
	$bankPayments->setFields($bankPaymentsFields); // https://topvisor.ru/api/v2/basic-params/fields/
	$bankPayments->setLimit($bankLimit); // https://topvisor.ru/api/v2/basic-params/paging/
	$bankPayments->setOrders($bankOrders); // https://topvisor.ru/api/v2/basic-params/orders/

	echo "<b>дата платежа;статус операции;сумма платежа</b><br>\n";

	// будем получать по $bankLimit страниц, пока не получим все
	do{
		// выполнить обращение к API
		$pageOfBankPayments = $bankPayments->exec();

		// метод getErrorsString() вернёт все возникшие ошибки в одной строке
		if($pageOfBankPayments->getErrors()) throw new \Exception($pageOfBankPayments->getErrorsString());

		// результат выполнения запроса, в данном случае это строки лога банка
		$resultOfBankPayments = $pageOfBankPayments->getResult();

		// построчный вывод лога банка
		foreach($resultOfBankPayments as $payment){
			echo "$payment->date;$payment->status;$payment->sum<br>\n";
		}

		// getNextOffset() offset для получениях следующей страницы результатов: https://topvisor.ru/api/v2/basic-params/paging/
		// в случае, если получены все результаты, getNextOffset() вернёт пустое значение
		$nextOffset = $pageOfBankPayments->getNextOffset();
		if($nextOffset) $bankPayments->setOffset($nextOffset);
	}while($nextOffset);

	$bankHistoryFields = ['date', 'target', 'sum']; // запрашиваемые поля для лога списаний

	$bankHistory = new TV\Pen($TVSession, 'get', 'bank_2', 'history');
	$bankHistory->setFields($bankHistoryFields);
	$bankHistory->setLimit($bankLimit);
	$bankHistory->setOrders($bankOrders);

	echo "<br>\n";
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