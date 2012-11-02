<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class War_lib
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
    
    
    public function prepare_groups($groups)
    {
        $data = array();
        
        foreach($groups as $group => $value)
        {
            $data[$group] = $value['title'];
        }
        
        return $data;
    }
    
    
    
}