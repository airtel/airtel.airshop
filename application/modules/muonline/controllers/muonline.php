<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Muonline extends MX_Controller {
    
    
    /**
     * General variables
     */
    public $priceplan_raw = array();
    
    public $priceplan = array();
    
    
    /**
     * MU variables
     */
    public $mu_player_data = array();
    
    public $mu_username = NULL;
    
    
    
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

        //
        $this->individual_init();
        
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
        // Load CI helpers and libraries
        $this->load->helper('cookie');
        
        // Load muonline library
        $this->load->library('muonline/muonline_lib');
        
        // For debugging
        //setcookie('WebShopUsername', 'test');
        
        // Check login
        $this->muonline_lib->check_login(FALSE);
        
        // Set MU username
        $this->mu_username = $this->muonline_lib->get_username();
    
        // Set MU player data
        $this->mu_player_data = array();        
        
        // Private actions initialization
        if($this->uri->segment(4) != 'error')
        {
            // Services table checking and setup
            $this->core_model->check_table($this->muonline_model->sql_services_table, $this->muonline_model->table_structure, TRUE);

            // Clear expired players data
            $this->muonline_model->clear_expired($this->muonline_model->sql_services_table);
        }
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
        
        // Username
        $data['username'] = $this->mu_username;
        
        // Set info field's value
        if(isset($data['fields']['username_info']))
        {
            $data['fields']['username_info']['value'] = $this->mu_username;
        }        
        
        // Specific actions only for Skillbooster and Gmaccess services
        if($this->module->active_service == 'skillbooster' OR $this->module->active_service == 'gmaccess')
        {
            // Get user characters
            $characters = $this->muonline_model->get_characters($this->mu_username);
            
            // Prepare characters array
            foreach($characters as $char)
            {
                // Fill in
                $data['fields']['character']['data'][$char->Name] = $char->Name;
            }
            
            // Specific only for skillbooster
            if($this->module->active_service == 'skillbooster')
            {
                $enabled_skills = $this->module->services['skillbooster']['active_skills'];
                
                // Prepare skills array
                foreach($enabled_skills as $skill)
                {
                    // Fill in
                    $data['fields']['skills']['data'][$skill] = $skill;
                }
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
                        
                        // Do unban
                        $this->muonline_model->del_ban($this->input->post('username'));
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Esi veiksmīgi dzēsts no banlist datubāzes');
                        
                    }
                    
                    // Credits
                    elseif($this->module->active_service == 'credits')
                    {
                        
                        // Add credits
                        $this->muonline_model->add_credits($this->input->post('username'), $goods_amount);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tev tika piešķirti ' . $goods_amount . ' kredīti');
                        
                    }
                    
                    // Cspoints
                    elseif($this->module->active_service == 'cspoints')
                    {
                        
                        // Add cspoints
                        $this->muonline_model->add_cspoints($this->input->post('username'), $goods_amount);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tev tika piešķirti ' . $goods_amount . ' CS pointi');
                        
                    }
                    
                    // Skillbooster
                    elseif($this->module->active_service == 'skillbooster')
                    {

                        // Get selected skill
                        $skill = $this->input->post('skills');

                        // Get character
                        $character = $this->input->post('character');
                        
                        // Boost skill
                        $this->muonline_model->boost_skill($this->mu_username, $character, $skill, $goods_amount);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}' . $skill.' tika palielināts uz ' . $goods_amount . ' vienībām');
                        
                    }
                    
                    // Gmaccess
                    elseif($this->module->active_service == 'gmaccess')
                    {
                        
                        // Check if user already has access
                        $user = $this->muonline_model->get_user_service_data($this->mu_username, $this->module->active_module, $this->module->active_service, $this->input->post('character'));
                        
                        // Update gmaccess service expire time
                        if($user !== FALSE)
                        {
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}GM access statuss tavam akauntam tika pagarināts par ' . $goods_amount . ' dienām!');
                        }  
                        
                        // Add new gm access
                        else
                        {
                            $this->muonline_model->add_gmaccess($this->mu_username, $this->input->post('character'));
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Tavam akauntam tika piešķirts GM access uz ' . $goods_amount . ' dienām!');
                        }

                        // Insert / update data into services table
                        $this->muonline_model->add_service_data($this->mu_username, $this->module->active_module, $this->module->active_service, $goods_amount, $this->input->post('character'));
                        
                    }
                    
                    // Vipserver
                    elseif($this->module->active_service == 'vipserver')
                    {
                        
                        // Check if user already has access
                        $user = $this->muonline_model->get_user_service_data($this->mu_username, $this->module->active_module, $this->module->active_service, NULL);
                        
                        // Update vipserver service expire time
                        if($user !== FALSE)
                        {
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Tava pieeja VIP serverim tika pagarināta par ' . $goods_amount . ' dienām!');
                            
                            // Expiration
                            $expiration = strtotime('+'.$goods_amount.' days', $user->expires);
                        }
                        
                        // Add new vipserver access
                        else
                        {
                            // Expiration
                            $expiration = strtotime('+'.$goods_amount.' days', time());

                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Tu tiki pievienots VIP servera reģistrēto lietotāju sarakstam uz ' . $goods_amount . ' dienām!');
                        }
                        
                        // Lets double check vipserver file and correct it if needed.
                        if( ! $exists = $this->muonline_lib->search_vip_member($this->mu_username))
                        {
                            // Add access to VIP server
                            $this->muonline_lib->add_vip_member($this->mu_username, $expiration);
                        }
                        else
                        {
                            // Update access to VIP server
                            $this->muonline_lib->update_vip_member($this->mu_username, $expiration);
                        }
                        
                        // Insert / update data into services table
                        $this->muonline_model->add_service_data($this->mu_username, $this->module->active_module, $this->module->active_service, $goods_amount, NULL);                        
                        
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
    
    
    public function login()
    {
        /**
         * Load HTML
         */
        $this->base->load_header($this->module->active_module);
        $this->load->view($this->module->active_module.'_login_tpl');
        $this->base->load_footer();        
    }
    
    
    
    /**
     * Custom muonline functions
     * Custom muonline AJAX handler
     * @return type
     */
    public function muonline_ajax_call()
    {
        if( ! $this->input->is_ajax_request()) show_error('Direct access denied.', 500);
        
        $action = $this->uri->segment(3);
        
        if(strlen($user_input = $this->input->post('user_input')) < 2) exit();

        $response = $this->{'_muonline_'.$action}($user_input);

        echo ($response == TRUE) ? 'TRUE' : 'FALSE';
    }
    
    
    /**
     * Custom muonline functions
     * Check if entered user exists
     * @param type $username
     */
    public function _muonline_check_valid_user($username)
    {
        $error_message = 'Šāds lietotājs nav atrasts!';
        
        // Do sql check
        $auth_params = $this->muonline_model->auth_params;
            
        if($this->core_model->search_valid_user($username, $auth_params['table_name'], $auth_params['username_field']) == FALSE)
        {
            $this->form_validation->set_message('_muonline_check_valid_user', $error_message);
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    /**
     * Custom muonline functions
     * Check if entered user is banned
     * @param type $username
     */
    public function _muonline_check_name_ban($username)
    {
        $error_message = 'Šādam lietotājam bans mūsu serverī nav atrasts!';
        
        // Do sql check
        if($this->muonline_model->check_ban($username) == FALSE)
        {
            $this->form_validation->set_message('_muonline_check_name_ban', $error_message);
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
}