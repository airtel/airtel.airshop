<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Ui
{
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function breadcrumb()
    {
        $output = $this->CI->load->view('core/breadcrumb_tpl', TRUE);
        
        return $output;
    }
    
    
    public function sms_sendtext($pricelist, $default_prices)
    {
        $curr_key = set_value('prices_sms');
        
        // If there was POST button pressed
        if( ! empty($curr_key))
        {
            // Change when adding countries support
            $first_country = array_shift(array_keys($this->CI->config->item('countries')));
            
            // key from POST
            $data['first_key'] = $curr_key;
        }
        // Staring from default
        else
        {
            // Default first country from list
            $first_country = array_shift(array_keys($this->CI->config->item('countries')));
            
            // Default key from current list
            $data['first_key'] = array_shift(array_keys($pricelist));
        }
        
        // Get real SMS price
        $data['price'] = $default_prices[$data['first_key']];
        
        // Default currency from current countries list
        $data['curr_currency'] = $this->CI->config->item($first_country, 'currency');
        
        // Default QR code data string, encoded to use in url.
        $data['qrdata'] = urlencode('SMSTO:'.$this->CI->config->item('short_number').':'.$this->CI->config->item('base_keyword').$data['first_key']);
        
        // Get HTML output
        $output = $this->CI->load->view('core/sms_sendtext_tpl', $data, TRUE);
        
        return $output;
    }
    
    
    /**
     * Performs actions with CI sessions and controlls system messages activities
     * @param type $unset
     * @return type
     */
    public function system_messages($unset = TRUE)
    {
        $message = $this->CI->session->userdata('message');
        
        if($message)
        {
            $msgarray = explode('{d}', $message);
            $message = $this->show_message($msgarray[1], $msgarray[0]);
            
            /**
             *  Unsetting message after we are done displaying it.
             *  This is needed because of flashdata will null only on page refresh,
             *  but we want to show it anywhere in any time without refreshing page
             */
            if($unset)
            {
                $this->CI->session->unset_userdata('message');
            }
            
            return $message;
        }
    }    
    
    
    /**
     * Outputs html with message in it
     * @param type $message
     * @param type $type
     * @return type
     */
    public function show_message($message, $type)
    {
        return '<div class="alert alert-'.$type.'""><a class="close" data-dismiss="alert">×</a><strong>'.ucfirst($type).':</strong> '.$message.'</div>';
    }   
    
    
    function show_access_flags()
    {
        $output = '';
        
        // Get specific flags for active module and active service
        if($this->CI->module->active_module == 'amx')
        {
            $flags = $this->CI->module->services[$this->CI->module->active_service]['amx_flags'];
            $flags_descr = $this->CI->config->item('amx_access_flags_description');
        }
        elseif($this->CI->module->active_module == 'sourcemod')
        {
            $flags = $this->CI->module->services[$this->CI->module->active_service]['access_group']['flags'];
            $flags_descr = $this->CI->config->item('sm_access_flags_description');
        }
        
        // Get flags description
        
        
        // Split flags
        $flags_array = str_split($flags, 1);
        
        // Prepare output
        $output .= '<b>Tiesības, kuras iegūsi pasūtot šo pakalpojumu:</b><br /><br />';
        $output .= '<table>';
        
        foreach($flags_array as $key => $value):

            $output .=' <tr><td style="width: 30px;"><b>'.$value.'</b></td><td>'.$flags_descr[$value].'</td></tr>';

        endforeach;
        
        $output .= '</table><br />';
        
        
        return $output;
    }
    
    

    
}