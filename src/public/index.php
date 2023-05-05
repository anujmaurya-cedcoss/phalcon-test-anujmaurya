<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use handler\Listener\Listener;
use component\Locale\Locale;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

include_once(BASE_PATH.'/vendor/autoload.php');
// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH. "/assets/",
        APP_PATH . "/models/",
        APP_PATH . "/controllers/",
        APP_PATH ."/components/"
    ]
);
$loader->registerNamespaces([
    "handler\Listener" => APP_PATH . "/handlers/",
    "handler\Aware" => APP_PATH . "/handlers/",
    "component\Locale" => APP_PATH."/components/",
    "Store\User" => APP_PATH. "/models/",
    "Store\Order" => APP_PATH. "/models/",
    "Store\Product" => APP_PATH. "/models/",
]);

$loader->register();

$container = new FactoryDefault();

$container->set('locale', (new Locale())->getTranslator());

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);


$application = new Application($container);

$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host' => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname' => 'store',
            ]
        );
    }
);
// injecting session in container
$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session->setAdapter($files);
        $session->start();
        return $session;
    }
);

$application = new Application($container);

$eventsManager = $container->get('eventsManager');

$eventsManager->attach(
    'application:beforeHandleRequest',
    new Listener()
);
$container->set('EventsManager', $eventsManager);
$application->setEventsManager($eventsManager);


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
