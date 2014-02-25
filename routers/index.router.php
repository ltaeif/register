<?php

// GET index route
$app->get('/index/', function () use ($app) {
   
    $app->render('front/index.twig');
});


$app->get('/index/user/signin/', function () use ($app) {

    $app->render('front/user/account/signin.twig');
});
