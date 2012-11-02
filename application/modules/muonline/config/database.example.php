<?php
/**
 * Muonline database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['muonline']['hostname'] = 'localhost';
$config['muonline']['username'] = 'muonline';
$config['muonline']['password'] = 'password';
$config['muonline']['database'] = 'database';
$config['muonline']['dbprefix'] = '';
$config['muonline']['dbdriver'] = 'mssql';


/**
 * Datubāzes tabulu uzstādījumi
 */
$config['sql_params'] = array(
    
    'auth_params' => array(
        'table_name' => 'MEMB_INFO',
        'username_field' => 'memb___id',
    ),
    
    'unban_params' => array(
        'table_name' => 'MEMB_INFO',
    ),
    
    'credits_params' => array(
        'table_name' => 'MEMB_CREDITS',
    ),
    
    'cspoints_params' => array(
        'table_name' => 'MEMB_INFO',
    ),
    
    'skillbooster_params' => array(
        'table_name' => 'Character',
    ),
    
    'gmaccess_params' => array(
        'table_name' => 'Character',
        'admin_code' => 32,
    ),
);


$config['sql_table'] = 'shop_services';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['muonline']['pconnect'] = FALSE;
$config['muonline']['db_debug'] = TRUE;
$config['muonline']['cache_on'] = FALSE;
$config['muonline']['cachedir'] = '';
$config['muonline']['char_set'] = 'utf8';
$config['muonline']['dbcollat'] = 'utf8_general_ci';