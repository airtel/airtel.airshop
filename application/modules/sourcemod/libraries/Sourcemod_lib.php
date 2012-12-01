<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Sourcemod_lib
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
    
    
    /**
     * Encrypts a password.
     *
     * @param $password password to encrypt.
     * @return string.
     */
    function sm_encrypt_passwd($password, $salt = 'SB_SALT')
    {
        return sha1(sha1($salt . $password));
    }
    
    
    /**
     * Check if steam_id is valid
     * @param string $steam_id
     * @return boolean
     */
    function check_steam_id($authid)
    {
        //return (preg_match('/^STEAM_[01]:[01]:\d{3,10}$/', $authid)) ? TRUE : FALSE;
        return (preg_match('/^STEAM_0:(0|1):[1-9]{1}[0-9]{0,19}$/', $authid)) ? TRUE : FALSE;
    } 
    
    
    function reloadadmins($server_ip, $server_port, $server_rcon)
    {
        $config = array();
        $config['server_hostname'] = $server_ip;
        $config['server_port'] = $server_port;
        $config['server_rcon'] = $server_rcon;
        
        $this->CI->load->library('core/rcon_srcds', $config);
        $rcon_command = $this->CI->config->item('reload_command');
        $response = $this->CI->rcon_srcds->rcon($rcon_command);

        $resp = (isset($response[0])) ? $response[0] : '';
        
        log_message('debug', 'Module: sourcemod; Function: reloadadmins; action: reloadadmins; Response: '.$resp);
        
        return $response;
    }
    
    
    
}