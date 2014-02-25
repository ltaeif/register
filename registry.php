<?php
if(!defined('_REGEXEC')) die('No direct access allowed.');
//if(!defined('_GaiaEXEC')) die('No direct access allowed.');
/**/
$sites    = array();
$confs    = array();
$dir = (file_exists('sites/') ? 'sites/' : '../sites/');



// general



$_SESSION['root'] = str_replace('\\', '/', dirname(__FILE__));

$_SESSION['url']   = 'http://' . $_SERVER['HTTP_HOST'].'/'.basename(dirname(__FILE__));
// sites values
$_SESSION['sites']['sites'] = $sites;
$_SESSION['sites']['count'] = count($sites);;
$_SESSION['sites']['confs'] = $confs;



// timeout values
$_SESSION['inactive']['time']    = 60;
$_SESSION['inactive']['start']   = true;
$_SESSION['inactive']['life']    = (time() - (isset($_SESSION['inactive']['timeout']) ? $_SESSION['inactive']['timeout'] : time()));
$_SESSION['inactive']['timeout'] = time();

// cron job
$_SESSION['cron']['delay'] = 60; // in seconds
$_SESSION['cron']['time']  = time(); // store the last cron time stamp

// directories
$_SESSION['dir']['AES']         = 'phpAES';
$_SESSION['dir']['adoHelper']   = 'dbHelper';

// user
$_SESSION['user']['pid']      = null;
$_SESSION['user']['name']     = null;
$_SESSION['user']['readOnly'] = null;

// server data
$_SESSION['server']                = $_SERVER;
$_SESSION['server']['OS']          = (php_uname('s') == 'Linux' ? 'Linux' : 'Windows');
$_SESSION['server']['IS_WINDOWS']  = (php_uname('s') == 'Linux' ? false : true);
$_SESSION['server']['PHP_VERSION'] = phpversion();
$_SESSION['server']['token']       = null;
$_SESSION['server']['last_tid']    = null;

// client data
$_SESSION['client']['browser'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['client']['os']      = (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') === false ? 'Linux' : 'Windows');

//Themes
//backoffice
$_SESSION['site']['nametheme']  =  'ace';
$_SESSION['site']['urltheme']  =  $_SESSION['url']  .'/public/ressources/'.$_SESSION['site']['nametheme'].'/' ;

///Front Office
$_SESSION['site']['namethemefront']  =  'front_herokuapp';
$_SESSION['site']['urlthemefront']  =  $_SESSION['url']  .'/public/ressources/'.$_SESSION['site']['namethemefront'].'/' ;