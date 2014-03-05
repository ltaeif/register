<?php

// GET index route
// Admin Home.

$app->get('/admin/', function() use ($app) {
 $app->render('admin/user/account/signin.twig');
});

$app->get('/admin/user/account/', function() use ($app) {
 $app->render('admin/user/account/index.twig');
});





// Admin Login.
$app->get('/admin/user/account/signin', function() use ($app) {

    $app->render('admin/user/account/signin.twig');
});

// Admin Signup.
$app->get('/admin/user/account/signup', function() use ($app) {

    $app->render('admin/user/account/signup.twig');
});


// Admin Forgetpassword.
$app->get('/admin/user/account/forgetpassword', function() use ($app) {

    if($app->request())
    {
        $data=array('1','2');
        $app->render('admin/user/account/forgetpassword.twig',array('data'=>$data));
    }
    else
    {

        $app->render('admin/user/account/forgetpassword.twig');
    }

});




// Admin Signout.
$app->get('/admin/user/account/signout',$authCheck, function() use ($app) {

    $app->render('admin/user/account/signout.twig');
});


// Admin Dashboard.
$app->get('/admin/dashboard/',$authCheck, function() use ($app) {

    $app->render('admin/dashboard/index.twig');
});






 
// Admin Add - POST.
$app->post('/admin/add', function() use ($app) {
 
 $hello='lol';
 $app->render('admin_layout.php', array('hello' => $hello));
});
 
// Admin Edit.
$app->get('/admin/edit/(:id)', function($id) use ($app) {
 
 $hello='lol';
 $app->render('admin_layout.php', array('hello' => $hello));
});
 
// Admin Edit - POST.
$app->post('/admin/edit/(:id)', function($id) use ($app) {
 
 $hello='lol';
 $app->render('admin_layout.php', array('hello' => $hello));
});
 
// Admin Delete.
$app->get('/admin/delete/(:id)', function($id) use ($app) {
 
 $hello='lol';
 $app->render('admin_layout.php', array('hello' => $hello));
});