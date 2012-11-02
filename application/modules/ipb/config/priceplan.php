<?php
/**
 * IPB priceplan.php konfigurācijas fails.
 */

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


$config['ipb_prices_sms']['lv'] = array(
    
    'unsuspend' => array(
        '200' => '200',
    ),
    
    'title' => array(
        '35' => '35'
    ),
    
    'displayname' => array(
        '35' => '35'
    ),
    
    'ipoints' => array(
        '150' => '100',
        '200' => '250',
        '300' => '350',
        '500' => '600',
    ),
    
    'unwarn' => array(
        '35' => '10',
        '75' => '20',
        '150' => '40',
        '200' => '50',
        '300' => '100',
    ),    
    
);


$config['ipb_prices_ibank']['lv'] = array(
    
    'unsuspend' => array(
        '2.00' => '200',
    ),
    
    'title' => array(
        '1.00' => '100'
    ),
    
    'displayname' => array(
        '1.00' => '100'
    ),
    
    'ipoints' => array(
        '1.50' => '100',
        '2.00' => '250',
        '3.00' => '350',
        '5.00' => '600',
    ),
    
    'unwarn' => array(
        '1.50' => '40',
        '2.00' => '50',
        '3.00' => '100',
    ),    
    
);


$config['ipb_prices_paypal']['eu'] = array(
    
    'unsuspend' => array(
        '2.00' => '200',
    ),
    
    'title' => array(
        '1.00' => '100'
    ),
    
    'displayname' => array(
        '1.00' => '100'
    ),
    
    'ipoints' => array(
        '1.50' => '100',
        '2.00' => '250',
        '3.00' => '350',
        '5.00' => '600',
    ),
    
    'unwarn' => array(
        '1.50' => '40',
        '2.00' => '50',
        '3.00' => '100',
    ),    
    
);