# Kata Component

Kata is a simple engine for load templates with extension "tpl.php".

## Getting Started

To install Kata, simply require it using Composer:

```bash
$ composer require elmattprofe/kata
```
## PHP code for load template

index.php

```php
require('../vendor/autoload.php');

use ElMattProfe\Component\Kata\Kata;

// If use a Dotenv add vars in .env
putenv('APP_VERSION=1.0');
putenv('APP_URL_BASE=localhost/kataweb/');
putenv('APP_NAME=KataWeb');

// Vars to display in the template
$vars = array(
    "SECTION" => "Welcome",
    "WEB_NAME" => getenv('APP_NAME')
);

// Template is in "resources/views/<section>/<method>", loadView ever at the end code or block code
// $vars contains an array with variable values displaying in the template, 'dev' or 'prod' is state of code, if no define defualt is 'dev'
Kata::loadView('landing/index', $vars, 'dev');
```
## Sample template

Put in directory "resources\views\landing" with name "index.tpl.php"

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{APP_NAME}}-{{SECTION}}</title>
    <link rel="stylesheet" type="text/css" href="{{APP_URL_BASE}}/resources/css/estilos.css?v={{APP_VERSION}}">
</head>
<body>
    {{WEB_NAME}}<br>
    <a href="{{APP_URL_BASE}}/login">Iniciar Sesión</a>
</body>
</html>
```
> **Note**
> APP_NAME, APP_URL_BASE and APP_VERSION are defined by default in the code, their values are obtained from the environment variables, therefore it is not necessary to pass their values by parameter in loadView.

# Other functions includes in Kata

## Extends

Kata can include another template within a template, this is extremely useful, it helps to simplify the code in the templates by not having to repeat common sections such as the footer, header and nav.

Place the following code in the template that will include another template

resources/views/landing/index.tpl.php
```html
@extends('header')

    <content>
        <h1>{{SECTION}}</h1>
    </content>

@extends('footer')
```

resources/views/header.tpl.php
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{APP_NAME}}-{{SECTION}}</title>
    <link rel="stylesheet" type="text/css" href="{{APP_URL_BASE}}/resources/css/estilos.css?v={{APP_VERSION}}">
</head>
<body>
```

resources/views/footer.tpl.php
```html
    <footer>
        The web: {{APP_NAME}}
    </footer>
</body>
</html>
```

## Yield

Yields are a way of creating variable code blocks within a template that will be called with extends, the content that will be replaced will be enclosed within the @section('name-yield') and @endsection('name-yield') directives.

Place the following code in the template that will include another template

resources/views/landing/index.tpl.php
```html
@extends('web')

@section('content')
    <h1>{{SECTION}}</h1>
@endsection('content')

@section('footer')
    @extends('footer')
@endsection('footer')
```

resources/views/web.tpl.php
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{APP_NAME}}-{{SECTION}}</title>
    <link rel="stylesheet" type="text/css" href="{{APP_URL_BASE}}/resources/css/estilos.css?v={{APP_VERSION}}">
</head>
<body>
    <content>
        @yield('content')
    </content>

    <footer>
        @yield('footer')
    </footer>
</body>
</html>
```

resources/views/footer.tpl.php
```html
    The web: {{APP_NAME}}
```

## Conditional block

It depends on a variable, if it exists then the content of that block will be rendered and displayed on the screen.

resources/views/landing/index.tpl.php
```html
@extends('web')

@section('content')
    @if('SECTION')
    <h1>{{SECTION}}</h1>
    @endif

    @if('DESCRIPTION')
    <p>This website is a demonstration of the power of Kata.</p>
    @endif
@endsection('content')

@section('footer')
    @extends('footer')
@endsection('footer')
```

## Resources

 * [Author](https://mattprofe.com.ar)