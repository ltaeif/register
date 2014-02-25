<?php
/**
 * GaiaEHR Configuration file per site
 * MySQL Config
 * Database Init Configuration
 */
//$_SESSION['site'] = array();
$_SESSION['site']['db']['type'] = 'mysql';
$_SESSION['site']['db']['host'] = 'localhost';
$_SESSION['site']['db']['port'] = '3306';
$_SESSION['site']['db']['username'] = 'root';
$_SESSION['site']['db']['password'] = '';
$_SESSION['site']['db']['database'] = 'event';
/**
 * AES Key
 * 256bit - key
 */
$_SESSION['site']['AESkey'] = "tw8nqfc0dg38ze6nepui0ht2bewr58ak";
/**
 * Default site language and theme
 * Check if the localization variable already has a value, if not pass the 
 * default language.
 */
$_SESSION['site']['name'] = 'default';
$_SESSION['site']['default_localization']  = 'fr_All';
$_SESSION['site']['theme'] = 'ext-all-gray';
$_SESSION['site']['timezone'] = 'Europe/Madrid';

$_SESSION['site']['id']    = basename(dirname(__FILE__));
$_SESSION['site']['dir']   = $_SESSION['site']['id'];
$_SESSION['site']['url']   = $_SESSION['url'] . '/sites/' . $_SESSION['site']['dir'];
$_SESSION['site']['path']  = str_replace('\\', '/', dirname(__FILE__));
$_SESSION['site']['temp']['url']  = $_SESSION['site']['url'] . '/temp';
$_SESSION['site']['temp']['path'] = $_SESSION['site']['path'] . '/temp';


//echo $_SESSION['site']['url'] ."<br>";