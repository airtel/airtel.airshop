<?php
/**
 * Minecraft master.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * 
 */
$config['minecraft_server_settings'] = array(
    
    /**
     * Servera ip adrese vai hostname
     */
    'server_hostname' => '192.168.0.1',
    
    /**
     * Servera rcon ports
     */
    'server_port' => '45675',
    
    /**
     * Servera query ports
     * Jābut ieslēgtam enable-query=true
     */
    'query_port' => '25565',
    
    /**
     * Servera rcon parole
     */    
    'server_rcon' => 'rcon_password',
    
    /**
     * Perkot pakalpojumus, ja parametrs uzstādīts uz TRUE serveris paziņos par to, ka lietotājs
     * ir nopircis pakalpojumus.
     */    
    'notifications' => TRUE,
);





/**
 * 
 */
$config['minecraft_services'] = array(

    'unban' => array(
      
        'title' => 'Noņemt banu',
        
        'plugin' => 'figadmin',
        
        /**
         * Piejami divi varianti:
         * both un rcon
         * Ja plugins lieto mysql datubāzi, tad uzstādam uz "both"
         */
        'functionality' => 'rcon',
        
        'toplist' => TRUE,
        
        'prices_display' => '%price %curr',
        
    ),
    
    'credits' => array(
      
        'title' => 'Iegādāties Credits',
        
        'plugin' => 'essentials',
        
        /**
         * Piejami divi varianti:
         * both un rcon
         * Ja plugins lieto mysql datubāzi, tad uzstādam uz "both"
         */
        'functionality' => 'rcon',
        
        'toplist' => TRUE,
        
        'prices_display' => '%price %curr - %amount credits',
        
    ),
    
    'amnesty' => array(
      
        'title' => 'Izmukt no Jail',
        
        'plugin' => 'jail',
        
        /**
         * Piejami divi varianti:
         * both un rcon
         * Ja plugins lieto mysql datubāzi, tad uzstādam uz "both"
         */
        'functionality' => 'rcon',
        
        'toplist' => TRUE,
        
        'prices_display' => '%price %curr',
        
    ),
    
    'peaceful' => array(
      
        'title' => 'Frakcijas Peaceful statuss',
        
        'plugin' => 'factions',
        
        /* not used */
        'functionality' => 'rcon',
        
        'toplist' => TRUE,
        
        'prices_display' => '%price %curr - %amount dienas',
        
    ),
    
    'power' => array(
      
        'title' => 'Frakcijas power',
        
        'plugin' => 'factions',
        
        /* not used */
        'functionality' => 'rcon',
        
        'prices_display' => '%price %curr - %amount power',
        
    ),
    
    'groups' => array(
      
        'title' => 'Grupas maiņa',
        
        'plugin' => 'permissionsex',
        
        'groups_world' => NULL,
        
        /**
         * Piejami divi varianti:
         * both un rcon
         * Ja plugins lieto mysql datubāzi, tad uzstādam uz "both"
         */
        'functionality' => 'rcon',
        
        'toplist' => TRUE,
        
        'prices_display' => '%price %curr - %amount dienas',
        
        /**
         * Grupu nosaukumiem obligāti jasakrīt ar nosaukumiem no priceplan.php faila.
         * Piemēram. miniVIP šeit un priceplan.php failā tieši tāpat miniVIP.
         * Otra daļa regulē to kā vizuāli tiks parādīts grupas nosaukums iekš dropdown menu.
         */
        'group_names' => array(
            
            'miniVIP' => 'mini VIP grupa',
            'midiVIP' => 'midi VIP grupa',
            'bigVIP' => 'big VIP grupa',
            'Moderator' => 'Spēles moderātors',
            'GameMaster' => 'Spēles Gamemaster',
            
        ),
        
    ),
    
    'exp' => array(
      
        'title' => 'Iegādāties EXP',
        
        'plugin' => 'base',
        
        /* not used */
        'functionality' => 'rcon',
        
        'prices_display' => '%price %curr - %amount EXP',
        
    ),
    
    'broadcast' => array(
      
        'title' => 'Ziņojuma broadcast',
        
        'plugin' => 'base',
        
        /* not used */
        'functionality' => 'rcon',
        
        'prices_display' => '%price %curr',
        
    ),
);


/**
 * Pieejamie apmaksas veidi
 */
$config['minecraft_payments'] = array('sms', 'ibank', 'paypal');


/**
 * Pašlaik vēl netiek izmantots
 */
$config['inline_login'] = TRUE;


/**
 * Pašlaik vēl netiek izmantots
 */
$config['login_auth_type'] = 'md5';