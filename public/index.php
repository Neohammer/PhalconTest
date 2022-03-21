<?php 

//External librairies autoloader
require __DIR__ . '/../vendor/autoload.php';

use Phalcon\Loader;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

use Phalcon\Db\Adapter\Pdo\Mysql;

use Phalcon\Di\FactoryDefault;

use Phalcon\Escaper;

use Phalcon\Flash\Session as FlashSession;

use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router\Annotations as RouterAnnotations;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine;
use Phalcon\Mvc\View\Engine\Twig;

use Phalcon\Url;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Session\Adapter\Stream as SessionAdapter;



define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');


$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();


// Create a DI
$container = new FactoryDefault();

//Database configuration
$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'project',
            ]
        );
    }
);

//registring view
$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
		
        return $view;
    }
);

//registring url and base path
$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
		
        return $url;
    }
);

//Session
$container->setShared(
    'session',
    function () {
		$session = new SessionManager();
		$files = new SessionAdapter();
		$session->setAdapter($files)->start();
		return $session;
    }
);

//Flash session
$container->set(
	'flash',
	function(  )
	{
		$escaper = new Escaper();
		$flash = new FlashSession($escaper,$this->get('session'));
		
		//managing bootstrap css classes
		$cssClasses = [
			'error'   => 'alert alert-danger',
			'success' => 'alert alert-success',
			'notice'  => 'alert alert-info',
			'warning' => 'alert alert-warning',
		];
		//set custom error class
		$flash->setCssClasses( $cssClasses );
		
		return $flash;
	}

);

$container->set(
	'router',
	function () {
		// Use the annotations router. We're passing false as we don't want the router to add its default patterns
		$router = new RouterAnnotations(false);

		// Read the annotations 
		$router->addResource('Client', '/client');
		$router->addResource('Company', '/company');
		$router->addResource('Provider', '/provider');
		$router->addResource('Product', '/product');
		$router->addResource('Employee', '/employee');
		$router->addResource('Transaction', '/transaction');
		$router->addResource('TransactionCompany', '/transactionCompany');
		$router->addResource('Transactionclient', '/transactionClient');

		return $router;
	}
);

//Create main application
$application = new Application($container);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}