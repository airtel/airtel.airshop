<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Amx_lib
{
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function check_login()
    {
        return FALSE;
    }
    
    
    function reloadadmins($server_address, $server_rcon)
    {
        $addr = explode(':', $server_address);
        
        $config = array();
        $config['server_ip'] = $addr['0'];
        $config['server_port'] = $addr['1'];
        $config['server_password'] = $server_rcon;
        
        $this->CI->load->library('core/rcon_hlds', $config);
        $rcon_command = $this->CI->config->item('reload_command');
        $response = $this->CI->rcon_hlds->rconcommand($rcon_command);
        
        log_message('debug', 'Module: amx; Function: reloadadmins; action: reloadadmins. Response: '.$response);
        
        return $response;
    }
    
    
}