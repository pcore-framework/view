## View

### Установка

```shell
composer require pcore/view
```

```php
use PCore\View\View;
use PCore\HttpMessage\BaseResponse;

$view = new View([
    'cache' => false,
    'path' => __DIR__ . '/../views/',
    'compileDir' => __DIR__ . '/../var/cache/views/'
]);
$renderer = $view->getRenderer();
```

```php
// @param string $template
// @param array $arguments
$body = $renderer->render('index', ['value' => 'Привет мир']);
```

```php
return new BaseResponse(200,
    ['Content-Type' => 'text/html; charset=utf-8'],
    (string)$body
);
```

```html
// views/index.blade.php
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PCore</title>
</head>
<body>
{{ $value }}
</body>
</html>
```

```php
// Выражения вывода {{ }} отправляются через функцию htmlspecialchars PHP для предотвращения XSS-атак
{{ $value }}
```

```php
// Вывод неэкранированных данных
{!! $value !!}
```