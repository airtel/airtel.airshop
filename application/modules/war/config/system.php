<?php
/**
 * War3 system.php konfigurācijas fails.
 */


if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


$config['load_custom_js'] = TRUE;


$config['submenu_names'] = array(
    'exp' => 'Lietotāju EXP saraksts',
    'expsurf' => 'Lietotāju Surf EXP saraksts',
    'expzm' => 'Lietotāju ZM EXP saraksts',
    'exphalo' => 'Lietotāju Halo EXP saraksts',
    'zmammo' => 'Lietotaju Ammo saraksts',
);


/**
 * Controlls which service needs obligatory login into system
 */
$config['services_settings'] = array(
    
    'exp' => array(
        'login_required' => FALSE,
    ),
    
    'expsurf' => array(
        'login_required' => FALSE,
    ),
    
    'expzm' => array(
        'login_required' => FALSE,
    ),
    
    'exphalo' => array(
        'login_required' => FALSE,
    ),
    
    'exchange' => array(
        'login_required' => FALSE,
    ),
    
    'zmammo' => array(
        'login_required' => FALSE,
    ),

);


$config['fields_exp'] = array(
    
    'races' => array(
        'label' => 'Izvēlies rasi',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'php_validation' => 'xss_clean|alpha_numeric',
        'options' => 'autocomplete="off" id="races" class="chosen"',
        'value' => set_value('races'),
    ),
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_exp_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),

);


$config['fields_exchange'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_exp_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),
);


$config['fields_zmammo'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_ammo_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),
);
