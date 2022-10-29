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

```php
// Комментарии
{{--  --}}
```

```php
// Расширение макета
@extends('')
// 
@yield('', '')
```

```php
// Необработанный PHP
@php
    $counter = 1;
@endphp
```

```php
// Подключение дочерних шаблонов
@include('app.errors')
```

```php
// Операторы If
@if (count($news) === 1)
   
@elseif (count($news) > 1)
   
@else
    
@endif
```

```php
@unless (Test::test())
   
@endunless
```

```php
// Переменная $test считается пустой
@empty($test)

@endempty
```

```php
// Переменная $test определена и не равна null
@isset($test)

@endisset
```

```php
// Циклы
@for ($i = 0; $i < 10; $i++)
    {{ $i }}
@endfor

@foreach ($news as $i)
    {{ $i->id }}
@endforeach
```

```php
// Операторы Switch
@switch($i)
    @case(1)
        
        @break

    @case(2)
  
        @break

    @default

@endswitch
```

```php
// Наследования шаблонов
@section('sidebar')
    
@show
```