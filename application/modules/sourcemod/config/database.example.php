<?php
/**
 * Sourcemod database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['sourcemod']['hostname'] = 'localhost';
$config['sourcemod']['username'] = 'username';
$config['sourcemod']['password'] = 'password';
$config['sourcemod']['database'] = 'database';
$config['sourcemod']['dbprefix'] = 'sb_';
$config['sourcemod']['dbdriver'] = 'mysqli';


$config['sql_table'] = 'shop_services';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['sourcemod']['pconnect'] = FALSE;
$config['sourcemod']['db_debug'] = TRUE;
$config['sourcemod']['cache_on'] = FALSE;
$config['sourcemod']['cachedir'] = '';
$config['sourcemod']['char_set'] = 'utf8';
$config['sourcemod']['dbcollat'] = 'utf8_general_ci';