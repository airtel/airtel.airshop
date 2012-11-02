<?php
/**
 * Sourcemod priceplan.php konfigurācijas fails.
 */

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


$config['sourcemod_prices_sms']['lv'] = array(
    
    'unban' => array(
        '200' => '200',
    ),
    
    'slot' => array(
        '95' => '60',
        '200' => '90',
        '300' => '120',
        '500' => '240',
    ),
    
    'access' => array(
        '95' => '60',
        '200' => '90',
        '300' => '120',
        '500' => '240',
    ),
    
    'vip' => array(
        '95' => '60',
        '200' => '90',
        '300' => '120',
        '500' => '240',
    ),
    
    'accessvip' => array(
        '95' => '60',
        '200' => '90',
        '300' => '120',
        '500' => '240',
    ),
    
);


/**
 * Cenām obligāti jābūt norādītām ar punktu!
 * Piemērs: '3.00' => '1000',
 */
$config['sourcemod_prices_ibank']['lv'] = array(
    
    'unban' => array(
        '2.00' => '200',
    ),
    
    'slot' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
    'access' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
    'vip' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
    'accessvip' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
);


/**
 * Cenām obligāti jābūt norādītām ar punktu!
 * Cenu valūta: EUR
 * Piemērs: '3.00' => '1000',
 */
$config['sourcemod_prices_paypal']['eu'] = array(
    
    'unban' => array(
        '2.00' => '200',
    ),
    
    'slot' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
    'access' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
    'vip' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
    'accessvip' => array(
        '1.00' => '60',
        '2.00' => '90',
        '3.00' => '120',
        '5.00' => '240',
    ),
    
);