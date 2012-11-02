<?php
/**
 * Muonline master.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['muonline_services'] = array(

    'unban' => array(
      
        'title' => 'Noņemt banu',
        
        'prices_display' => '%price %curr',
        
        'toplist' => TRUE,
        
    ),
    
    'credits' => array(
      
        'title' => 'Iegādāties Credits',
        
        'prices_display' => '%price %curr - %amount credits',
        
        'toplist' => FALSE,
        
    ),
    
    'cspoints' => array(
      
        'title' => 'Iegādāties CSPoints',
        
        'prices_display' => '%price %curr - %amount CSpoints',
        
        'toplist' => FALSE,
        
    ),
    
    'skillbooster' => array(
      
        'title' => 'Skill-booster',
        
        'prices_display' => '%price %curr - %amount Skill points',
        
        'active_skills' => array('Strength', 'Dexterity', 'Vitality', 'Energy'),
        
        /**
         * Uzstādam atkarībā no muonline servera konfigurācijas!
         */
        'max_skill' => '32000',
        
    ),
    
    'gmaccess' => array(
      
        'title' => 'GM access',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,

    ),
    
    'vipserver' => array(
      
        'title' => 'Reģitrēties VIP serverī',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'file_path' => 'D:/muOnline/SubServerVIP/Data/ConnectMember.txt',
        
        /* not used */
        'unlimited' => FALSE,
        
        'toplist' => TRUE,
        
    ),
    
);


/**
 * Pieejamie apmaksas veidi
 */
$config['muonline_payments'] = array('sms', 'ibank', 'paypal');


/**
 * Shop mēģinās izvilikt username no šiem cookies uzstādījumiem. Varam papildināt ja cookie nosaukums
 * atšķiras
 */
$config['cookie_names'] = array(
    'WebShopUsername',
    'mu_user',
    //'username',
);


/**
 * Pašlaik vēl netiek izmantots
 */
$config['inline_login'] = TRUE;


/**
 * Pašlaik vēl netiek izmantots
 */
$config['login_auth_type'] = 'md5';