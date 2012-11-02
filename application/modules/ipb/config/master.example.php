<?php
/**
 * Ipb master.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['ipb_services'] = array(

    'unsuspend' => array(
      
        'title' => 'Noņemt suspend',
        
        'prices_display' => '%price %curr',
        
    ),
    
    'title' => array(
      
        'title' => 'Uzlikt savu title',
        
        'prices_display' => '%price %curr',
        
    ),
    
    'displayname' => array(
      
        'title' => 'Uzlikt savu Display name',
        
        'prices_display' => '%price %curr',
        
    ),
    
    'ipoints' => array(
      
        'title' => 'Iegādāties i-Points',
        
        'toplist' => TRUE,
        
        'prices_display' => '%price %curr - %amount ipoints',
        
        /**
         * ja IPB lieto ibeconomy, tad jāuzstāda uz TRUE
         */
        'ibeconomy' => TRUE,
        
    ),
    
    'unwarn' => array(
      
        'title' => 'Noņemt Warn statusu',
        
        'prices_display' => '%price %curr - %amount %',
        
    ),
    
);


/**
 * Pieejamie apmaksas veidi
 */
$config['ipb_payments'] = array('sms', 'ibank', 'paypal');


// Default
$config['ipb_board_path'] = '/home/public_html/community/forums/';


/**
 * 
 */
$config['ipb_inline_login'] = TRUE;