<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Amx extends MX_Controller {

    
    /**
     * General variables
     */
    public $priceplan_raw = array();
    
    public $priceplan = array();
    
    
    /**
     * AMX variables
     */
    public $servers = array();
    
    public $settings = array();
    
    
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
        $this->load->library('amx/amx_lib');

        // Services table checking and setup
        $this->core_model->check_table($this->amx_model->sql_services_table, $this->amx_model->table_structure, TRUE);

        // Clear expired players data
        $this->amx_model->clear_expired($this->amx_model->sql_services_table);

        // Set hlds servers for module
        if($this->uri->segment(2) == 'index' && $this->module->active_service != 'unban')
        {
            $this->servers = $this->amx_model->get_servers($this->module->services[$this->module->active_service]['exclude_servers']);
        }

        $this->settings = $this->config->item('services_settings');
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
        $data['fields'] = ($this->module->active_service == 'unban') ? $this->config->item('fields_'.$this->module->active_service) : $this->config->item('fields_amx');

        
        if($this->module->active_service != 'unban')
        {
            foreach($this->servers as $server)
            {
                $data['fields']['servers']['data'][$server->id] = $server->hostname;
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

                    
                    // Unban
                    if($this->module->active_service == 'unban')
                    {
                        // Remove ban
                        $this->amx_model->del_ban($this->input->post('ipaddress'));
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}IP adrese <strong>'.$this->input->post('ipaddress').'</strong> tika dzēsta no banu datubāzes.');
                    }
                    
                    // Subscription services
                    elseif($this->settings[$this->module->active_service]['type'] == 'subscription')
                    {
                        $username = $this->input->post('username');
                        $password = ($this->config->item('login_auth_type') == 'md5') ? md5($this->input->post('password')) : $this->input->post('password');
                        $server_id = $this->input->post('servers');
                        $amx_flags = $this->module->services[$this->module->active_service]['amx_flags'];
                        
                        
                        // Search for player by his username in selected server
                        // If player found then update actions neeed to be performed
                        if($this->amx_model->search_player($username, $server_id))
                        {
                            
                            // Get player data
                            $player_data = $this->amx_model->get_player($username, $server_id);
                            
                            
                            // Lets check entered password
                            if($player_data->password == $password)
                            {
                                
                                // Check if new access type is same as already bought one.
                                // If they are same type then we need just to update expire time
                                if($player_data->service == $this->module->active_service)
                                {
                                    
                                    // Update expire time
                                    $this->amx_model->add_service_data($player_data->admin_id, $this->module->active_module, $this->module->active_service, $goods_amount, $server_id);
                                    
                                    // Set shop message
                                    $this->session->set_userdata('message', 'info{d}'.$username.' veiksmīgi <strong>pagarināja</strong> "'.ucfirst($this->module->active_service).'" pakalpojumu uz '.$goods_amount.' dienām!');
                                    
                                }
                                
                                // Player bought another service for same server. We need to upgrade 
                                // his admin account and add one more service into shop services table.
                                else
                                {
                                    
                                    // Upgrade admin's amx flags in amx admins table
                                    $this->amx_model->update_admin($player_data->admin_id, $amx_flags);
                                    
                                    // Add new entry in services table
                                    $this->amx_model->add_service_data($player_data->admin_id, $this->module->active_module, $this->module->active_service, $goods_amount, $server_id);
                                    
                                    // Do reload admins
                                    // amx reload admins ...
                                    
                                    // Set shop message
                                    $this->session->set_userdata('message', 'info{d}<strong>"'.ucfirst($this->module->active_service).'"</strong> uzlikts kā aktīvais pakalpojums lietotājam <strong>'.$username.'.</strong> Pakalpojuma darbības termiņš <strong>'.$goods_amount.'</strong> dienas!');
                                    
                                }
                            }
                            
                            // Wrong password. Set shop message and redirect !
                            else
                            {
                                
                                // Set shop message
                                $this->session->set_userdata('message', 'error{d}Šajā serverī jau ir šads lietotājs. Lūdzu ievadi pareizu šī lietotāja paroli.');
                                
                                // Redirect
                                redirect($this->module->active_module.'/index/'.$this->module->active_service);
                                
                            }
                        }
                        
                        // Player not found. Lets add it.
                        else
                        {
                            
                            // Add admin
                            $player_id = $this->amx_model->add_admin($username, $password, $amx_flags, $server_id);
                            
                            // Add service
                            $this->amx_model->add_service_data($player_id, $this->module->active_module, $this->module->active_service, $goods_amount, $server_id);
                            
                            // Do reload admins
                            // amx reload admins ...
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}'.$username.' veiksmīgi iegādājās "'.ucfirst($this->module->active_service).'" pakalpojumu uz '.$goods_amount.' dienām!');
                            
                        }
                    }
                    
                    // Reload privelegies
                    if($this->config->item('reload_privelegies') && $this->settings[$this->module->active_service]['type'] == 'subscription')
                    {
                        $server = $this->amx_model->get_server($server_id);
                        $this->amx_lib->reloadadmins($server->address, $server->rcon);
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
     * Custom amx functions
     * Custom amx AJAX handler
     * @return string TRUE / FALSE ( not boolean ! )
     */
    public function amx_ajax_call()
    {
        if( ! $this->input->is_ajax_request()) show_error('Direct access denied.', 500);
        
        $action = $this->uri->segment(3);
        
        if(strlen($user_input = $this->input->post('user_input')) < 2) exit();
        
        if($action == 'check_user')
        {
            $response = $this->{'_amx_'.$action}($user_input, $this->input->post('server_id'), $this->input->post('password'));
        }
        else
        {
            $response = $this->{'_amx_'.$action}($user_input);
        }
        
        echo ($response == TRUE) ? 'TRUE' : 'FALSE';
    }
    
    
    /**
     * 
     * @param type $ipaddress
     * @return boolean
     */
    public function _amx_check_banned_ip($ipaddress)
    {
        if($this->amx_model->check_ban($ipaddress) == FALSE)
        {
            $this->form_validation->set_message('_amx_check_banned_ip', 'Šādai ip adresei bans mūsu serveros nav atrasts!');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    public function _amx_check_user($username, $server_id, $password)
    {
        $player_data = $this->amx_model->get_player($username, $server_id);
        $password = ($this->config->item('login_auth_type') == 'md5') ? md5($this->input->post('password')) : $this->input->post('password');
        
        if( ! empty($player_data))
        {
            // User is found and password matches
            if($player_data->password == $password)
            {
                return TRUE;
            }
            
            // User is found and password is wrong
            else
            {
                return FALSE;
            }
        }
        
        // User not found so we dont care about it
        else
        {
            return TRUE;
        }
    }
    
    
}