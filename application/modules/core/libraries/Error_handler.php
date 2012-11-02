<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class Error_handler {
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }    
    
    
    public function show_error($type, $message, $module = NULL, $service = NULL)
    {
        $this->CI->session->set_userdata('message', $type.'{d}'.$message);
        
        if($this->CI->uri->segment(4) != 'error')
        {
            redirect($this->CI->uri->segment(1).'/index/'.$this->CI->uri->segment(3).'/error');
        }
    }
    

}