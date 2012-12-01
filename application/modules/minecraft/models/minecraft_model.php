<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Minecraft_model extends CI_Model {
    
    
    /**
     * Main variables
     */
    public $table_structure = array();
    
    public $sql_services_table = NULL;
    
    
    /**
     * Minecraft variables
     */
    public $auth_params = array();
    
    public $unban_params = array();
    
    public $amnesty_params = array();
    
    public $credits_params = array();
    
    
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
    
    
    /**
     * Function fills Class vars
     * @param type $sql_params
     * @return \Minecraft_model
     */
    public function sql_init($sql_params = array())
    {
        foreach ($sql_params as $key => $val)
        {
            if (isset($this->$key))
            {
                $method = 'set_'.$key;
                if (method_exists($this, $method))
                {
                    $this->$method($val);
                }
                else
                {
                    $this->$key = $val;
                }
            }
        }
        return $this;
    }
    
    
    public function individual_init()
    {
        // Load sql settings for minecraft from config file
        $sql_params = $this->config->item('sql_params');
        
        // Get name of services table
        $this->sql_services_table = $this->config->item('sql_table');
        
        // Class sql params variables initialization
        $this->sql_init($sql_params);
    }

    
    /**
     * Clears every user with expired service time
     * @param string $table_name
     */
    public function clear_expired($table_name)
    {
        $expired_members = $this->core_model->get_expired_members($this->module->active_module, $table_name);
        
        // If there is at least one expired member
        if(count($expired_members) > 0)
        {
            // Lets work with each expired player
            foreach($expired_members as $member)
            {
                // Peaceful service
                if($member->service == 'peaceful')
                {
                    // Remove peaceful status
                    $this->rcon_minecraft->communicate('f peaceful '.$member->username);
                }
                
                // Groups service
                elseif($member->service == 'groups')
                {
                    // Get groups world
                    $world = $this->module->services['groups']['groups_world'];
                    
                    // Remove group
                    $this->rcon_minecraft->communicate('pex user ' . $member->username . ' group remove ' . $member->group . ' ' . $world);
                }
                
                // Delete from database
                $this->core_model->del_service_data($member->id, $table_name);
            }
        }
    }
    
    
    /**
     * Add or update user data in services table
     * @param type $username
     * @param type $module
     * @param type $service
     * @param type $days
     * @param type $group
     */
    public function add_service_data($username, $module, $service, $days, $group = NULL)
    {
        // Get user from services table
        $user = $this->get_user_service_data($username, $module, $service, $group);

        // User already has service
        if($user !== FALSE)
        {
            // Update time
            $time = strtotime('+'.$days.' days', $user->expires);
            
            $this->db->where('username', $username)
                     ->where('module', $module)
                     ->where('service', $service)
                     ->where('group', $group)
                     ->set('expires', $time)
                     ->update($this->sql_services_table);
        }
        // Add new service
        else
        {
            $time = strtotime('+'.$days.' days', time());
            
            $data = array(
                'username' => $username,
                'expires' => $time,
                'module' => $module,
                'service' => $service,
                'group' => $group,
            );
            
            $this->db->insert($this->sql_services_table, $data);
        }
    }
    
    
    /**
     * Gets user from services table
     * @param type $username
     * @param type $module
     * @param type $service
     * @param type $group
     * @return type
     */
    public function get_user_service_data($username, $module, $service, $group = NULL)
    {
        $q = $this->db->where('username', $username)
                      ->where('module', $module)
                      ->where('service', $service)
                      ->where('group', $group)
                      ->get($this->sql_services_table);
        
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
    }
    
    
    /**
     * Function checks if username is in jail
     * @param type $nickname
     * @return type
     */
    public function search_prisoner($username)
    {
        $q = $this->db->where($this->amnesty_params['username_field'], $username)
                      ->get($this->amnesty_params['table_name']);

        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }

    
    /**
     * Function checks if username is in banlist
     * @param type $nickname
     * @return type
     */
    public function search_name_ban($username)
    {
        $q = $this->db->where($this->unban_params['username_field'], $username)
                      ->get($this->unban_params['table_name']);
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    /**
     * Function deletes player from banlist
     * @param type $username
     */
    public function del_name_ban($username)
    {
        $this->db->where($$this->unban_params['username_field'], $username)
                 ->delete($this->unban_params['table_name']);
    }
    
    
    public function toplist_unban()
    {
        $q = $this->db->get($this->unban_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function toplist_credits()
    {
        $q = $this->db->get($this->credits_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function toplist_amnesty()
    {
        $q = $this->db->get($this->amnesty_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }   
    
    
    public function toplist_peaceful()
    {
        $q = $this->db->where('service', 'peaceful')
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
        
    
    public function toplist_groups()
    {
        $q = $this->db->where('service', 'groups')
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
}