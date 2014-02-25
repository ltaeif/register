<?php
session_name('slim_session');
session_start();
session_cache_limiter(false);
define('_REGEXEC', 0);


defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__).'/..'));
	
//|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/library'),
	realpath(APPLICATION_PATH . '/configs'),
	//realpath(APPLICATION_PATH . '/../sites'),
        //addHelperPath(APPLICATION_PATH . '/views/helpers'),
    get_include_path(),
)));





include_once('Mobile_Detect.php');
$mobile = new Mobile_Detect();
$site = (isset($_GET['site']) ? $_GET['site'] : 'default');

$mDebug = false;
if($mobile->isMobile() || $mDebug){
	header('Location: mobile/?site='.$site);
}else{
	
	/**
	 * Startup the registry
	 * This contains SESSION Variables to use in the application
	 * and mobile_detect class is used to detect mobile browsers.
	 */
	include_once('../registry.php');

	/**
	 * set the site using the url parameter site, or default if not given
	 */
	 
	if(file_exists(APPLICATION_PATH.'/sites/' . $site . '/db_site_config.php')){
		include_once(APPLICATION_PATH.'/sites/' . $site . '/db_site_config.php');
	} else {
		$_SESSION['site'] = array('error' => 'Site configuration file not found, Please contact Support Desk. Thanks!');
	}
	
	//var_dump($_SESSION);
	include_once('mvc_app.php');

    //var_dump($_SESSION);
	
	
}

$_SESSION['inactive']['timeout'] = time();