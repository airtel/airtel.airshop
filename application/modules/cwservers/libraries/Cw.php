<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Cw
{
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function prepare_start($options, $ip, $port, $hostname, $rcon, $password, $screen_name = NULL)
    {
        $screen_name = ( ! empty($screen_name)) ? $screen_name : $this->CI->module->active_service.'-'.$port;
        
        $string = 'cd ' . $options['server_dir'] . ' && screen -AmdS '. $screen_name . ' ' . $options['script'];

        foreach($options['args'] as $arg => $value)
        {
            $string .= ' ' . $arg . ' ' . $value;
        }
        
        $string .= ' ' . $options['extra'];
        
        $string .= ' +ip ' . $ip;
        $string .= ' +port ' . $port;
        
        // User defined settings
        $string .= ' +hostname ' . $hostname;
        $string .= ' +rcon_password ' . $rcon;
        $string .= ' +sv_password ' . $password;
        
        return $string;
    }
    
    
    public function ping_host($settings = array(), $throw_errors = FALSE)
    {
        if( ! $socket = @fsockopen($settings['hostname'], $settings['port'], $errno, $errstr, 1))
        {
            if($throw_errors)
            {
                $this->CI->error_handler->show_error('error', 'Serveris nav sasniedzams. Mēģiniet vēlāk.');
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            fclose($socket);
            return TRUE;
        }
    }
    
    
    public function search_in_output($needle, $output)
    {
        $is_found = FALSE;
        
        foreach($output as $o)
        {
            if(strpos($o, $needle) !== FALSE)
            {
                return $is_found = TRUE;
            }
        }
        
        return $is_found;
    }
    
    
}