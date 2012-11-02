<?php
/**
 * Muonline priceplan.php configuration file.
 */

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


$config['muonline_prices_sms']['lv'] = array(
    
    'unban' => array(
        '300' => '300',
    ),
    
    'credits' => array(
        '15' => '100',
        '35' => '350',        
        '60' => '620',
        '95' => '1050',        
        '150' => '1550',
        '200' => '2100',        
        '300' => '3200',
        '400' => '4300', 
        '500' => '5400',
    ),
    
    'cspoints' => array(
        '15' => '100',
        '60' => '600',
        '150' => '1500',
        '300' => '3000',
        '400' => '4000', 
        '500' => '5000',
    ),
    
    'skillbooster' => array(
        '50' => '4000',
        '95' => '8000',
        '200' => '16000',
        '250' => '32000',
    ),
    
    /**
     * Cenas pret dienu skaitu attiecība
     */
    'gmaccess' => array(
        '15' => '1',
        '150' => '7',
        '300' => '30',
        '500' => '90',
    ),
    
    /**
     * Cenas pret dienu skaitu attiecība
     */
    'vipserver' => array(
        '15' => '1',
        '95' => '7',
        '200' => '30',
        '500' => '90',
    ),
    
);


/**
 * Cenām obligāti jābūt norādītām ar punktu!
 * Piemērs: '3.00' => '1000',
 */
$config['muonline_prices_ibank']['lv'] = array(
    
    'unban' => array(
        '3.00' => '300',
    ),
    
    'credits' => array(
        '1.00' => '1050',        
        '1.50' => '1550',
        '2.00' => '2100',        
        '3.00' => '3200',
        '4.00' => '4300', 
        '5.00' => '5400',
    ),
    
    'cspoints' => array(
        '1.50' => '1500',
        '3.00' => '3000',
        '4.00' => '4000', 
        '5.00' => '5000',
    ),
    
    'skillbooster' => array(
        '1.00' => '8000',
        '2.00' => '16000',
        '2.50' => '32000',
    ),
    
    'gmaccess' => array(
        '1.50' => '7',
        '3.00' => '30',
        '5.00' => '90',
        '10.00' => '240',
    ),
    
    'vipserver' => array(
        '1.00' => '7',
        '2.00' => '30',
        '5.00' => '90',
        '10.00' => '240',
    ),
    
);


/**
 * Cenām obligāti jābūt norādītām ar punktu!
 * Cenu valūta: EUR
 * Piemērs: '3.00' => '1000',
 */
$config['muonline_prices_paypal']['eu'] = array(
    
    'unban' => array(
        '3.00' => '300',
    ),
    
    'credits' => array(
        '1.00' => '1050',        
        '1.50' => '1550',
        '2.00' => '2100',        
        '3.00' => '3200',
        '4.00' => '4300', 
        '5.00' => '5400',
    ),
    
    'cspoints' => array(
        '1.50' => '1500',
        '3.00' => '3000',
        '4.00' => '4000', 
        '5.00' => '5000',
    ),
    
    'skillbooster' => array(
        '1.00' => '8000',
        '2.00' => '16000',
        '2.50' => '32000',
    ),
    
    'gmaccess' => array(
        '1.50' => '7',
        '3.00' => '30',
        '5.00' => '90',
        '10.00' => '240',
    ),
    
    'vipserver' => array(
        '1.00' => '7',
        '2.00' => '30',
        '5.00' => '90',
        '10.00' => '240',
    ),
    
);