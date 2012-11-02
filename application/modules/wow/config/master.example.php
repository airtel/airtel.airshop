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
        
    ),
    
    'unban_character' => array(
      
        'title' => 'Noņemt character banu',
        
        'prices_display' => '%price %curr',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
    ),
    
    'unban_ip' => array(
      
        'title' => 'Noņemt IP banu',
        
        'prices_display' => '%price %curr',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
    ),
    
    'gold' => array(
        
        'title' => 'Iegādāties gold',
        
        'prices_display' => '%price %curr - %amount gold',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
    ),
    
    'exp' => array(
        
        'title' => 'Paaugstināt Character EXP',
        
        'prices_display' => '%price %curr - %amount Exp',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
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
