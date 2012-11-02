<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Validation
{
    
    
    private $CI;
    
    
    function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    function validation_parser($fields = array(), $pay_method = NULL)
    {
        $pay_fields = $this->CI->config->item('fields_'.$pay_method);
        /**
         * Service specific fields
         */
        foreach($fields as $field => $field_opts)
        {
            if($field_opts['type'] != 'text')
            {
                $this->CI->form_validation->set_rules($field, '', $field_opts['php_validation']);
            }
        }
        
        
        /**
         * Payment method mandatory fields from config
         */
        foreach($pay_fields as $field => $field_opts)
        {
            $this->CI->form_validation->set_rules($field, '', $field_opts['php_validation']);
        }
        
        
        return ($this->CI->form_validation->run() == FALSE) ? FALSE : TRUE;
    }
    
    
}