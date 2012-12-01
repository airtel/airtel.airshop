<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class System
{
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function build_priceplan_raw()
    {
        $priceplan_raw = array();
    
        // Foreach payment methods make priceplan
        foreach($this->CI->module->pay_methods as $paymethod)
        {
            // Temporary fix
            $countrycode = ($paymethod == 'paypal') ? 'eu' : 'lv';
            
            $priceplan_raw[$paymethod] = $this->CI->config->item($countrycode, $this->CI->module->active_module.'_prices_'.$paymethod);
        }
        
        return $priceplan_raw;
    }
    
    
    public function build_priceplan($dimension = FALSE)
    {
        // Define some variables
        $priceplan = array();
        
        // Values to replace
        $values = array('%price', '%curr', '%amount');        
        
        // Get loaded services for selected module
        $services = $this->CI->config->item($this->CI->module->active_module.'_services');
        
        // Foreach payment methods make priceplan
        foreach($this->CI->module->pay_methods as $paymethod)
        {
            // Temporary fix
            $countrycode = ($paymethod == 'paypal') ? 'eu' : 'lv';

            // Get config item name for each payment method. Example: minecraft_prices_sms
            $config_item_name = $this->CI->module->active_module.'_prices_'.$paymethod;
            
            // Get pricelist for selected module
            $prices = $this->CI->config->item($countrycode, $config_item_name);

            // Get selected service prices
            $service_pricelist = ($dimension == FALSE OR $dimension == 'GROUPS') ? $prices[$this->CI->module->active_service] : $prices[$this->CI->module->active_service][$dimension];
            
            // Foreach service price array
            foreach($service_pricelist as $key => $value)
            {
                if($paymethod == 'sms')
                {
                    if(isset($this->CI->module->sms_prices[$key]))
                    {
                        $source = $this->CI->module->sms_prices[$key];
                    }
                    else 
                    {
                        $this->CI->error_handler->show_error('error', 'Modulis: <strong>"'.$this->CI->module->active_module.'"</strong>. Pakalpojuma <strong>"'.$this->CI->module->active_service.'"</strong> priceplan.php konfigurācijas fails satur nepareizu sms cenas kodu - <strong>"'.$key.'"</strong>. Lūdzu izlabojiet konfigurācijas failu.');
                    }
                }

                elseif($paymethod == 'ibank' OR $paymethod == 'paypal')
                    $source = $key;
                
                // Replace values with params from price arrays
                $replacements = array($source, $this->CI->config->item($countrycode, 'currency'), $value);
                
                // Prepeare pricelist for selected service with all available payment methods
                $priceplan[$paymethod][$key] = str_replace($values, $replacements, $services[$this->CI->module->active_service]['prices_display']);
            }
        }
        return $priceplan;
    }

    
    public function build_priceplan_groups()
    {
        // Define some variables
        $priceplan = array();
        
        // Values to replace
        $values = array('%price', '%curr', '%amount');
        
        // Get loaded services for selected module
        $services = $this->CI->config->item($this->CI->module->active_module.'_services');
        
        // Foreach payment methods make priceplan
        foreach($this->CI->module->pay_methods as $paymethod)
        {
            // Temporary fix
            $countrycode = ($paymethod == 'paypal') ? 'eu' : 'lv';
            
            // Get config item name for each payment method. Example: minecraft_prices_sms
            $config_item_name = $this->CI->module->active_module.'_prices_'.$paymethod;
            
            // Get pricelist for selected module
            $prices = $this->CI->config->item($countrycode, $config_item_name);
            
            // Get selected service prices
            $service_pricelist = $prices[$this->CI->module->active_service];
        
            foreach($service_pricelist as $key => $value)
            {
                foreach($value as $index => $element)
                {
                    if($paymethod == 'sms')
                        $source = $this->CI->module->sms_prices[$index];

                    elseif($paymethod == 'ibank' OR $paymethod == 'paypal')
                        $source = $index;
                    
                    // Replace values with params from price arrays
                    $replacements = array($source, $this->CI->config->item($countrycode, 'currency'), $element);
                    
                    // Prepeare pricelist for selected service with all available payment methods
                    $priceplan[$paymethod][$key][$index] = str_replace($values, $replacements, $services[$this->CI->module->active_service]['prices_display']);
                }
            }
            
        }
        return $priceplan;
    }        
            
            
    
    
}