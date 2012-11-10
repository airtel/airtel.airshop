<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class General extends MX_Controller {

    
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
        
        // Init db models
        $this->module->db_init();
        
        // Module specific variables and functions initialization and execution
        $this->module->module_init();

        // Private actions initialization
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
        // Load module library
        $this->load->library('general/general_lib');
        
        // Services table checking and setup
        $this->core_model->check_table($this->general_model->donate_table, $this->general_model->table_structure, TRUE);        
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
                    $username = $this->input->post('username');
                    $message = $this->input->post('message');
                    
                    
                    // Donor not found. let's add new one
                    if( ! $user = $this->general_model->get_donor($username))
                    {
                        
                        // Add donor
                        $this->general_model->add_donor($username, $message, $pay_method, $goods_amount);

                    }
                    
                    // Donor is found
                    else
                    {
                        
                        // Update donor
                        $this->general_model->update_donor($username, $message, $pay_method, $goods_amount);
                        
                    }
                    
                    // Set shop message
                    $this->session->set_userdata('message', 'info{d}Paldies ka atbalstīji mūs!');
                    
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
    
    
}