<?php


// front group
$app->group('/index', function () use ($app) {


    // User group
    $app->group('/user', function () use ($app) {

        // Get signin
        $app->get('/signin/', function ()  use ($app)  {

            $app->render('front/user/account/signin.twig');

        });

        $app->get('/forgetpassword/', function ()  use ($app)  {

            $app->render('front/user/account/forgetpassword.twig');

        });



        // Get signin
        $app->get('/signout/', function ()  use ($app) {

            $app->render('front/user/account/signout.twig');

        });

    });



});

$app->get('/', function () use ($app) {

    $app->render('front/index.twig');
});


$app->notFound(function () use ($app) {
    $app->render('front/404.twig');
});

// GET index route
$app->get('/index/', function () use ($app) {
   
    $app->render('front/index.twig');
});


$app->get('/index/user/signin/', function () use ($app) {

    $app->render('front/user/account/signin.twig');
});
