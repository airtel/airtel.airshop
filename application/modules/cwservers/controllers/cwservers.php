<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Cwservers extends MX_Controller {

    
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
        // Load CW lib
        $this->load->library('cwservers/cw');

        // Do server ping check
        if($this->uri->segment(2) != 'check_status')
            $this->cw->ping_host($this->module->services[$this->module->active_service]['ssh'], TRUE);

        // Services table checking and setup
        $this->core_model->check_table($this->cwservers_model->sql_services_table, $this->cwservers_model->table_structure, TRUE);
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
        
        if($this->uri->segment(4) != 'error')
        {
            // Prepare available servers array
            $servers = $this->cwservers_model->get_available_servers($this->module->active_service);
        
            if(count($servers) > 0)
            {
                foreach($this->cwservers_model->get_available_servers($this->module->active_service) as $server)
                {
                    $data['fields']['servers']['data'][$server->id] = $server->server_hostname . ':' . $server->port;
                }
            }
            else
            {
                $data['fields']['servers']['data'][''] = 'Nav brīvu serveru...';
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
                    
                    
                    // Load ssh lib
                    $this->load->library('core/ssh', $this->module->services[$this->module->active_service]['ssh']);
                    
                    
                    // Prepare server data
                    $insert = array(
                        'name' => $this->input->post('name'),
                        'server_rcon' => $this->input->post('server_rcon'),
                        'server_password' => $this->input->post('server_password'),
                        'expires' => strtotime('+'.$goods_amount.' days', time()),
                    );
                    // Insert into db
                    $this->cwservers_model->occupy_server($this->input->post('servers'), $this->module->active_service, $insert);
                    
                    // Get server
                    // @todo !!!
                    $server = $this->cwservers_model->get_server($this->input->post('servers'));

                    // Prepare start string
                    $start_string = $this->cw->prepare_start($this->module->services[$this->module->active_service]['options'], $server->ip, $server->port, $server->name, $server->server_rcon, $server->server_password);
                    
                    
                    // Do SSH commands
                    $this->ssh->execute($start_string);
                    
                    
                    // Hlds
                    if($this->module->active_service == 'hlds')
                    {
                        $this->session->set_userdata('message', 'info{d}Paldies. CS:1.6 Serveris ir pasūtīts uz '.$goods_amount.' dienām. Servera dati: '.$server->server_hostname.':'.$server->port.' Servera Rcon: '.$server->server_rcon.' Servera parole: '.$server->server_password);
                    }
                    
                    // Srcds
                    if($this->module->active_service == 'srcds')
                    {
                        $this->session->set_userdata('message', 'info{d}Paldies. CS:S Serveris ir pasūtīts uz '.$goods_amount.' dienām. Servera dati: '.$server->server_hostname.':'.$server->port.' Servera Rcon: '.$server->server_rcon.' Servera parole: '.$server->server_password);
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
    
    
    public function check_status()
    {
        if($this->uri->segment(3) == $this->config->item('passkey'))
        {
            
            // Clear expired players data
            $this->cwservers_model->clear_expired($this->cwservers_model->sql_services_table);
            
            // Check if all screens are running
            
            // With each service type
            foreach($this->module->services as $service => $items)
            {
                // Get service ssh settings
                $settings = $items['ssh'];
                
                // Load ssh lib for service
                $this->load->library('core/ssh', $settings);
                        
                // Wipe dead screens and get output of alive ones
                $output = $this->ssh->execute('screen -wipe && screen -ls', TRUE);
                       
                // Get occupied servers from db by each service
                $running_servers = $this->cwservers_model->get_occupied_servers_external($service);
                
                // If there is at least one occupied server
                if(count($running_servers) > 0)
                {
                    // With each running server do comparsion with ssh output
                    foreach($running_servers as $server)
                    {
                        // Prepare screen name
                        $screen_name = $server->module.'-'.$server->port;
                        
                        if($this->cw->search_in_output($screen_name, $output) === FALSE)
                        {
                            // Prepare start string
                            $start_string = $this->cw->prepare_start($items['options'], $server->ip, $server->port, $server->name, $server->server_rcon, $server->server_password, $screen_name);

                            // Do screen start
                            $this->ssh->execute($start_string);
                            
                            // Debug output message
                            $debug_message = 'Starting dead server. IP: '.$server->ip.' Port: '.$server->port.' Screen: '.$screen_name;
                            log_message('debug', $debug_message);
                            
                            echo $debug_message.'<br />';
                        }
                    }
                }
                
                //echo '<pre>';
                //print_r($output);
                //echo '</pre>';
            }
            
            
            
            
            // do foreach with each service ( hlds /srcrds )
            // get command screen -ls output
            // compare db records with screen 
            // if there is db record and no screen or dead screen running then kill the screen and
            // new one.
            
            
        }
        else
        {
            exit('Wrong passkey.');
        }
    }
    
    
    
    
}