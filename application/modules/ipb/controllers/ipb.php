<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Ipb extends MX_Controller {

    
    /**
     * General variables
     */
    public $priceplan_raw = array();
    
    public $priceplan = array();

    
    /**
     * IPB variables
     */
    public $ipb_mebmer_data = FALSE;
    
    public $ipb_member_id = FALSE;
    
    
    /**
     * Constructor
     */
    public function __construct()        
    {
        parent::__construct();
        
        // Init library
        $this->load->library('core/module');
        
        // Db functions
        $this->load->library('ipb/ipb_db');
        
        // Module specific variables and functions initialization and execution
        $this->module->module_init();
        
        // Init db models
        $this->module->db_init();

        // Private actions initialization
        if($this->uri->segment(4) != 'error')
        {
            $this->load->library('ipb/ipb_lib');
            
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
        // Load CI helpers and libraries
        $this->load->helper('cookie');        
        
        // Load our special helper if IPB detected version is 3.x
        if($this->ipb_model->settings['ipb_version'] == 3)
        {
            $this->load->helper('ipb');            
        }
        
        // Check login
        $this->ipb_lib->check_login();
        
        // Set IPB username ID
        $this->ipb_member_id = $this->input->cookie('member_id');
        
        // Set IPB member data
        $this->ipb_member_data = $this->ipb_model->get_data_by_id($this->ipb_member_id); 
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
        
        // If there is info field, let's fill it with needed value
        if($this->uri->segment(4) != 'error')
        {
            if(isset($data['fields']['username_info']))
            {
                $data['fields']['username_info']['value'] = $this->ipb_member_data->name;
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
                
                    // Unsuspend
                    if($this->module->active_service == 'unsuspend')
                    {
                        // Un suspend
                        $this->ipb_model->unsuspend_member($this->ipb_member_id);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Suspended statuss tika veiksmīgi noņemts!');
                    }
                    
                    // Title
                    elseif($this->module->active_service == 'title')
                    {
                        // Set new title
                        $this->ipb_model->update_member_title($this->ipb_member_id, $this->input->post('title'));
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tavs "title" tika veiksmīgi nomainīts!');
                    }
                    
                    // Displayname
                    elseif($this->module->active_service == 'displayname')
                    {
                        // Change display name
                        $this->ipb_model->change_displayname($this->ipb_member_id, $this->input->post('displayname'));
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tavs "Display name" tika veiksmīgi nomainīts!');
                    }
                    
                    // Ipoints
                    elseif($this->module->active_service == 'ipoints')
                    {
                        // Set ipoints
                        $this->ipb_model->add_member_ipoints($this->ipb_member_id, $goods_amount);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tev tika piešķirti '.$goods_amount.' ipoints!');
                    }
                    
                    // Unwarn
                    elseif($this->module->active_service == 'unwarn')
                    {
                        // Get current level
                        $current_level = ($this->ipb_member_data->warn_level - $goods_amount);

                        // No levels under zero
                        $end_level = ($current_level < 0) ? 0 : $current_level;
                        
                        // Set new warn level
                        $this->ipb_model->update_member_warnlevel($this->ipb_member_id, $end_level);
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tavs Warn līmenis tika samazināts līdz '.$end_level.' procentu līmenim');
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
        if($this->input->post('ipb_username'))
        {
            $this->form_validation->set_rules('ipb_username', '', 'trim|required|min_length[2]|max_length[50]|xss_clean');
            $this->form_validation->set_rules('ipb_password', '', 'trim|required|min_length[2]|max_length[50]|xss_clean');
            
            if($this->form_validation->run() == FALSE)
            {
                // do something
            }    
            else
            {
                if($this->ipb_model->settings['ipb_version'] == 3)
                {
                    $response = do_ipb_login($this->input->post('ipb_username'), $this->input->post('ipb_password'));

                    list($action, $msg) = explode(':', $response);
                    $action = trim(strtolower($action));
                    $msg = trim($msg);
                }
                
                elseif($this->ipb_model->settings['ipb_version'] == 2)
                {
                    $password = md5($this->input->post('ipb_password'));
                    $member = $this->ipb_model->get_data_by_login($this->input->post('ipb_username'));

                    if( ! $member->id)
                    {
                        $action = 'error';
                        $msg = 'no_user';
                    }
                    else
                    {
                        $converge = $this->ipb_model->get_member_converge_data($member->email);
                        
                        if($this->ipb_lib->ipbv2_authenticate_member($converge->converge_pass_hash, $converge->converge_pass_salt, $password) != TRUE)
                        {
                            $action = 'error';
                            $msg = 'wrong_auth';
                        }
                        else
                        {
                            // Create session, cookies and other stuff, then do redirect
                            $this->ipb_lib->ipbv2_stronghold_set_cookie($member->id, $member->member_login_key);
                            $action = 'success';
                            $msg = '';
                        }
                    }
                }
                
                
                // Check IPB output
                if($action == 'success')
                {
                    //
                }
                elseif($action == 'error')
                {
                    if($msg == 'wrong_auth') $output = 'Norādīts nepareizs IPB lietotājvārds vai parole.';
                    if($msg == 'no_user') $output = 'Norādīts neeksistējošs IPB lietotājvārds.';
                    elseif($msg == 'bruteforce_account_unlock') $output = 'Bruteforce uzbrukuma mēģinājums. Lietotājs laicīgi nobloķēts';
                    
                    $this->session->set_userdata('message', 'error{d}'.$output);
                    
                }
                
                redirect('ipb/login');
            }
        }
        
        
        /**
         * Load HTML
         */
        $this->base->load_header($this->module->active_module);
        $this->load->view($this->module->active_module.'_login_tpl');
        $this->base->load_footer();        
    }
    
    
    public function logout()
    {
        if($this->ipb_model->settings['ipb_version'] == 3)
        {
            do_ipb_logout();
        }
        elseif($this->ipb_model->settings['ipb_version'] == 2)
        {
            $this->ipb_lib->ipbv2_logout();
        }
        
        redirect('ipb/login');
    }
    
    
    /**
     * Custom ipb functions
     * Custom ipb AJAX handler
     * @return string TRUE / FALSE ( not boolean ! )
     */
    public function ipb_ajax_call()
    {
        if( ! $this->input->is_ajax_request()) show_error('Direct access denied.', 500);
        
        $action = $this->uri->segment(3);
        
        if(strlen($user_input = $this->input->post('user_input')) < 2) exit();

        $response = $this->{'_ipb_'.$action}($user_input);
        
        echo ($response == TRUE) ? 'TRUE' : 'FALSE';
    }
    
    
    public function _ipb_check_suspended($username)
    {
        if($this->ipb_model->check_suspended($username) == FALSE)
        {
            $this->form_validation->set_message('_ipb_check_suspended', 'Šim lietotājam Suspended status netika atrasts.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    public function _ipb_check_displayname($username)
    {
        if($this->ipb_model->check_displayname($username) == TRUE)
        {
            $this->form_validation->set_message('_ipb_check_displayname', 'Šāds display name jau eksistē! Lūdzu izvēlies citu.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    public function _ipb_check_valid_displayname($username)
    {
        $chars = ';,|[]';
        
        if(strpbrk($username, $chars))
        {
            $this->form_validation->set_message('_ipb_check_valid_displayname', 'Display name nevar saturēt: '.$chars);
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
}