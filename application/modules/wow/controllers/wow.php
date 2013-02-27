<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Wow extends MX_Controller {

    
    /**
     * General variables
     */
    public $priceplan_raw = array();
    
    public $priceplan = array();
    
    
    /**
     * Constructor
     */
    public function __construct()        
    {
        parent::__construct();
        
        // Init library
        $this->load->library('core/module');

        // Module specific variables and functions initialization and execution
        $this->module->module_init();
        
        // Init db models
        //$this->module->db_init();
        $this->load->model('wow_model');

        // Private actions initialization
        if($this->uri->segment(4) != 'error')
        {
            $this->individual_init();
        }
        
        // Validation options initialization
        $this->validation_init();
    }
    
    
    private function validation_init()
    {
        // Load form validation library
        $this->load->library('form_validation');        
        
        // Set Super Class pointer
        $this->form_validation->CI =& $this;
        
        // Set validation delimiters
        $this->form_validation->set_error_delimiters('', '');     
        
        // Load our custom validation library
        $this->load->library('core/validation');
    }
    
    
    private function individual_init()
    {
        //
    }
    
    
    private function priceplan_init()
    {
        $this->priceplan_raw = $this->system->build_priceplan_raw();
        
        // Prepare priceplan for all available pay methods
        $this->priceplan = $this->system->build_priceplan();
    }
    
    
    public function index()
    {
        // Load prices
        $this->priceplan_init();
        
        // Get fields for module active service
        $data['fields'] = $this->config->item('fields_'.$this->module->active_service);

        
        /**
         * Lets begin work with inputs here:
         */
        if($this->input->post('submit'))
        {
            $pay_method = $this->validation->get_paymethod();
            $pay_code = $this->input->post($pay_method.'code');
            $post_price = $this->input->post('prices_'.$pay_method);
            
            
            /**
             * Starting validation check
             */
            if($this->validation->validation_parser($data['fields'], $pay_method) == FALSE)
            {
                log_message('debug', $this->module->active_module.'/'.$this->module->active_service.': validation failed!');
                $this->session->set_userdata('message', 'error{d}Lūdzu pārbaudiet visus ievadītos datus!');
            }
            else 
            {
                // Get answer about code from airtel api
                $response = $this->{$pay_method.'code'}->code_response_wsearch($pay_code, TRUE);
                
                
                // Check airtel api response
                if($response['price'] >= $post_price && array_key_exists($response['price'], $this->priceplan[$pay_method]))
                {
                    $goods_amount = $this->priceplan_raw[$pay_method][$this->module->active_service][$response['price']];

                    // Unban ( all types )
                    if(strpos($this->module->active_service, 'unban_') !== FALSE)
                    {
                        
                        // Remove ban
                        $this->wow_model->{$this->module->active_service}($this->input->post('expression'));
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Uzstādītais ban tips ir veiksmīgi noņemts');
                        
                    }
                    
                    // Gold
                    elseif($this->module->active_service == 'gold')
                    {
                        
                        // Add Gold
                        $this->wow_model->add_gold($this->input->post('character'), $goods_amount);

                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Lietotājam <strong>'.$this->input->post('character').'</strong> tika piešķirts <strong>' . $goods_amount . '</strong> Gold');
                        
                    }
                    
                    // Exp
                    elseif($this->module->active_service == 'exp')
                    {
                        
                        // Add Gold
                        $this->wow_model->add_exp($this->input->post('character'), $goods_amount);

                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Lietotājam <strong>'.$this->input->post('character').'</strong> tika piešķirts <strong>' . $goods_amount . '</strong> Exp');
                        
                    }
                    
                    // Levelup
                    elseif($this->module->active_service == 'levelup')
                    {
                        
                        // Add level
                        $this->wow_model->add_levelup($this->input->post('character'), $goods_amount);

                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Lietotājam <strong>'.$this->input->post('character').'</strong> tika piešķirts <strong> + ' . $goods_amount . '</strong> LevelUp');
                        
                    }
                    
                    // Donate points
                    elseif($this->module->active_service == 'donate_points')
                    {
                        
                        // Add level
                        $this->wow_model->add_donate_points($this->input->post('expression'), $goods_amount);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Lietotājam <strong>'.$this->input->post('expression').'</strong> tika piešķirts <strong> + ' . $goods_amount . '</strong> Donate points');
                        
                    }
                    
                    
                    // Activate {pay_method} code
                    $this->{$pay_method.'code'}->code_response_wsearch($pay_code, $this->module->testing);
                    
                    // Redirect
                    redirect($this->module->active_module.'/index/'.$this->module->active_service);
                
                }
                elseif($response['answer'] == 'code_not_found')
                {
                    $this->session->set_userdata('message', 'error{d}Ir ievadīts neeksistējošs "'.$pay_method.'" kods!');
                }
                else
                {
                    $this->session->set_userdata('message', 'error{d}Ir ievadīts nepareizas vērtības "'.$pay_method.'" kods!');
                }
            }
        }
        
        
        
        
        /**
         * Load HTML
         */
        $this->base->load_header($this->module->active_module);
        $this->load->view($this->module->active_module.'_tpl', $data);
        $this->base->load_footer();     
    }
    
    
    /**
     * Custom wow functions
     * Custom wow AJAX handler
     * @return type
     */
    public function wow_ajax_call()
    {
        if( ! $this->input->is_ajax_request()) show_error('Direct access denied.', 500);
        
        $action = $this->uri->segment(3);
        
        if(strlen($user_input = $this->input->post('user_input')) < 2) exit();

        $response = $this->{'_wow_'.$action}($user_input);
        
        echo ($response == TRUE) ? 'TRUE' : 'FALSE';
    }
    
    
    /**
     * Custom minecraft functions
     * Checks if entered user exists
     * @param type $username
     */
    public function _wow_valid_user($username)
    {
        if($this->wow_model->search_valid_user($username, 'characters', 'name') == FALSE)
        {
            $this->form_validation->set_message('_wow_valid_user', 'Šāds lietotājs datubāzē nav atrasts');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    public function _wow_valid_web_user($username)
    {
        if($this->wow_model->search_valid_web_user($username) == FALSE)
        {
            $this->form_validation->set_message('_wow_valid_web_user', 'Šāds lietotājs datubāzē nav atrasts. Ielogojies vismaz vienu reizi web-portālā');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    /**
     * Custom minecraft functions
     * Check if ban by name exists
     * @param type $username
     */
    public function _wow_check_ban_account($account)
    {
        if( ! $this->wow_model->check_ban_account($account))
        {
            $this->form_validation->set_message('_wow_check_ban_account', 'Šādam lietotāja akauntam bans mūsu serverī nav atrasts!');
            return FALSE;
        }    
        else
        {
            return TRUE;
        }
    }
    
    
    public function _wow_check_ban_character($character)
    {
        if( ! $this->wow_model->check_ban_character($character))
        {
            $this->form_validation->set_message('_wow_check_ban_character', 'Šādam lietotāja characterim bans mūsu serverī nav atrasts!');
            return FALSE;
        }    
        else
        {
            return TRUE;
        }
    }
    
    
    public function _wow_check_ban_ip($ipaddress)
    {
        if( ! $this->wow_model->check_ban_ip($ipaddress))
        {
            $this->form_validation->set_message('_wow_check_ban_ip', 'Šādai IP adresei bans mūsu serverī nav atrasts!');
            return FALSE;
        }    
        else
        {
            return TRUE;
        }
    }
    
    
    public function _wow_check_online($username)
    {
        if($this->wow_model->check_online($username) === TRUE)
        {
            $this->form_validation->set_message('_wow_check_online', 'Lūdzu vispirms izej no spēles.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
}