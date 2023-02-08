Kata Component
================

Kata is a simple engine for load templates with extension "tpl.php".

Getting Started
---------------

```
$ composer require elmattprofe/kata
```

```php
require('../vendor/autoload.php');

use ElMattProfe\Component\Kata\Kata;

// If use a Dotenv add vars in .env
putenv('APP_VERSION=1.0');
putenv('APP_URL_BASE=localhost/kataweb/');
putenv('APP_NAME=KataWeb');

// Vars to display in the template
$vars = array(
    "TITLE" => "Welcome",
    "WEB_NAME" => getenv('APP_NAME')
);

// Template is in "resources/views/<section>/<method>", loadView ever at the end code or block code
// $vars contains an array with variable values displaying in the template, 'dev' or 'prod' is state of code
Kata::loadView('landing/index', $vars, 'dev');

```

Resources
---------

 * [Author](https://mattprofe.com.ar)