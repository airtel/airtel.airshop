<?php
/**
 * Amx master.php configuration file.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['amx_services'] = array(

    'unban' => array(
      
        'title' => 'Noņemt banu',
        
        'prices_display' => '%price %curr',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
    ),
    
    'nickname' => array(
      
        'title' => 'Reģistrēt savu nickname',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        'amx_flags' => 'z',
        
        /**
         * Norādiet serverus kuriem nebūs pieejams šis pakalpojums.
         * Aizpildīšanas piemērs:
         * array(1, 2, 3),
         */
        'exclude_servers' => array(),
        
    ),
    
    'slot' => array(
      
        'title' => 'Slot',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */        
        'toplist' => TRUE,
        
        'amx_flags' => 'bz',
        
        'exclude_servers' => array(),
        
    ),
    
    'access' => array(
      
        'title' => 'AMX access',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        'amx_flags' => 'bcdejiu',
        
        'exclude_servers' => array(),
        
    ),
    
    'vip' => array(
      
        'title' => 'VIP',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        'amx_flags' => 'bit',
        
        'exclude_servers' => array(),
        
    ),
    
    'accessvip' => array(
      
        'title' => 'AMX access + VIP',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        /**
         * Spēlētāju tops
         * TRUE / FALSE
         */
        'toplist' => TRUE,
        
        'amx_flags' => 'bcdejiut',
        
        'exclude_servers' => array(),
        
    ),
    
);


/**
 * Description for flags
 */
$config['amx_access_flags_description'] = array (
    'b' => 'reservation (can join on reserved slots)',
    'c' => 'amx_kick command ',
    'd' => 'amx_ban and amx_unban commands ',
    'e' => 'amx_slay and amx_slap commands ',
    'j' => 'amx_vote and other vote commands ',
    'i' => 'amx_chat and other chat commands ',
    'n' => 'allow to ban players permanently ',
    'u' => 'menu access',
    't' => 'vip flag',
    'z' => 'registered user ( not admin )',
);


/**
 * Pieejamie apmaksas veidi
 */
$config['amx_payments'] = array('sms', 'ibank', 'paypal');


/**
 * Not used in this shop version 
 */
$config['inline_login'] = FALSE;


/**
 * Kādā veidā tiek glabātas paroles ieksh amx datubāzes
 * Available options: md5 / plain
 */
$config['login_auth_type'] = 'plain';


/**
 * Veik pieslēģsanos pie servera un privilēģiju pārlādēšanu ?
 * Ja ir TRUE, tad skripts pēc veiksmīgas pasūtīšanas pieslēdzas pie servera
 * un izpilda admin reloadprivelgies komandu, kas ļauj izmantot pakalpojumu
 * uzreiz pēc tā pasūtīšanas. 
 * Lai opcija strādātu amxbans tabūlā serverim nepieciešama uztādīta pareiza rcon parole
 */
$config['reload_privelegies'] = TRUE;


/**
 * Kada komanda serverī tiek izmantota lai veiktu reload privelegies
 */
$config['reload_command'] = 'amx_reloadadmins';