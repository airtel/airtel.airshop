<?php
/**
 * IPB system.php configuration file.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['load_custom_js'] = TRUE;


/**
 * IPB services table structure
 * 
 * $config['ibp_table_structure'] = array(
 *      'id' => array('type' => 'INT', 'auto_increment' => TRUE),
 *      'username' => array('type' => 'VARCHAR', 'constraint' => '64'),
 *      'expires' => array('type' => 'VARCHAR', 'constraint' => '12'),
 *      'module' => array('type' => 'VARCHAR', 'constraint' => '12'),
 *      'service' => array('type' => 'VARCHAR', 'constraint' => '12'),
 * );
 */


$config['submenu_names'] = array(
    'ipoints' => 'Bagātako lietotāju tops'
);


/**
 * IPB login form attributes
 */
$config['ipbform_attr'] = array(
    'id' => 'ipbform', 
    'class' => 'form-horizontal well', 
    'autocomplete' => 'off',
    'style' => 'margin-top: 25px;',
);


/**
 * Controlls which service needs obligatory login into system
 */
$config['services_settings'] = array(
    
    'unsuspend' => array(
        'login_required' => FALSE,
    ),
    
    'title' => array(
        'login_required' => TRUE,
    ),
    
    'displayname' => array(
        'login_required' => TRUE,
    ),
    
    'ipoints' => array(
        'login_required' => TRUE,
    ),
    
    'unwarn' => array(
        'login_required' => TRUE,
    ),
);


/**
 * Fields for ipb services
 */
$config['fields_unsuspend'] = array(
    
    'username' => array(
        'label' => 'IPB lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_suspended',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__ipb_check_suspended',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_title'] = array(
    
    'username_info' => array(
        'label' => 'IPB lietotājvārds',
        'type' => 'text',
        'value' => '',
    ),
    
    'title' => array(
        'label' => 'Tavs title',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean|max_length[150]|min_length[2]',
        'options' => array(
            'name' => 'title',
            'id' => 'title',
            'value' => set_value('title'),
            'maxlength' => '150',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_displayname'] = array(
    
    'username_info' => array(
        'label' => 'IPB lietotājvārds',
        'type' => 'text',
        'value' => '',
    ),
    
    'displayname' => array(
        'label' => 'Tavs display name',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_displayname check_valid_displayname',
        'php_validation' => 'required|xss_clean|max_length[150]|min_length[2]|callback__ipb_check_valid_displayname|callback__ipb_check_displayname',
        'options' => array(
            'name' => 'displayname',
            'id' => 'displayname',
            'value' => set_value('displayname'),
            'maxlength' => '150',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_ipoints'] = array(
    
    'username_info' => array(
        'label' => 'IPB lietotājvārds',
        'type' => 'text',
        'value' => '',
    ),
    
    /**
     * No fields required
     */     
);


$config['fields_unwarn'] = array(
    
    'username_info' => array(
        'label' => 'IPB lietotājvārds',
        'type' => 'text',
        'value' => '',
    ),
    
    /**
     * No fields required
     */     
);