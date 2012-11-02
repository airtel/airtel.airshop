<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Minecraft extends MX_Controller {

    
    /**
     * General variables
     */
    public $priceplan_raw = array();
    
    public $priceplan = array();
    
    public $priceplan_groups = array();
    
    
    /**
     * Minecraft specific variables
     */
    public $commands = array();
    
    public $server_settings = array();
    
    
    /**
     * Constructor
     */
    public function __construct()        
    {
        parent::__construct();

        // Base modules
        $this->load->module('base');
        $this->load->module('smscode');
        $this->load->module('ibankcode');
        $this->load->module('paypalcode');
        
        // Init library
        $this->load->library('core/module');
        
        // Core libraries
        $this->load->library('core/error_handler');
        $this->load->library('core/ui');
        $this->load->library('core/system');
        
        // Module sql model loading
        $this->load->model($this->module->active_module.'/'.$this->module->active_module.'_model');
        
        // Core model loading
        $this->load->model('core/core_model');
        
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
        // Load minecraft library
        $this->load->library('minecraft/minecraft_lib');
        
        // Load connection settings from config
        $this->server_settings = $this->config->item('minecraft_server_settings');
        
        // Load minecraft rcon library
        $this->load->library('core/rcon_minecraft', $this->server_settings);
        
        // Services table checking and setup
        $this->core_model->check_table($this->minecraft_model->sql_services_table, $this->minecraft_model->table_structure);
        
        // Clear expired players data
        $this->minecraft_model->clear_expired($this->minecraft_model->sql_services_table);
        
        // Load plugin commands
        $this->commands = $this->config->item('plugin_commands');
    }
    
    
    private function priceplan_init()
    {
        $this->priceplan_raw = $this->system->build_priceplan_raw();
        
        
        if($this->module->active_service == 'groups')
        {
            // Select first group from array and set as active
            $group = array_shift(array_keys($this->priceplan_raw['sms']['groups']));
            
            // Prepare priceplan for all available pay methods
            $this->priceplan = $this->system->build_priceplan($group);
            
            // Prepare groups priceplan
            $this->priceplan_groups = $this->system->build_priceplan_groups();
        }
        else
        {
            // Prepare priceplan for all available pay methods
            $this->priceplan = $this->system->build_priceplan();
        }
    }  
    
    
    public function index()
    {
        // Load prices
        $this->priceplan_init();
        
        // Get fields for module active service
        $data['fields'] = $this->config->item('fields_'.$this->module->active_service);
        
        
        // Specific actions only for groups service
        if($this->module->active_service == 'groups')
        {
            // Prepare groups array
            $data['fields']['group']['data'] = $this->minecraft_lib->prepare_groups($this->priceplan_raw['sms']['groups']);
        }

        
        /**
         * Lets begin work with inputs here:
         */
        if($this->input->post('submit'))
        {
            if($this->input->post('smscode') && $this->input->post('smscode') != '99999999')
            {
                $pay_method = 'sms';
            }
            elseif($this->input->post('ibankcode') && $this->input->post('ibankcode') != '99999999')
            {
                $pay_method = 'ibank';
            }
            elseif($this->input->post('paypalcode') && $this->input->post('paypalcode') != '99999999')
            {
                $pay_method = 'paypal';
            }
            
            
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
                
                
                // Fix for groups
                $prices_temp = ($this->module->active_service == 'groups') ? $this->priceplan_groups[$pay_method][$this->input->post('group')] : $this->priceplan[$pay_method];
                
                
                // Check airtel api response
                if($response['price'] >= $post_price && array_key_exists($response['price'], $prices_temp))
                {
                    
                    // Get goods amount ( credits, power, days... )
                    if($this->module->active_service == 'groups')
                    {
                        $goods_amount = $this->priceplan_raw[$pay_method][$this->module->active_service][$this->input->post('group')][$response['price']];
                    }
                    else
                    {
                        $goods_amount = $this->priceplan_raw[$pay_method][$this->module->active_service][$response['price']];
                    }
                    
                    
                    // Unban
                    if($this->module->active_service == 'unban')
                    {
                        
                        // Do unban
                        $this->rcon_minecraft->communicate('unban ' . $this->input->post('username'));
                        
                        // Send notification
                        if($this->server_settings['notifications'] == TRUE)
                        {
                            $this->rcon_minecraft->communicate('say User ' . $this->input->post('username') . ' unbanned himself using Shop services.');
                        }
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Esi veiksmīgi dzēsts no banlist datubāzes');
                        
                    }
                    
                    // Credits
                    elseif($this->module->active_service == 'credits')
                    {
                        
                        // Get active plugin
                        $plugin = $this->module->services['credits']['plugin'];
                        
                        // Get plugin command
                        $command = $this->commands['credits'][$plugin]['command_give'];
                        
                        // Add credits
                        $this->rcon_minecraft->communicate($command . ' ' . $this->input->post('username') . ' ' . $goods_amount);
                        
                        // Send notification
                        if($this->server_settings['notifications'] == TRUE)
                        {
                            $this->rcon_minecraft->communicate('say User ' . $this->input->post('nickname') . ' bought '.$goods_amount.' credits using Shop services.');
                        }
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tev tika piešķirti ' . $goods_amount . ' kredīti');
                        
                    }
                    
                    // Amnesty
                    elseif($this->module->active_service == 'amnesty')
                    {
                        
                        // Get active plugin
                        $plugin = $this->module->services['amnesty']['plugin'];
                        
                        // Get plugin command
                        $command = $this->commands['amnesty'][$plugin]['command_unjail'];
                        
                        // Remove from jail
                        $this->rcon_minecraft->communicate($command . ' ' . $this->input->post('username'));
                        
                        // Send notification
                        if($this->server_settings['notifications'] == TRUE)
                        {
                            $this->rcon_minecraft->communicate('say User ' . $this->input->post('nickname') . ' released from jail using Shop services');
                        }
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Esi veiksmīgi atbrīvots no ieslodzījuma!');
                        
                    }
                    
                    // Peaceful
                    elseif($this->module->active_service == 'peaceful')
                    {
                        
                        // Get faction data
                        $faction = $this->minecraft_model->get_user_service_data($this->input->post('faction'), $this->module->active_module, $this->module->active_service);
                        
                        // Update faction peaceful service expire time
                        if($faction !== FALSE)
                        {
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Peaceful statuss tavai frakcijai ir pagarināts par ' . $goods_amount . ' dienām!');
                        }    
                        
                        // Add new peaceful faction
                        else
                        {
                            // Get active plugin
                            $plugin = $this->module->services['peaceful']['plugin'];

                            // Get plugin command
                            $command = $this->commands['peaceful'][$plugin]['command_set_pf'];
                            
                            // Set peaceful status
                            $this->rcon_minecraft->communicate($command . ' ' . $this->input->post('faction'));
                            
                            if($this->server_settings['notifications'] == TRUE)
                            {
                                $this->rcon_minecraft->communicate('say Faction ' . $this->input->post('faction') . ' gained peaceful status using Shop services');
                            }
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Tava frakcija ieguva peaceful statusu uz ' . $goods_amount . ' dienām!');
                        }
                        
                        // Insert / update data into services table
                        $this->minecraft_model->add_service_data($this->input->post('faction'), $this->module->active_module, $this->module->active_service, $goods_amount);
                        
                    }
                    
                    // Power
                    elseif($this->module->active_service == 'power')
                    {
                        
                        // Get active plugin
                        $plugin = $this->module->services['power']['plugin'];
                        
                        // Get plugin command
                        $command = $this->commands['power'][$plugin]['command_add_power'];
                        
                        // Add power
                        $this->rcon_minecraft->communicate($command . ' ' . $this->input->post('username') . ' ' . $goods_amount);
                        
                        // Send notification
                        if($this->server_settings['notifications'] == TRUE)
                        {
                            $this->rcon_minecraft->communicate('say Faction ' . $this->input->post('username') . ' gained power amount of ' . $goods_amount . ' using Shop services');
                        }
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tavai frakcijai tika piešķirts papildus Power. Power daudzums: ' . $goods_amount);
                        
                    }
                    
                    // Groups
                    elseif($this->module->active_service == 'groups')
                    {
                        
                        $group = $this->input->post('group');
                        $username = $this->input->post('username');
                        
                        // Get userdata
                        $user = $this->minecraft_model->get_user_service_data($username, $this->module->active_module, $this->module->active_service, $group);
                        
                        // Update user group service expire time
                        if($user !== FALSE)
                        {
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Tavas priviēģiju grupas "' . $group . '" termiņš tika pagarināts uz ' . $goods_amount . ' dienām!');
                        }
                        
                        // Add new service
                        else
                        {
                            // Get active world
                            $world = $this->module->services['groups']['world'];
                            
                            // Get active plugin
                            $plugin = $this->module->services['groups']['plugin'];

                            // Get plugin command
                            $command = $this->commands['groups'][$plugin]['command_set_group'];
                            
                            // Insert values
                            $command_string = $this->minecraft_lib->pex_str_replace($username, $group, $world, $command);
                            
                            // Set new group
                            $this->rcon_minecraft->communicate($command_string);
                            
                            // Send notification
                            if($this->server_settings['notifications'] == TRUE)
                            {
                                $this->rcon_minecraft->communicate('say User ' . $username . ' upgraded his acount to ' . $group . ' using Shop services');
                            }
                            
                            // Set shop message
                            $this->session->set_userdata('message', 'info{d}Esi veiksmīgi ieguvis privilēģiju grupu "' . $group . '" uz ' . $goods_amount . ' dienām!');
                        }
                        
                        // Insert / update data into services table
                        $this->minecraft_model->add_service_data($username, $this->module->active_module, $this->module->active_service, $goods_amount, $group);
                        
                    }
                    
                    // Exp
                    elseif($this->module->active_service == 'exp')
                    {
                        // Give EXP
                        $this->rcon_minecraft->communicate('exp give ' . $this->input->post('username') . ' ' . $goods_amount);
                        
                        // Send notification
                        if($this->server_settings['notifications'] == TRUE)
                        {
                            $this->rcon_minecraft->communicate('say User ' . $this->input->post('username') . ' bought some experience using Shop services');
                        }
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Esi veiksmīgi papildinājis savu EXP dadzumu par '. $goods_amount . ' punktiem');
                    }
                    
                    // Broadcast
                    elseif($this->module->active_service == 'broadcast')
                    {
                        // Send message
                        $this->rcon_minecraft->communicate('broadcast '.$this->input->post('message'));
                        
                        // Set shop message
                        $this->session->set_userdata('message', 'info{d}Tava ziņa ir nosūtīta!');
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
     * Custom minecraft functions
     * Custom minecraft AJAX handler
     * @return type
     */
    public function minecraft_ajax_call()
    {
        if( ! $this->input->is_ajax_request()) show_error('Direct access denied.', 500);
        
        $action = $this->uri->segment(3);
        
        if(strlen($user_input = $this->input->post('user_input')) < 2) exit();

        $response = $this->{'_minecraft_'.$action}($user_input);
        
        echo ($response == TRUE) ? 'TRUE' : 'FALSE';
    }
    
    
    /**
     * Custom minecraft functions
     * Check if entered user exists
     * @param type $username
     */
    public function _minecraft_check_valid_user($username)
    {
        $error_message = 'Šāds lietotājs nav atrasts!';
        $result = TRUE;
        
        // Do sql check
        if($this->module->services['credits']['functionality'] == 'both')
        {
            $auth_params = $this->minecraft_model->auth_params;
            
            if($this->core_model->search_valid_user($username, $auth_params['table_name'], $auth_params['username_field']) == FALSE)
            {
                $result = FALSE;
            }
        }
        
        // Do rcon check
        elseif($this->module->services['credits']['functionality'] == 'rcon')
        {
            $response = $this->rcon_minecraft->communicate('seen '.$username, TRUE);
            if(stristr($response, 'player not found') !== FALSE)
            {
                $result = FALSE;
            }
        }
        
        // Set validation error if result is FALSE
        if($result == FALSE)
        {
            $this->form_validation->set_message('_minecraft_check_valid_user', $error_message);
        }
        
        return $result;
    }
    
    
    /**
     * Custom minecraft functions
     * Check if ban by name exists
     * @param type $username
     */
    public function _minecraft_check_name_ban($username)
    {
        $error_message = 'Šādam lietotājam bans mūsu serverī nav atrasts';
        $result = TRUE;
        
        // Do sql check
        if($this->module->services['unban']['functionality'] == 'both')
        {
            if( ! $this->minecraft_model->search_name_ban($username))
            {
                $result = FALSE;
            }    
        }
        
        // Do rcon check
        elseif($this->module->services['unban']['functionality'] == 'rcon')
        {
            $response = $this->rcon_minecraft->communicate('checkban '.$username, TRUE);
            if(stristr($response, 'is not banned') !== FALSE)
            {
                $result = FALSE;
            }
        }
        
        // Set validation error if result is FALSE
        if($result == FALSE)
        {
            $this->form_validation->set_message('_minecraft_check_name_ban', $error_message);
        }
            
        return $result;
    }
    
    
    /**
     * Custom minecraft functions
     * Check if prisoner exists
     * @param type $username
     */
    public function _minecraft_check_prisoner($username)
    {
        $error_message = 'Šāds lietotājs cietumā neuzturas!';
        $result = TRUE;
        
        // Do sql check
        if($this->module->services['amnesty']['functionality'] == 'both')
        {
            if($this->mc_model->search_prisoner($username) == FALSE)
            {
                
                $result = FALSE;
            }
        }
        
        // Do rcon check
        elseif($this->module->services['amnesty']['functionality'] == 'rcon')
        {
            $response = $this->rcon_minecraft->communicate('jailcheck '.$username, TRUE);
            if(stristr($response, 'is not jailed') !== FALSE)
            {
                $result = FALSE;
            }
        }
        
        // Set validation error if result is FALSE
        if($result == FALSE)
        {
            $this->form_validation->set_message('_minecraft_check_prisoner', $error_message);
        }
            
        return $result;
    }
    
    
    /**
     * Custom minecraft functions
     * Check if faction exists
     * @param type $username
     */
    public function _minecraft_check_faction($faction)
    {
        $response = $this->rcon_minecraft->communicate('f list', FALSE);
        $factions = explode("\n", $response);

        foreach($factions as $f)
        {
             if(stristr($f, $faction) !== FALSE)
             {
                 return TRUE;
             }
        }

        $this->form_validation->set_message('_minecraft_check_faction', 'Neesam atraduši frakciju ar šādu nosaukumu');
        return FALSE;
    }
    
    
    public function _minecraft_check_online($username)
    {
        // Load minecraft query library
        $this->load->library('core/query_minecraft', $this->server_settings);
        
        // Query server and get player list
        $players = $this->query_minecraft->get_players();
        
        // Find player in list
        if( ! empty($players) && count($players) > 0)
        {
            foreach($players as $player)
            {
                 if(stristr($player, $username) !== FALSE)
                 {
                     return TRUE;
                 }
            }
        }
        
        $this->form_validation->set_message('_minecraft_check_online', 'Tev jaatrodas spēlē lai pasūtītu šo pakalpojumu!');
        return FALSE;
    }
    
    
}