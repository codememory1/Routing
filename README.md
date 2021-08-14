# Codememory Routing

## Установка

```
composer require codememory/routing
```

> Обязательно выполняем следующие команды, после уставки пакета

* Создание глобальной конфигурации, если ее не существует
    * `php vendor/bin/gc-cdm g-config:init`
* Merge всей конфигурации
    * `php vendor/bin/gc-cdm g-config:merge --all`

> Папка **.config** хранит в себе глобальную конфигурацию пакетов **codememory**

## Обзор конфигурации
```yaml
# configs/routing.yaml

routing:
  _settings:
    # Path with route files
    pathWithRoutes: App/Routing/Routes/
    
    # Namespace for Software
    softwareNamespace: Codememory\Routing\App\Routing\Software\
    
    # Suffix for file with routes
    routesFileSuffix: null
  
  # List added routes
  _routes:
    # Route name
    test:
      path: 'test/:id' # Route path, with parameter id
      method: 'GET'    # HTTP Method
      class:           # Handler for route
        controller: Codememory\Routing\App\Controllers\TestController
        method: main
      # Regular Expressions for Route Path Parameters
      parameters:
        id: '\d+'
        name: '[a-zA-Z]+'
      # Route software
      software:
        Auth: api    # SoftwareName:MethodName
        CheckIp: api
      schemes:
        - http
        - https
```

После установки, достаточно вызвать методы `__constructStatic` и после него вызвать уже `processAllRoutes`.

> Если вы хотите, чтоб маршруты еще подгружались из файлов. Достаточно вызвать метод **initializingRoutesFromConfig** перед вызовом **processAllRoutes**

## Примеры инициализации
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