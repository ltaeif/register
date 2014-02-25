<?php
//https://github.com/jeremykendall/flaming-archer

  require '../Slim/Slim.php';
  Slim\Slim::registerAutoloader();
  require '../Slim/Extras/Views/Twig.php';
 
  use Slim\Slim;
  use Slim\Extras\Views\Twig as Twig;
 
  $twigView=new Twig;
  //SET the Session to Twig
  $twigView->setData(array(
    'SESSION' => $_SESSION
  ));
  
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

$app->run();

?>
