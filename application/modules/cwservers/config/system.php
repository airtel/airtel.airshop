<?php
/**
 * CWservers system.php configuration file.
 * Do not edit this file !
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['load_custom_js'] = TRUE;


/**
 * CWservers services table structure
 */
$config['cwservers_table_structure'] = array(
    
    'id' => array('type' => 'INT', 'auto_increment' => TRUE),
    'module' => array('type' => 'VARCHAR', 'constraint' => '32'),    
    'server_hostname' => array('type' => 'VARCHAR', 'constraint' => '100'),
    'ip' => array('type' => 'VARCHAR', 'constraint' => '15'),
    'port' => array('type' => 'INT', 'constraint' => '5'),
    'name' => array('type' => 'VARCHAR', 'constraint' => '32', 'null' => TRUE),
    'server_rcon' => array('type' => 'VARCHAR', 'constraint' => '32', 'null' => TRUE),
    'server_password' => array('type' => 'VARCHAR', 'constraint' => '32', 'null' => TRUE),
    'expires' => array('type' => 'VARCHAR', 'constraint' => '10', 'default' => 0),

);


$config['submenu_names'] = array(
    
    'hlds' => 'Aizņemto serveru saraksts',
    'srcds' => 'Aizņemto serveru saraksts',
    
);


$config['fields_hlds'] = array(
    
    'servers' => array(
        'label' => 'Izvēlies serveri',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'php_validation' => 'xss_clean|numeric',
        'options' => 'autocomplete="off" id="servers" class="choose_server chosen"',
        'value' => set_value('servers'),
    ),
    
    'server_rcon' => array(
        'label' => 'Rcon parole',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean',
        'options' => array(
            'name' => 'server_rcon',
            'id' => 'server_rcon',
            'value' => set_value('server_rcon'),
            'maxlength' => '32',
            'minlength' => '2',
            'title' => 'Izvēlies RCON paroli, ar kuru varēsi kontrolēt serveri.',
        ),
    ),
    
    'server_password' => array(
        'label' => 'Servera parole',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean',
        'options' => array(
            'name' => 'server_password',
            'id' => 'server_password',
            'value' => set_value('server_password'),
            'maxlength' => '32',
            'minlength' => '2',
            'title' => 'Parole, kuru būs jāzin katram lietotājam, kurš vēlēsies spēlēt ar tevi.',
        ),
    ),
    
    'name' => array(
        'label' => 'Hostname',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean',
        'options' => array(
            'name' => 'name',
            'id' => 'name',
            'value' => set_value('name'),
            'maxlength' => '32',
            'minlength' => '4',
            'title' => 'Serevera nosaukums, kurš parādās spēles serveru līstē.',
        ),
    ),
    
);


$config['fields_srcds'] = array(
    
    'servers' => array(
        'label' => 'Izvēlies serveri',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'php_validation' => 'xss_clean|numeric',
        'options' => 'autocomplete="off" id="servers" class="choose_server chosen"',
        'value' => set_value('servers'),
    ),
    
    'server_rcon' => array(
        'label' => 'Rcon parole',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean',
        'options' => array(
            'name' => 'server_rcon',
            'id' => 'server_rcon',
            'value' => set_value('server_rcon'),
            'maxlength' => '32',
            'minlength' => '2',
            'title' => 'Izvēlies RCON paroli, ar kuru varēsi kontrolēt serveri.',
        ),
    ),
    
    'server_password' => array(
        'label' => 'Servera parole',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean',
        'options' => array(
            'name' => 'server_password',
            'id' => 'server_password',
            'value' => set_value('server_password'),
            'maxlength' => '32',
            'minlength' => '2',
            'title' => 'Parole, kuru būs jāzin katram lietotājam, kurš vēlēsies spēlēt ar tevi.',
        ),
    ),
    
    'name' => array(
        'label' => 'Hostname',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean',
        'options' => array(
            'name' => 'name',
            'id' => 'name',
            'value' => set_value('name'),
            'maxlength' => '32',
            'minlength' => '4',
            'title' => 'Serevera nosaukums, kurš parādās spēles serveru līstē.',
        ),
    ),
    
);