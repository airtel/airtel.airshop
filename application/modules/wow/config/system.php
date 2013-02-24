<?php
/**
 * Wow system.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['load_custom_js'] = TRUE;


/**
 * Wow services table structure
 */
/*$config['wow_table_structure'] = array(
    'id' => array('type' => 'INT', 'auto_increment' => TRUE),
    'username' => array('type' => 'VARCHAR', 'constraint' => '64'),
    'expires' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'module' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'service' => array('type' => 'VARCHAR', 'constraint' => '12'),
);*/


$config['submenu_names'] = array(
    'unban_account' => 'Lietotāju banu saraksts',
    'unban_character' => 'Lietotāju banu saraksts',
    'unban_ip' => 'Lietotāju banu saraksts',
    'gold' => 'Gold tops',
    'exp' => 'Exp tops',
    'levelup' => 'Spēlētāju tops',
);


/**
 * Controlls which service needs obligatory login into system
 */
$config['services_settings'] = array(
    
    'unban_account' => array(
        'login_required' => FALSE,
    ),
    
    'unban_character' => array(
        'login_required' => FALSE,
    ),
    
    'unban_ip' => array(
        'login_required' => FALSE,
    ),
    
    'gold' => array(
        'login_required' => FALSE,
    ),
    
    'donate_points' => array(
        'login_required' => FALSE,
    ),
    
    'exp' => array(
        'login_required' => FALSE,
    ),
    
    'levelup' => array(
        'login_required' => FALSE,
    ),    
    
);


/**
 * Fields for wow services
 */
$config['fields_unban_account'] = array(
    
    'expression' => array(
        'label' => 'Account',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_ban_account',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_check_ban_account',
        'options' => array(
            'name' => 'expression',
            'id' => 'expression',
            'value' => set_value('expression'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_unban_character'] = array(
    
    'expression' => array(
        'label' => 'Character',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_ban_character',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_check_ban_character',
        'options' => array(
            'name' => 'expression',
            'id' => 'expression',
            'value' => set_value('expression'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_unban_ip'] = array(
    
    'expression' => array(
        'label' => 'IP adrese',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_ip check_ban_ip',
        'php_validation' => 'required|xss_clean|max_length[15]|valid_ip|callback__wow_check_ban_ip',
        'options' => array(
            'name' => 'expression',
            'id' => 'expression',
            'value' => set_value('expression'),
            'maxlength' => '15',
        ),
    ),  
);


$config['fields_gold'] = array(
    
    'character' => array(
        'label' => 'Character',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user check_online',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_valid_user|callback__wow_check_online',
        'options' => array(
            'name' => 'character',
            'id' => 'character',
            'value' => set_value('character'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_donate_points'] = array(
    
    'expression' => array(
        'label' => 'Account',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_web_user', //valid_web_user
        //'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_valid_web_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_valid_web_user',
        'options' => array(
            'name' => 'expression',
            'id' => 'expression',
            'value' => set_value('expression'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_exp'] = array(
    
    'character' => array(
        'label' => 'Character',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user check_online',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_valid_user|callback__wow_check_online',
        'options' => array(
            'name' => 'character',
            'id' => 'character',
            'value' => set_value('character'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_levelup'] = array(
    
    'character' => array(
        'label' => 'Character',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user check_online',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__wow_valid_user|callback__wow_check_online',
        'options' => array(
            'name' => 'character',
            'id' => 'character',
            'value' => set_value('character'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);