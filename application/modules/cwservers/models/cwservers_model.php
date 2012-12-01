<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Cwservers_model extends CI_Model {

    
    /**
     * Main variables
     */
    public $table_structure = array();
    
    public $sql_services_table = NULL;
    
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Loading database from config
        $this->load->database($this->config->item($this->module->active_module), FALSE, TRUE);
        
        // Load table structure
        $this->table_structure = $this->config->item($this->module->active_module.'_table_structure');
        
        // Private actions initialization
        $this->individual_init();
    }
    
    
    public function individual_init()
    {
        // Get name of services table
        $this->sql_services_table = $this->config->item('cwservers_sql_table');
    }    
    
    
    private function get_expired_servers($table_name)
    {
        $current_time = time();
        
        $q = $this->db->where('expires <', $current_time)
                      ->where('expires !=', 0)
                      ->get($table_name);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function clear_expired($table_name)
    {
        // Get all expired serverss
        $expired_servers = $this->get_expired_servers($table_name);
        
        // If there is at least one expired server
        if(count($expired_servers) > 0)
        {
            // Lets work with each expired server
            foreach($expired_servers as $server)
            {
                // Load SSH lib
                $settings = $this->module->services[$server->module]['ssh'];
                
                // Load ssh lib
                $this->load->library('core/ssh', $settings);
                
                // Do SSH command
                $this->ssh->execute('screen -X -S '.$server->module.'-'.$server->port.' kill');
                
                // Set array with empty values
                $data = array(
                    'name' => '',
                    'server_rcon' => '',
                    'server_password' => '',
                    'expires' => 0,
                );
                // Update server data
                $this->occupy_server($server->id, $server->module, $data);
            }
        }
    }
    
    
    public function get_available_servers($module)
    {
        $q = $this->db->where('expires', 0)
                      ->where('module', $module)
                      ->get($this->sql_services_table);

        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Function gets occupied servers by internal active module
     * @return type
     */
    public function get_occupied_servers()
    {
        $q = $this->db->where('expires >', 0)
                      ->where('module', $this->module->active_service)
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Function gets occupied servers by specified external service
     * @param type $service
     * @return type
     */
    public function get_occupied_servers_external($service = 'hlds')
    {
        $q = $this->db->where('expires >', 0)
                      ->where('module', $service)
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
            
            
    
    /**
     * Updates server row with new information. Function can be used also for clearing row.
     * @param type $server_id
     * @param type $module
     * @param type $data
     */
    public function occupy_server($server_id, $module, $data = array())
    {
        $this->db->where('id', $server_id)
                 ->where('module', $module)
                 ->update($this->sql_services_table, $data);
    }
    
    
    public function get_server($server_id)
    {
        $q = $this->db->where('id', $server_id)
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
}