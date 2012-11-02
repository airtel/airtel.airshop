<?php
/**
 * General master.php configuration file.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['general_services'] = array(

    'donate' => array(
      
        'title' => 'Atbalstīt projektu',
        
        'prices_display' => '%price %curr',
        
        'toplist' => TRUE,

    ),
    
    
    // Not implemented in this shop version
    /*'advert' => array(
      
        'title' => 'Izvietot reklāmu'
        
    ),*/
    
);


/**
 * Pieejamie apmaksas veidi
 */
$config['general_payments'] = array('sms', 'ibank', 'paypal');