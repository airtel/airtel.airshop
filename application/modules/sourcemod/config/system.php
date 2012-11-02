<?php
/**
 * Sourcemod system.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['load_custom_js'] = TRUE;


/**
 * Sourcemod services table structure
 */
$config['sourcemod_table_structure'] = array(
    'id' => array('type' => 'INT', 'auto_increment' => TRUE),
    'server_id' => array('type' => 'INT', 'constraint' => '11'),
    'username' => array('type' => 'VARCHAR', 'constraint' => '64'),
    'expires' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'module' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'service' => array('type' => 'VARCHAR', 'constraint' => '12'),
);


$config['submenu_names'] = array(
    'unban' => 'Lietotāju banu saraksts',
    'slot' => 'Slot lietotāji',
    'access' => 'AMX access lietotāji',
    'vip' => 'VIP lietotāji',
    'accessvip' => 'AMX access + VIP lietotāji',
);


/**
 * Controlls which service needs obligatory login into system and other services settings
 */
$config['services_settings'] = array(
    
    'unban' => array(
        'login_required' => FALSE,
        'type' => 'one-time',
    ),
    
    'slot' => array(
        'login_required' => FALSE,
        'type' => 'subscription',
    ),
    
    'access' => array(
        'login_required' => FALSE,
        'type' => 'subscription',
    ),
    
    'vip' => array(
        'login_required' => FALSE,
        'type' => 'subscription',
    ),
    
    'accessvip' => array(
        'login_required' => FALSE,
        'type' => 'subscription',
    ),
 
);


$config['fields_unban'] = array(
    
    'ipaddress' => array(
        'label' => 'Tava IP adrese',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_ip check_banned_ip',
        'php_validation' => 'required|xss_clean|max_length[15]|min_length[7]|valid_ip|callback__amx_check_banned_ip',
        'options' => array(
            'name' => 'ipaddress',
            'id' => 'ipaddress',
            'value' => set_value('ipaddress'),
            'maxlength' => '15',
            'minlength' => '7',
        ),
    ),
    
    'authid' => array(
        'label' => 'Tavs STEAM:ID',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_authid check_banned_authid',
        'php_validation' => 'required|xss_clean|max_length[18]|min_length[11]|callback__amx_check_banned_authid',
        'options' => array(
            'name' => 'authid',
            'id' => 'authid',
            'value' => set_value('authid'),
            'maxlength' => '18',
            'minlength' => '11',
        ),
    ),
    
);


$config['fields_sourcemod'] = array(
  
    'servers' => array(
        'label' => 'Izvēlies serveri',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 2,
        'php_validation' => 'xss_clean',
        'options' => 'autocomplete="off" id="servers" class="chosen"',
        'value' => set_value('servers'),
    ),    
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '64',
            'minlength' => '2',
        ),
    ),
    
    'email' => array(
        'label' => 'Tavs E-pasts',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required email',
        'php_validation' => 'required|xss_clean|max_length[128]|min_length[2]|valid_email',
        'options' => array(
            'name' => 'email',
            'id' => 'email',
            'value' => set_value('email'),
            'maxlength' => '128',
            'minlength' => '2',
        ),
    ),
    
    'authid' => array(
        'label' => 'Tavs STEAM:ID',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_authid',
        'php_validation' => 'required|xss_clean|max_length[18]|min_length[11]',
        'options' => array(
            'name' => 'authid',
            'id' => 'authid',
            'value' => set_value('authid'),
            'maxlength' => '18',
            'minlength' => '11',
        ),
    ),
    
    'password' => array(
        'label' => 'Tava parole',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean|max_length[32]|min_length[2]',
        'options' => array(
            'name' => 'password',
            'id' => 'password',
            'value' => set_value('password'),
            'maxlength' => '32',
            'minlength' => '2',
        ),
    ),
    
);