<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Ipb_db {

    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function get_ibp_settings()
    {
        $settings = $this->CI->config->item('ipb');
        $config_path = $this->CI->config->item('ipb_board_path') . 'conf_global.php';
        
        // We don't want additional problems with extra shop configuration, so we will just
        // suppress errors because in IPBv3 config file there is mistake with define function.
        @require($config_path);

        $settings['hostname'] = $INFO['sql_host'];
        $settings['username'] = $INFO['sql_user'];
        $settings['password'] = $INFO['sql_pass'];
        $settings['database'] = $INFO['sql_database'];
        $settings['dbprefix'] = $INFO['sql_tbl_prefix'];
        $settings['ipb_version'] = ( ! defined('IN_DEV')) ? '2' : '3';
        $settings['id_column'] = ($settings['ipb_version'] == 3) ? 'member_id' : 'id';

        return $settings;
    }
    
    
}