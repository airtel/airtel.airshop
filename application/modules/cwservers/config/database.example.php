<?php
/**
 * CWservers database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['cwservers']['hostname'] = 'localhost';
$config['cwservers']['username'] = 'username';
$config['cwservers']['password'] = 'password';
$config['cwservers']['database'] = 'database';
$config['cwservers']['dbprefix'] = '';
$config['cwservers']['dbdriver'] = 'mysqli';


$config['cwservers_sql_table'] = 'cwservers';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['cwservers']['pconnect'] = FALSE;
$config['cwservers']['db_debug'] = TRUE;
$config['cwservers']['cache_on'] = FALSE;
$config['cwservers']['cachedir'] = '';
$config['cwservers']['char_set'] = 'utf8';
$config['cwservers']['dbcollat'] = 'utf8_general_ci';