# topvisor-sdk

SDK для [Topvisor API](https://topvisor.com/ru/api/v2/sdk-php/)

# Установка

Используйте [composer](https://getcomposer.org/) для установки

composer.json
```json
{
    "require": {
        "topvisor/topvisor-sdk": "~2.3"
    }
}
```

# Пример использования библиотеки

Примеры использования библиотеки расположены в папке [examples](https://github.com/topvisor/topvisor-sdk/tree/master/examples). 

Для работы с api требуется авторизоваться.

```php
// вместо "..." необходимо прописать путь до файла autoload
include(__DIR__.'/../../vendor/autoload.php');

use Topvisor\TopvisorSDK\V2 as TV;

$userId = ''; // подставьте ваш user id
$accessToken = ''; // подставьте ваш API ключ

// используется для дальнейших запросов к api
$session = new TV\Session([
    'userId' => $userId, 
    'accessToken' => $accessToken
]); 
```
