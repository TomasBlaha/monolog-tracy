kucera/monolog-extensions
======
A set of [Monolog](http://github.com/seldaek/monolog) extensions.

Installation
------------

Using  [Composer](http://getcomposer.org/):

```sh
$ composer require kucera/monolog-extensions:~0.1.0
```


Blue Screen Handler
------------
Converts your exception reports into beautiful and clear html files using [Tracy](https://github.com/nette/tracy).

[![Uncaught exception rendered by Tracy](http://nette.github.io/tracy/images/tracy-exception.png)](http://nette.github.io/tracy/tracy-exception.html)

### Tell me how!
Just push the handler into the stack.
```php
use Kucera\Monolog\Factory;

$logger = new Monolog\Logger('channel');

$logDirectory = __DIR__ . '/log';
$logger->pushHandler(Factory::blueScreenHandler($logDirectory));
```
… Profit!
```php
$logger->critical('Exception occured!', array(
    'exception' => new Exception(),
));
```

#### Tips
You don't have to use the factory method, handler is instantiable on its own. `Kucera\Monolog\Factory::blueScreen()` might come in handy then.
