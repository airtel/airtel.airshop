<?php
/**
 * Wow database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['wow']['hostname'] = 'localhost';
$config['wow']['username'] = 'username';
$config['wow']['password'] = 'password';
$config['wow']['database'] = 'database';
$config['wow']['dbprefix'] = '';
$config['wow']['dbdriver'] = 'mysqli';


$config['sql_table'] = 'shop_services_wow';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['wow']['pconnect'] = FALSE;
$config['wow']['db_debug'] = TRUE;
$config['wow']['cache_on'] = FALSE;
$config['wow']['cachedir'] = '';
$config['wow']['char_set'] = 'utf8';
$config['wow']['dbcollat'] = 'utf8_general_ci';