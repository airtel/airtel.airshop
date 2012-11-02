<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Muonline_lib
{
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    /**
     * 
     */
    public function check_login($check_only = FALSE)
    {
        $mu_username = $this->get_username();
        
        
        if($check_only == FALSE)
        {
            if($this->CI->uri->rsegment(2) == 'index')
            {   
                if($this->CI->uri->rsegment(3) == 'gmaccess' OR $this->CI->uri->rsegment(3) == 'skillbooster')
                {
                    
                    if($mu_username === FALSE OR empty($mu_username))
                    {
                        $this->CI->session->set_userdata('message', 'warning{d}Lai izmantotu šo pakalpojumu ir jāielogojas iekš Web-shop!');
                        redirect($this->CI->module->active_module.'/login');
                    }
                }
            }
        }
        else
        {
            if($mu_username === FALSE)
            {
                // do redirect ?
                return TRUE;
            }
            else
            {
                // do redirect ?
                return FALSE;
            }
        }
    }
    
    
    /**
     * Function tries to get username out of cookies
     * @return type
     */
    public function get_username()
    {
        $cookie_names = $this->CI->config->item('cookie_names');
        $mu_username = FALSE;
        
        foreach($cookie_names as $cookie)
        {
            $mu_username = $this->CI->input->cookie($cookie, TRUE);
            
            if($mu_username !== FALSE && ! empty($mu_username))
            {
                break;
            }
        }
        
        return $mu_username;
    }
    
    
    /**
     * 
     * @param type $username
     * @param type $expiration
     */
    function add_vip_member($username, $expiration)
    {
        $data = '"' . $username . '"' . "\t\t\t" . '// Added by airtel shop. Expires: ' . date('d.m.Y H:i:s', $expiration) . "\n";
        
        if ( ! write_file($this->CI->module->services['vipserver']['file_path'], $data, 'a+'))
        {
            log_message('error', 'Muonline service: Failed to add VIP member! Member: '.$username.'. Filename: ' . $this->CI->module->services['vipserver']['file_path']);
            $this->CI->error_handler->show_error('error', 'Kļūda pievienojot VIP servera sarakstā. Lūdzu vērsties pie servera administrācijas!');
        }
    }
    
    
    /**
     * 
     * @param type $username
     */
    function del_vip_member($username)
    {
        $data = read_file($this->CI->module->services['vipserver']['file_path']);
        $new_data = '';
        $members = explode("\n", $data);
        
        foreach($members as $m)
        {
            preg_match('"([^\\"]+)"', $m, $result);
            
            if( ! empty($result))
            {
                if($result[0] != $username)
                {
                    $new_data .= $m . "\n";
                }
            }
        }
        
        if ( ! write_file($this->CI->module->services['vipserver']['file_path'], $new_data))
        {
            log_message('error', 'Muonline service: Failed to delete VIP member from file! Member: '.$username.'. Filename: ' . $this->CI->module->services['vipserver']['file_path']);
        }
    }
    
    
    /**
     * 
     * @param type $username
     * @param type $expiration
     */
    function update_vip_member($username, $expiration)
    {
        $data = read_file($this->CI->module->services['vipserver']['file_path']);
        $new_data = '';
        $members = explode("\n", $data);

        foreach($members as $m)
        {
            preg_match('"([^\\"]+)"', $m, $result);
            
            if( ! empty($result))
            {
                if($result[0] == $username)
                {
                    $new_data .= '"' . $result[0] . '"' . "\t\t\t" . '// Added by airtel sms shop. Expires: ' . date('d.m.Y H:i:s', $expiration) . "\n";
                }
                elseif($result[0] != $username)
                {
                    $new_data .= $m . "\n";
                }
            }
        }
        
        if ( ! write_file($this->CI->module->services['vipserver']['file_path'], $new_data))
        {
            log_message('error', 'Muonline service: Failed to update VIP member! Member: '.$username.'. Filename: ' . $this->CI->module->services['vipserver']['file_path']);
            $this->CI->error_handler->show_error('error', 'Kļūda atjaunojot VIP servera sarakstu. Lūdzu vērsties pie servera administrācijas!');
        }
    }
    
    
    /**
     * 
     * @param type $username
     * @return boolean
     */
    function search_vip_member($username)
    {
        $data = read_file($this->CI->module->services['vipserver']['file_path']);
        $members = explode("\n", $data);
        $member_exists = FALSE;
        
        foreach($members as $m)
        {
            preg_match('"([^\\"]+)"', $m, $result);
            
            if( ! empty($result))
            {
                if($result[0] == $username)
                {
                    $member_exists = TRUE;
                    break;
                }
            }
        }
        
        return $member_exists;
    }
    
    
}