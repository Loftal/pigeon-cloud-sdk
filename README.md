# Pigeon Cloud SDK

## Overview
The Pigeon Cloud SDK makes it easier for developers to use Pigeon Cloud API in their PHP code.
[Pigeon Cloud API](https://documenter.getpostman.com/view/8580068/VUjLLnHM)

## Installation
```shell
composer require pigeon-cloud/pigeon-cloud-sdk
```

## Requirement
- PHP 8.0+

## Usage
### .env
```
PIGEON_API_URL=https://xxx.pigeon-cloud.com/api/v1
PIGEON_MASTER_ID=admin
PIGEON_MASTER_PASSWORD=password
```

### example.php
```php
require 'vendor/autoload.php';

use PigeonCloudSdk\PigeonCondition;
use PigeonCloudSdk\PigeonProvider;

// Environment variables need to be loaded.
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pigeonProvider = new PigeonProvider('{table_id}');
$pigeonCondition = new PigeonCondition();
$pigeonCondition->add('{and_or}', '{field}', '{condition}', '{value}');
list($data, $count) = $pigeonProvider->fetch($pigeonCondition);
```

## Features
### fetch
```php
$pigeonProvider = new PigeonProvider('1');
$pigeonCondition = new PigeonCondition();
$pigeonCondition->add('and', 'field__1', 'eq', '1');
$page = 1;
$per_page = 10;
$order = 'id desc';

// fetch list
list($data, $count) = $pigeonProvider->fetch($pigeonCondition, $page, $per_page, $order);

// fetch one
$data = $pigeonProvider->fetchOne($pigeonCondition, $order);

// fetch all
$data_all = $pigeonProvider->fetchAll($pigeonCondition, $order);
```

### insert
```php
$pigeonProvider = new PigeonProvider('1');

// insert
$insert_data = [
    ['field__1' => 'Test 1'],
    ['field__1' => 'Test 2'],
];
$response_data = $pigeonProvider->insert($insert_data);

// insert one
$inserted_id = $pigeonProvider->insertOne(['field__1' => 'Test 1']);
```

### update
```php
$pigeonProvider = new PigeonProvider('1');

// update
$update_data = [
    ['id' => 1, 'field__1' => 'Update 1'],
    ['id' => 2, 'field__1' => 'Update 2'],
];
$response_data = $pigeonProvider->update($update_data);

// update one
$isSucceeded = $pigeonProvider->updateOne(1, ['field__1' => 'Update 1']);
```

### delete
```php
$pigeonProvider = new PigeonProvider('1');

// delete
$response_data = $pigeonProvider->delete([1, 2]);

// delete one
$isSucceeded = $pigeonProvider->delete(1);
```

### file
```php
$pigeonProvider = new PigeonProvider('1');

// delete
$response = $pigeonProvider->file('1');
header('Content-Disposition: attachment; filename="filename.ext"');
echo $response;
exit(0);
```

### debug mode
```php
$pigeonProvider = new PigeonProvider('1');
$pigeonProvider->setDebug(); // Enable Debug mode
$pigeonProvider->fetch();

// The API URL, POST data, and response data will be displayed.
```


## License
 [MIT License](LICENSE) 