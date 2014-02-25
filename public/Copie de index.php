<?php
//use Slim\Slim;

// Slim
//require '../Slim/Slim.php';
//\Slim\Slim::registerAutoloader();
//use Slim\Extras\Views;
// Setup custom Twig view
//$twigView = new \Slim\Extras\Views\Twig();

//require '../vendor/autoload.php';
//require '../config.php';


  require '../Slim/Slim.php';
  Slim\Slim::registerAutoloader();
  require '../Slim/Extras/Views/Twig.php';
 
  use Slim\Slim;
  use Slim\Extras\Views\Twig as Twig;
 
  $twigView=new Twig;
	
$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => $twigView,
    'templates.path' => '../templates/',
));


require '../Lib/Config.php';

// DB Config
Config::write('db.host', 'localhost');
Config::write('db.port', '');
Config::write('db.basename', 'events');
Config::write('db.user', 'root');
Config::write('db.password', '');

// Project Config
Config::write('path', 'http://localhost:88/slimMVC');



// Automatically load router files
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}

$app->run();

/*
require '../Lib/Config.php';

// DB Config
Config::write('db.host', 'localhost');
Config::write('db.port', '');
Config::write('db.basename', 'events');
Config::write('db.user', 'root');
Config::write('db.password', '');

// Project Config
Config::write('path', 'http://localhost/slimMVC');





$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => $twigView,
    'templates.path' => '../templates/',
));

// Automatically load router files
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}
*/

