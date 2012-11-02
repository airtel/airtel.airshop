<?php
/**
 * CWservers master.php configuration file.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['cwservers_services'] = array(

    'hlds' => array(
        
        'title' => 'Counter-Strike 1.6 CW',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,
        
        'ssh' => array(
            'hostname' => '127.0.0.1',
            'port' => '22',
            'username' => 'cwservers',
            'password' => 'mypassword',
        ),
        
        'options' => array(
            
			// Direktorija kur atrodas servera faili relatīvi pret ssh lietotāja home direktoriju.
            'server_dir' => './hlds/',
            'script' => './hlds_run',
            
            'args' => array(
                
                '-binary' => './hlds_i686',
                '-game' => 'cstrike',
                '+maxplayers' => '11',
                '+map' => 'de_dust2',
                '+sv_lan' => '0',
                '+sys_ticrate' => '1000',
                '+servercfgfile' => 'server.cfg',
            ),
            
            'extra' => '-insecure -nomaster',
        ),
        
    ),
    
    'srcds' => array(
        
        'title' => 'Counter-Strike:Source CW',
        
        'prices_display' => '%price %curr - %amount dienas',
        
        'toplist' => TRUE,
        
        'ssh' => array(
            'hostname' => '127.0.0.1',
            'port' => '22',
            'username' => 'cwservers',
            'password' => 'mypassword',
        ),    
        
        'options' => array(
            
            // Direktorija kur atrodas servera faili relatīvi pret ssh lietotāja home direktoriju.
            'server_dir' => './srcds/css/',
            
            'script' => './srcds_run',
            
            'args' => array(
                
                '-game' => 'cstrike',
                '+maxplayers' => '11',
                '+map' => 'de_dust2',
                '+sv_lan' => '0',
                '+servercfgfile' => 'server.cfg',
            ),
            
            'extra' => '',
        ),
        
    ),
    
);


/**
 * Pieejamie apmaksas veidi
 */
$config['cwservers_payments'] = array('sms', 'ibank', 'paypal');