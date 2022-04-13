<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
// session_start();
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Http\Response;
use Phalcon\Http\Response\Headers;
use Phalcon\Http\Cookie;
use Phalcon\Di;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
// use Phalcon\Config;
use Phalcon\Config\ConfigFactory;

use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Cache;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\CacheFactory;
// use Phalcon\Cache\AdapterFactory;
// use Phalcon\Storage\SerializerFactory;


// $config = new Config([]);
// $filename = '../app/etc/config.php';
$factory = new ConfigFactory();
// $config = $factory->newInstance('php',$filename);
// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        APP_PATH . "/listners/",
    ],
    
);
$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . "/Component"
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);



$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers",
        APP_PATH . "/models/"
    ]);


$loader->registerNamespaces(
    [
        'App\Listeners' => APP_PATH . '/listners'
    ]
    );
    $loader->register();

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);
$container->set(
    'date',
    function () {
        // $url = new date();
        // $url->setBaseUri('/');
        return date('Y:M:D:H:M:S');
    }
);



$container->set(
    'db',
    function () {
   

        return new Mysql(
            [
                'host'=> 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'spotify',
                ]
            );
        }
);
$application = new Application($container);




// $container->set(
//     'config',
//     $config,
//     true

// );

// $eventsManager = new EventsManager();

// $eventsManager->attach(
//     'main',
//     new MainListner()
   
// );
// $eventManager->attach(
//     'application:beforeHandRequest',
//     new App\listners\NotificationListner()

// );

// $container->set(
//     'eventsManager',
//     function () use($eventsManager) {
//         return $eventsManager;

//     }
// );

// $eventsManager->attach(
//     'application:beforeHandleRequest',
//     new App\Listeners\NotificationListner()
// );

// $application->setEventsManager($eventsManager);

// $di->setShared(
//     'session',
//     function(){
//         $session = new Session();
//         $session->start();
//         return $session;
//     }
// );

// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );


$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->setName('token')
            ->start();

        return $session;
    }
);
// $di->set( 
//     "cookies", function () { 
//        $cookies = new Cookies();  
//        $cookies->useEncryption(false);  
//        return $cookies; 
//     } 
//  ); 
$container->setShared(
    "cache",
    function () {
        $options = [
            'defaultSerializer' => 'Json',
            'lifetime'          => 7200,
        ];

        $serializerFactory = new SerializerFactory();
        $adapterFactory    = new AdapterFactory(
            $serializerFactory,
            $options
        );

        $cacheFactory = new CacheFactory($adapterFactory);

        return $cacheFactory->newInstance('apcu');
    }
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );
    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
