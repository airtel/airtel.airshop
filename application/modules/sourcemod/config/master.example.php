<?php
/**
 * Sourcemod master.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['sourcemod_services'] = array(

    'unban' => array(
      
        'title' => 'Noņemt banu',
        
        'prices_display' => '%price %curr',
        
        'toplist' => TRUE,
        
    ),
    
    'slot' => array(
      
        'title' => 'Slot',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,
        
        /**
         * Norādiet serverus kuriem nebūs pieejams šis pakalpojums.
         * Aizpildīšanas piemērs:
         * array(1, 2, 3),
         */        
        'exclude_servers' => array(),
        
        'access_group' => array(
            'name' => 'airtel-shop-slot',
            'flags' => 'a',
            'immunity' => '0',
        ),
        
        'web_access_group' => array(
            'name' => 'airtel-shop-webgroup-slot',
            'extraflags' => '0',
        ),
        
    ),
    
    'access' => array(
      
        'title' => 'SM access',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,
        
        'exclude_servers' => array(),
        
        'access_group' => array(
            'name' => 'airtel-shop-access',
            'flags' => 'abcdfk',
            'immunity' => '50',
        ),
        
        'web_access_group' => array(
            'name' => 'airtel-shop-webgroup-access',
            'extraflags' => '0',
        ),
        
    ),
    
    'vip' => array(
      
        'title' => 'VIP',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,
        
        'exclude_servers' => array(),
        
        'access_group' => array(
            'name' => 'airtel-shop-vip',
            'flags' => 'abcdfk',
            'immunity' => '50',
        ),
        
        'web_access_group' => array(
            'name' => 'airtel-shop-webgroup-vip',
            'extraflags' => '0',
        ),
        
    ),
    
    'accessvip' => array(
      
        'title' => 'SM access + VIP',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,
        
        'exclude_servers' => array(),
        
        'access_group' => array(
            'name' => 'airtel-shop-accessvip',
            'flags' => 'abcdfk',
            'immunity' => '50',
        ),
        
        'web_access_group' => array(
            'name' => 'airtel-shop-webgroup-accessvip',
            'extraflags' => '0',
        ),
        
    ),
    
);


/**
 * Masīvs satur datus par to, ko dara katrs no flagiem, ja lietojat savās funkcijās papildus flagus,
 * kas nav aprakstīti šeit, tad droši pievienojiet tos šajā konfigā.
 * 
 * Piemērs:
 * 'burts' => 'šī burta nozīme',
 * 
 */
$config['sm_access_flags_description'] = array (
    'a' => 'reservation (can join on reserved slots)',
    'b' => 'Admin flags',
    'c' => 'Kick other players',
    'd' => 'Ban other players',
    'f' => 'Slay/harm other players',
    'k' => 'Start or create votes',
);


/**
 * Pieejamie apmaksas veidi
 */
$config['sourcemod_payments'] = array('sms', 'ibank', 'paypal');


/**
 * Not used in this shop version 
 */
$config['sourcemod_inline_login'] = FALSE;


/**
 * Veik pieslēģsanos pie servera un privilēģiju pārlādēšanu ?
 * Ja ir TRUE, tad skripts pēc veiksmīgas pasūtīšanas pieslēdzas pie servera
 * un izpilda admin reloadprivelgies komandu, kas ļauj izmantot pakalpojumu
 * uzreiz pēc tā pasūtīšanas. 
 * Lai opcija strādātu sourcebans tabūlā serverim nepieciešama uztādīta pareiza rcon parole
 */
$config['reload_privelegies'] = TRUE;


/**
 * Kada komanda serverī tiek izmantota lai veiktu reload privelegies
 */
$config['reload_command'] = 'sm_reloadadmins';


/**
 * Servera tips. STEAM serveriem = TRUE; NON-STEAM serveriem = FALSE;
 * Steam gadījumā piesaiste notiks pēc steamid; nonsteam gadījumā pēc IP adreses. 
 */
$config['use_steam'] = TRUE;