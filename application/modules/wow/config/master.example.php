<?php
/**
 * Wow master.php configuration file.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['wow_services'] = array(

    'unban_account' => array(
      
        'title' => 'Noņemt account banu',
        
        'prices_display' => '%price %curr',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'auth',
    ),
    
    'unban_character' => array(
      
        'title' => 'Noņemt character banu',
        
        'prices_display' => '%price %curr',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'chars',
    ),
    
    'unban_ip' => array(
      
        'title' => 'Noņemt IP banu',
        
        'prices_display' => '%price %curr',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'auth',
    ),
    
    'gold' => array(
        
        'title' => 'Iegādāties gold',
        
        'prices_display' => '%price %curr - %amount gold',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'chars',
    ),
    
    'donate_points' => array(
        
        'title' => 'Buy donate points',
        
        'prices_display' => '%price %curr - %amount Dpoints',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => FALSE,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'web',
    ),
    
    'exp' => array(
        
        'title' => 'Paaugstināt Character EXP',
        
        'prices_display' => '%price %curr - %amount Exp',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'chars',
    ),
    
    'levelup' => array(
        
        'title' => 'Paaugstināt Character Level',
        
        'prices_display' => '%price %curr - pievienot %amount levels',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        /**
         * Serverī maksimālais spēlētāja līmenis
         */
        'max_level' => 80,
        
        /**
         * Database where plugin tables are stored.
         */
        'db_array_name' => 'chars',
    ),
    
);


/**
 * Pieejamie apmaksas veidi
 */
$config['wow_payments'] = array('sms', 'ibank', 'paypal');


/**
 * Pašlaik vēl netiek izmantots
 */
$config['wow_inline_login'] = TRUE;
