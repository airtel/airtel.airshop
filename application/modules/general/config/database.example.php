<?php
/**
 * General database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['general']['hostname'] = 'localhost';
$config['general']['username'] = 'username';
$config['general']['password'] = 'password';
$config['general']['database'] = 'database';
$config['general']['dbprefix'] = '';
$config['general']['dbdriver'] = 'mysqli';


$config['donate_sql_table'] = 'shop_donors';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['general']['pconnect'] = FALSE;
$config['general']['db_debug'] = TRUE;
$config['general']['cache_on'] = FALSE;
$config['general']['cachedir'] = '';
$config['general']['char_set'] = 'utf8';
$config['general']['dbcollat'] = 'utf8_general_ci';