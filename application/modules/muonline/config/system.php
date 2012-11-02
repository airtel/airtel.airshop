<?php
/**
 * Muonline system.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['load_custom_js'] = TRUE;


/**
 * Muonline services table structure
 */
$config['muonline_table_structure'] = array(
    'id' => array('type' => 'INT', 'identity' => TRUE),
    'username' => array('type' => 'VARCHAR', 'constraint' => '64'),
    'character' => array('type' => 'VARCHAR', 'constraint' => '64', 'null' => TRUE),
    'expires' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'module' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'service' => array('type' => 'VARCHAR', 'constraint' => '12'),
);


$config['submenu_names'] = array(
    'unban' => 'Lietotāju banu saraksts',
    'credits' => 'Kredītu tops',
    'cspoints' => 'CSpoints tops',
    'gmaccess' => 'Apmaksāto GM\'u tops',
    'vipserver' => 'Vipservera lietotāju saraksts',
);


/**
 * Controlls which service needs obligatory login into system
 */
$config['services_settings'] = array(
    
    'unban' => array(
        'login_required' => FALSE,
    ),
    
    'credits' => array(
        'login_required' => FALSE,
    ),
    
    'cspoints' => array(
        'login_required' => FALSE,
    ),
    
    'skillbooster' => array(
        'login_required' => TRUE,
    ),
    
    'gmaccess' => array(
        'login_required' => TRUE,
    ),
    
    'vipserver' => array(
        'login_required' => FALSE,
    ),
);


/**
 * Fields for muonline services
 */
$config['fields_unban'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_name_ban',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__muonline_check_name_ban',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_credits'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__muonline_check_valid_user',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_cspoints'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__muonline_check_valid_user',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_skillbooster'] = array(

    'username_info' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'text',
        'value' => '',
    ),    
    
    'character' => array(
        'label' => 'MU character',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'php_validation' => 'xss_clean|alpha_numeric',
        'options' => 'autocomplete="off" id="character" class="chosen"',
        'value' => set_value('character'),
    ),
    
    
    'skills' => array(
        'label' => 'Izvēlies skill',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'php_validation' => 'xss_clean|alpha_numeric',
        'options' => 'autocomplete="off" id="skills" class="chosen"',
        'value' => set_value('skills'),
    ),
);


$config['fields_gmaccess'] = array(

    'username_info' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'text',
        'value' => '',
    ),    
    
    'character' => array(
        'label' => 'MU character',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'php_validation' => 'xss_clean|alpha_numeric',
        'options' => 'autocomplete="off" id="character" class="chosen"',
        'value' => set_value('character'),
    ),
);


$config['fields_vipserver'] = array(

    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__muonline_check_valid_user',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),      
);