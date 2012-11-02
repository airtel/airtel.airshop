<?php
/**
 * War3 master.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['war_services'] = array(

    'exp' => array(
      
        'title' => 'Paaugstināt War3 EXP',
        
        'prices_display' => '%price %curr - %amount EXP',
        
        'toplist' => TRUE,
        
        'db_array_name' => 'war3',
        
    ),
    
    'expsurf' => array(
        
        'title' => 'Paaugstināt Surf EXP',
        
        'prices_display' => '%price %curr - %amount EXP',
        
        'toplist' => TRUE,
        
        'db_array_name' => 'surf',
        
    ),
    
    /*'expzm' => array(
        
        'title' => 'Paaugstināt Zombiemod EXP',
        
        'prices_display' => '%price %curr - %amount EXP',
        
        'toplist' => TRUE,
        
        'db_array_name' => 'zm',
        
    ),*/
    
    /*'exphalo' => array(
        
        'title' => 'Paaugstināt Halo EXP',
        
        'prices_display' => '%price %curr - %amount EXP',
        
        'toplist' => TRUE,
        
        'db_array_name' => 'halo',
        
    ),*/
    
    /**
     * Zombie plague ammo packs bank amxx plugin
     * https://forums.alliedmods.net/showthread.php?t=82090
     */
    'zmammo' => array(
      
        'title' => 'ZM Ammo pack iegāde',
        
        'prices_display' => '%price %curr - %amount ammo',
        
        'toplist' => TRUE,
        
        'db_array_name' => 'zm',
        
        /**
         * Note: tabulas nosaukumam iekš servera ir jasatur tādu pašu tabulu prefiksu kā citām War3 datubāzēm.
         * Piemēram: wc3_zp_bank
         * 
         * Šeit norādam tabulas nosaukumu bez prefiksa!
         * Piemērs, ko norāda šeit: zp_bank
         * Istais tabulas nosaukums: wc3_zp_bank
         */
        'db_sql_table' => 'zp_bank'
        
    ),
    
);


/**
 * Pieejamie apmaksas veidi
 */
$config['war_payments'] = array('sms', 'ibank', 'paypal');


/**
 * Not used in this shop version 
 */
$config['war_inline_login'] = FALSE;


