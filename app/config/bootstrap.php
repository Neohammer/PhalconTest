<?php 

$di = new \Phalcon\DI\FactoryDefault();


$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        'app/controllers/',
        'app/models/',
    ]
);

$loader->register();

return new \Phalcon\Mvc\Application($di);