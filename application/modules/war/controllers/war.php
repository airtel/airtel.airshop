<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class War extends MX_Controller {

    
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
        $this->module->db_init();

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
        // Load amx library
        $this->load->library('war/war_lib');
        

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
        
        $data = array();
        
        if($this->uri->segment(4) != 'error')
        {
            // Get fields for module active service
            if(substr($this->module->active_service, 0, 3) == 'exp')
            {
                $data['fields'] = $this->config->item('fields_exp');

                $races = $this->war_model->get_server_races($this->module->services[$this->module->active_service]['db_array_name']);

                if(count($races) > 0)
                {
                    foreach($races as $race)
                    {
                        $data['fields']['races']['data'][$race->race_id] = $race->race_name;
                    }
                }

            }
            else
            {
                $data['fields'] = $this->config->item('fields_'.$this->module->active_service);
            }
        }
        
        
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
                    $db = $this->module->services[$this->module->active_service]['db_array_name'];
                    $username = $this->input->post('username');
                        
                    // Exp
                    if(substr($this->module->active_service, 0, 3) == 'exp')
                    {
                        $race_id = $this->input->post('races');
                        
                        // If user does not exist then set shop message and redirect
                        if(! $user = $this->war_model->get_player($db, $username, $race_id))
                        {
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'error{d}Šādas rases spēlētajs nav atrasts. Pārbaudi vai spēlē ilgāk!');

                            // Redirect
                            redirect($this->module->active_module.'/index/'.$this->module->active_service);
                            
                        }
                        // User exists.
                        else
                        {
                            
                            // Add EXP
                            $this->war_model->add_exp($db, $user->player_id, $race_id, $goods_amount);
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Lietotājam <strong>'.$user->player_name.'</strong> tika pieškirti <strong>' . $goods_amount . '</strong> EXP');
                            
                        }
                        
                    }
                    
                    // ZmAmmo
                    elseif($this->module->active_service == 'zmammo')
                    {
                        
                        // If user does not exist then set shop message and redirect
                        if(! $user = $this->war_model->get_ammo_player($db, $username))
                        {
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'error{d}Šādas rases spēlētajs nav atrasts. Pārbaudi vai spēlē ilgāk!');

                            // Redirect
                            redirect($this->module->active_module.'/index/'.$this->module->active_service);
                            
                        }
                        // User exists.
                        else
                        {
                            
                            // Add Ammo
                            $this->war_model->add_ammo($db, $username, $goods_amount);
                        
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Lietotājam <strong>'.$username.'</strong> tika pieškirti <strong>' . $goods_amount . '</strong> Ammo');
                            
                        }
                        
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
     * Custom war functions
     * Custom war AJAX handler
     * @return string TRUE / FALSE ( not boolean ! )
     */
    public function war_ajax_call()
    {
        if( ! $this->input->is_ajax_request()) show_error('Direct access denied.', 500);
        
        $action = $this->uri->segment(3);
        
        if(strlen($user_input = $this->input->post('user_input')) < 2) exit();
        
        
        if($action == 'check_valid_exp_user')
        {
            $response = $this->{'_war_'.$action}($user_input, $this->uri->segment(4), $this->input->post('race_id'));
        }
        else
        {
            $response = $this->{'_war_'.$action}($user_input, $this->uri->segment(4));
        }

        echo ($response == TRUE) ? 'TRUE' : 'FALSE';
    }
    
    
    public function _war_check_valid_exp_user($username, $service, $race_id)
    {
        $dbname = $this->module->services[$service]['db_array_name'];
        $user = $this->war_model->get_player($dbname, $username, $race_id);
        
        if($user !== FALSE)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    
    public function _war_check_ammo_user($username, $service)
    {
        $dbname = $this->module->services[$service]['db_array_name'];
        $user = $this->war_model->get_ammo_player($dbname, $username);
        
        if($user !== FALSE)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    
}