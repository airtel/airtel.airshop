<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Module
{
    
    
    private $CI;
    
    public $services = array();
    
    public $active_service = NULL;
    
    public $active_module = NULL;
    
    public $sms_prices = array();
    
    public $pay_methods = array();
    
    public $testing = NULL;
    
    public $loaded_modules = array();
    
    
    public function __construct()
    {
        // Get superclass pointer
        $this->CI =& get_instance();
        
        // Load Base modules
        $this->CI->load->module('base');
        $this->CI->load->module('smscode');
        $this->CI->load->module('ibankcode');
        $this->CI->load->module('paypalcode');

        // Debug
        //$this->CI->output->enable_profiler(TRUE);
        
        // Geting active module from router
        $this->active_module = $this->CI->router->fetch_class();
        
        // Loading module specific configs
        $this->CI->load->config($this->active_module.'/master');
        $this->CI->load->config($this->active_module.'/system');
        $this->CI->load->config($this->active_module.'/priceplan');
        
        $this->CI->load->config($this->active_module.'/database');
        
        // Initializing loaded services
        $this->services = $this->CI->config->item($this->active_module.'_services');
        
        // Module and Service settings
        $this->active_service = $this->CI->uri->rsegment(3);
        
        // SMS prices
        $this->sms_prices = $this->CI->smscode->get_prices('lv');
        
        // Set payment methods
        $this->pay_methods = $this->CI->config->item($this->active_module.'_payments');
        
        // Load code testing param
        $this->testing = $this->CI->config->item('testing');
        
        // Get loaded modules
        $this->loaded_modules = $this->CI->config->item('base_modules');
        
        // Load core libraries
        $this->CI->load->library('core/error_handler');
        $this->CI->load->library('core/ui');
        $this->CI->load->library('core/system');
    }

    
    /**
     * Loads module database and core database models
     */
    public function db_init()
    {
        // Module sql model loading
        $this->CI->load->model($this->active_module.'/'.$this->active_module.'_model');
        
        // Core model loading
        $this->CI->load->model('core/core_model');
    }
    
    
    /**
     * Initialization activities of module
     */
    public function module_init()
    {
        $this->security_layer();
        
        // Setting active service
        if(empty($this->active_service) && $this->CI->uri->rsegment(2) == 'index')
        {
            $this->active_service = array_shift(array_keys($this->services));
            redirect($this->active_module.'/index/'.$this->active_service);
        }
        
        // Unsetting first price from pricelist
        unset($this->sms_prices['5']);
        
    }
    
    
    /**
     * 
     */
    private function security_layer()
    {
        // Access check to modules
        if($this->CI->uri->segment(1))
        {
            if( ! array_key_exists($this->CI->uri->segment(1), $this->loaded_modules))
            {
                show_error('Access to disabled base modules is restricted !');
            }
        }

        if($this->CI->uri->segment(2) && $this->CI->uri->segment(2) == 'index')
        {
            if($this->CI->uri->rsegment(3) != '')
            {
                if( ! array_key_exists($this->CI->uri->rsegment(3), $this->services))
                {
                    show_error('Access to disabled services is restricted !');
                }
            }
        }
    }

    
}