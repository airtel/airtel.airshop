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
        $this->load_modules();

        // Debug
        //$this->CI->output->enable_profiler(TRUE);
        
        // Get active module from router
        $this->active_module = $this->CI->router->fetch_class();
        
        // Get active service
        $this->active_service = $this->CI->uri->rsegment(3);        
        
        // Load Error handler
        $this->CI->load->library('core/error_handler');
        
        // Load ui library
        $this->CI->load->library('core/ui');
        
        // Load active module configs
        $this->load_configs();
        
        // Initializing loaded services
        $this->services = $this->CI->config->item($this->active_module.'_services');
        
        // SMS prices
        $this->sms_prices = $this->CI->smscode->get_prices('lv');
        
        // Set payment methods
        $this->pay_methods = $this->CI->config->item($this->active_module.'_payments');
        
        // Load code testing param
        $this->testing = $this->CI->config->item('testing');
        
        // Get loaded modules
        $this->loaded_modules = $this->CI->config->item('base_modules');
        
        // Load core libraries
        $this->CI->load->library('core/system');
    }

    
    /**
     * Loads module database and core database models
     */
    public function db_init()
    {
        // Check connection settings before loading database
        $this->db_check_connection();
        
        // On error show we dont want extra code to be loaded
        if($this->CI->uri->segment(4) != 'error')
        {
            // Module sql model loading
            $this->CI->load->model($this->active_module.'/'.$this->active_module.'_model');
            
            // Core model loading
            $this->CI->load->model('core/core_model');
        }
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
    
    
    /**
     * Checks for database connection
     */
    private function db_check_connection()
    {
        // Load basic db settings
        if($this->active_module == 'war')
        {
            if($this->active_service == 'check_valid_exp_user' OR $this->active_service == 'check_ammo_user')
            {
                $active_service = array_shift(array_keys($this->services));
                $settings = $this->CI->config->item($this->services[$active_service]['db_array_name']);
            }
            else
            {
                $settings = $this->CI->config->item($this->services[$this->active_service]['db_array_name']);
            }
        }
        else
        {
            $settings = $this->CI->config->item($this->active_module);
        }
        
        
        // Workaround for IPB module
        if($this->active_module == 'ipb')
        {
            $settings = $this->CI->ipb_db->get_ibp_settings();
        }
        
        // Format DSN string from input
        $dsn = $settings['dbdriver'].'://'.$settings['username'].':'.$settings['password'].'@'.$settings['hostname'];
        
        // Load database and dbutil
        $this->CI->load->database($dsn, FALSE, TRUE);
        $this->CI->load->dbutil();
        
        // Check connection details
        if( ! $this->CI->dbutil->database_exists($settings['database']))
        {
            $this->CI->error_handler->show_error('error', 'Nav iespējams pieslēgties pie datubāzes.');
        }
        
        //unset($this->db);
        $this->CI->db->close();
    }
    
    
    /**
     * Function loads base shop modules
     */
    public function load_modules()
    {
        $modules = array('base', 'smscode', 'ibankcode', 'paypalcode');
        
        foreach($modules as $module)
        {
            $this->CI->load->module($module);
        }
    }
    
    
    /**
     * Function loads active module configs
     * Checks if each module config exists and loads it
     * If config does not exist executes error handler function.
     */
    public function load_configs()
    {
        // Set needed configs for each module
        $configs = array('master', 'database', 'priceplan', 'system');
        
        foreach($configs as $cfg)
        {
            if(file_exists(APPPATH . 'modules/'.$this->active_module.'/config/'.$cfg.'.php'))
            {
                $this->CI->load->config($this->active_module.'/'.$cfg);
            }
            else
            {
                $message = 'Moduļa konfigurācijas fails "'.$this->active_module.'/config/'.$cfg.'.php" nav atrasts! Iespējams ka šī ir jauna shop instalācija un tādā gadījumā ir javeic moduļa konfigurācijas faila pārsaukšana no '.$cfg.'.example.php uz '.$cfg.'.php';
                log_message('error', $message);
                show_error($message);
            }
        }
    }

    
}