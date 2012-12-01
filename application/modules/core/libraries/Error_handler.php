<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class Error_handler {
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }    
    
    
    /**
     * Function inserts error message in log file, then redirects to module error page and shows
     * error message
     * 
     * @param type $type
     * @param type $message
     * @param type $module
     * @param type $service
     */
    public function show_error($type, $message, $module = NULL, $active_service = NULL)
    {
        $this->CI->session->set_userdata('message', $type.'{d}'.$message);
        
        if($this->CI->uri->segment(4) != 'error')
        {
            // Log error and do that only once
            log_message('error', $message);
            
            // Get active service
            $active_service = ( ! $service = $this->CI->uri->segment(3)) ? array_shift(array_keys($this->CI->module->services)) : $service;
            
            // Redirect and show error message
            redirect($this->CI->uri->segment(1).'/index/'.$active_service.'/error');
        }
    }
    

}