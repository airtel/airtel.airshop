<?php
/**
 * Amx database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['amx']['hostname'] = 'localhost';
$config['amx']['username'] = 'username';
$config['amx']['password'] = 'password';
$config['amx']['database'] = 'database';
$config['amx']['dbprefix'] = 'amx_';
$config['amx']['dbdriver'] = 'mysqli';


$config['sql_table'] = 'shop_services';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['amx']['pconnect'] = FALSE;
$config['amx']['db_debug'] = TRUE;
$config['amx']['cache_on'] = FALSE;
$config['amx']['cachedir'] = '';
$config['amx']['char_set'] = 'utf8';
$config['amx']['dbcollat'] = 'utf8_general_ci';