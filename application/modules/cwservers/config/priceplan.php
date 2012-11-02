<?php
/**
 * CWservers priceplan.php configuration file.
 */

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


$config['cwservers_prices_sms']['lv'] = array(
    
    'hlds' => array(
        '35' => '1',
        '95' => '3',
        '150' => '7',
    ),
    
    'srcds' => array(
        '35' => '1',
        '95' => '3',
        '150' => '7',
    ),
    
);


/**
 * Cenas formātam jabūt obligāti ar punktu!
 * Piemērs:
 * '2.00' => '60',
 */
$config['cwservers_prices_ibank']['lv'] = array(
    
    'hlds' => array(
        '1.00' => '3',
        '1.50' => '7',
        '2.50' => '14',
    ),
    
    'srcds' => array(
        '1.00' => '3',
        '1.50' => '7',
        '2.50' => '14',
    ),
    
);


/**
 * Cenas formātam jabūt obligāti ar punktu!
 * Cenu valūta: EUR
 * Piemērs:
 * '2.00' => '60',
 */
$config['cwservers_prices_paypal']['eu'] = array(
    
    'hlds' => array(
        '1.00' => '3',
        '1.50' => '7',
        '2.50' => '14',
    ),
    
    'srcds' => array(
        '1.00' => '3',
        '1.50' => '7',
        '2.50' => '14',
    ),
    
);