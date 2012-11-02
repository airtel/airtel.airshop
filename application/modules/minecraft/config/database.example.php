<?php
/**
 * Minecraft database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Datubāzes konekcijas parametri. 
 */
$config['minecraft']['hostname'] = 'localhost';
$config['minecraft']['username'] = 'username';
$config['minecraft']['password'] = 'password';
$config['minecraft']['database'] = 'database';
$config['minecraft']['dbprefix'] = '';
$config['minecraft']['dbdriver'] = 'mysqli';


/**
 * Pluginu datubāžu uzstādījumi
 */
$config['sql_params'] = array(
    
    'auth_params' => array(
        'table_name' => 'authme',
        'username_field' => 'username',
        'password_field' => 'password',
    ),
    
    'unban_params' => array(
        'table_name' => 'banlist',
        'username_field' => 'name',
    ),
    
    'amnesty_params' => array(
        'table_name' => 'jail_prisoners',
        'username_field' => 'PlayerName',
    ),
    
    'credits_params' => array(
        'table_name' => 'iConomy',
        'username_field' => 'username',
        'balance_field' => 'balance',
    ),
);


$config['sql_table'] = 'shop_services';


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['minecraft']['pconnect'] = FALSE;
$config['minecraft']['db_debug'] = TRUE;
$config['minecraft']['cache_on'] = FALSE;
$config['minecraft']['cachedir'] = '';
$config['minecraft']['char_set'] = 'utf8';
$config['minecraft']['dbcollat'] = 'utf8_general_ci';