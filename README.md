# Codememory Routing

## Установка

```
composer require codememory/routing
```

> Обязательно выполняем следующие команды, после уставки пакета

* Создание глоабльной конфигурации, если ее не существует
    * `php vendor/bin/gc-cdm g-config:init`
* Merge всей конфигурации
    * `php vendor/bin/gc-cdm g-config:merge --all`

> Папка **.config** хранит в себе глобальную конфигурацию пакетов **codememory**

После установки, достаточно вызвать методы `__constructStatic` и после него вызвать уже `processAllRoutes`.

> Если вы хотите, чтоб маршруты еще подгружались из файлов. Достаточно вызвать метод **initializingRoutesFromConfig** перед вызовом **processAllRoutes**

## Примеры инциализации
### Инициализация маршрутов, без учета файлов
```php
<?php

use Codememory\Routing\Router;
use Codememory\HttpFoundation\Request\Request;

require_once 'vendor/autoload.php';

Router::__constructStatic(new Request());
Router::processAllRoutes();
```

### Инициализация маршрутов из конфигурации и файлов
```php
<?php

use Codememory\Routing\Router;
use Codememory\HttpFoundation\Request\Request;

require_once 'vendor/autoload.php';

Router::__constructStatic(new Request());
Router::initializingRoutesFromConfig();
Router::processAllRoutes();
```

> Примеры использования маршрутов. Смотрите в файле **.example**