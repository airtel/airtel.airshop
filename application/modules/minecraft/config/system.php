<?php
/**
 * Minecraft system.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


$config['load_custom_js'] = TRUE;


/**
 * Minecraft services table structure
 */
$config['minecraft_table_structure'] = array(
    'id' => array('type' => 'INT', 'auto_increment' => TRUE),
    'username' => array('type' => 'VARCHAR', 'constraint' => '64'),
    'expires' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'module' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'service' => array('type' => 'VARCHAR', 'constraint' => '12'),
    'group' => array('type' => 'VARCHAR', 'constraint' => '64', 'null' => TRUE),
);


$config['submenu_names'] = array(
    'unban' => 'Lietotāju banu saraksts',
    'credits' => 'Kredītu tops',
    'amnesty' => 'Ieslodzīto lietotāju saraksts',
    'peaceful' => 'Peaceful frakciju saraksts',
    'groups' => 'Grupu lietotāji',
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
    
    'amnesty' => array(
        'login_required' => FALSE,
    ),
    
    'peaceful' => array(
        'login_required' => FALSE,
    ),
    
    'power' => array(
        'login_required' => FALSE,
    ),
    
    'groups' => array(
        'login_required' => FALSE,
    ),
    
    'exp' => array(
        'login_required' => FALSE,
    ),
    
    'broadcast' => array(
        'login_required' => FALSE,
    ),    
);


/**
 * Plugin commands
 */
$config['plugin_commands'] = array(

    'unban' => array(
        
        'figadmin' => array(
            'command_unban' => 'unban',
            'command_banlist' => '',
        ),
        
        'kiwiadmin' => array(
            'command_unban' => 'unban',
            'command_banlist' => '',
        ),
    ),
    
    'credits' => array(
        
        'essentials' => array(
            'command_give' => 'eco give',
            'command_top' => 'balancetop',
            'command_amount' => 'balance',
        ),
        
        'iconomy' => array(
            'command_give' => 'money give',
            'command_top' => 'money top',
            'command_amount' => 'money ',
        ),
    ),
    
    'amnesty' => array(
      
        'jail' => array(
            'command_unjail' => 'unjail',
            'command_prisoners' => 'jailcheck',
        ),
    ),
    
    'peaceful' => array(
        
        'factions' => array(
            'command_set_pf' => 'f peaceful',
            'command_del_pf' => 'f peaceful',
        ),
    ),
    
    'power' => array(
        
        'factions' => array(
            'command_add_power' => 'f powerboost faction',
        ),
    ),
    
    'groups' => array(
        
        'permissionsex' => array(
            'command_set_group' => 'pex user <user> group set <group> [world]',
            'command_del_group' => 'pex user <user> group remove <group> [world]',
        ),
    ),
    
    'exp' => array(
      
        'base' => array(
            'command_give_exp' => 'exp give',
        ),
    ),
    
    'broadcast' => array(
      
        'base' => array(
            'command_broadcast' => 'broadcast',
        ),
    ),
    
);


/**
 * Fields for minecraft services
 */
$config['fields_unban'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_name_ban',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__minecraft_check_name_ban',
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
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__minecraft_check_valid_user',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_amnesty'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_prisoner',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__minecraft_check_prisoner',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),  
);


$config['fields_peaceful'] = array(
    
    'faction' => array(
        'label' => 'Tava frakcija',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]',
        'options' => array(
            'name' => 'faction',
            'id' => 'faction',
            'value' => set_value('faction'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ), 
);


$config['fields_power'] = array(
    
    'username' => array(
        'label' => 'Tava frakcija',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
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


$config['fields_groups'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required valid_user',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__minecraft_check_valid_user',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),
    
    'group' => array(
        'label' => 'Izvēlies grupu',
        'type' => 'dropdown',
        'fill' => 'mandatory',
        'wizard_step' => 2,
        'php_validation' => 'xss_clean|alpha_numeric',
        'options' => 'id="group"',
        'value' => set_value('group'),
    ),
);


$config['fields_exp'] = array(
    
    'username' => array(
        'label' => 'Tavs lietotājvārds',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required check_online',
        'php_validation' => 'required|xss_clean|max_length[30]|min_length[2]|callback__minecraft_check_valid_user',
        'options' => array(
            'name' => 'username',
            'id' => 'username',
            'value' => set_value('username'),
            'maxlength' => '30',
            'minlength' => '2',
        ),
    ),
);


$config['fields_broadcast'] = array(
    
    'message' => array(
        'label' => 'Tava ziņa',
        'type' => 'input',
        'fill' => 'mandatory',
        'wizard_step' => 1,
        'ajax_validation' => 'required',
        'php_validation' => 'required|xss_clean|max_length[140]|min_length[5]',
        'options' => array(
            'name' => 'message',
            'id' => 'message',
            'value' => set_value('message'),
            'maxlength' => '140',
            'minlength' => '5',
        ),
    ),
);