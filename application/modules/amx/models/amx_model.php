<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Amx_model extends CI_Model {
    
    
    /**
     * Main variables
     */
    public $table_structure = array();
    
    public $sql_services_table = NULL;
    
    
    /**
     * AMX variables
     */
    public $amx_version = 6;
    
    
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
        $this->sql_services_table = $this->config->item('sql_table');
        
        // Set AMX bans version
        $this->amx_version = $this->get_version();
    }
    
    
    /**
     * Checks if bans_edit table exists
     * If table exists then amx version is 6
     * else amx version is 5
     * @return int
     */
    public function get_version()
    {
        return ($this->db->table_exists('bans_edit')) ? 6 : 5;
    }
    
    
    /**
     * 
     * @param type $table_name
     */
    public function clear_expired($table_name)
    {
        // Get all expired members
        $expired_members = $this->core_model->get_expired_members($this->module->active_module, $table_name);
        
        // Get all active members
        $active_services = $this->core_model->get_active_members($this->module->active_module, $table_name);
        
        // Define other services array
        $other_services = array();
        
        // If there is at least one expired member
        if(count($expired_members) > 0)
        {
            // Lets work with each expired player
            foreach($expired_members as $member)
            {
                // Lets check if our expired member has atleast one more non active subscription service
                if(count($active_services) > 0)
                {
                    // Search in each services and collect all active services in array
                    foreach($active_services as $key => $service)
                    {
                        // If service in loop meets our criterias then add it into other services array
                        if($member->username == $service->username && $member->server_id == $service->server_id)
                        {
                            $other_services[$key] = strlen($this->module->services[$service->service]['amx_flags']);
                        }
                    }
                }     
                
                // Let's work with our collected results
                // If we collected atleast one other player's service
                if(count($other_services) > 0)
                {
                    // If there is more than one service lets calculate highest service by flags
                    if(count($other_services) > 1)
                    {
                        $key = array_search(max($other_services), $other_services);
                        $exchange_service = $active_services[$key];
                    }
                    
                    // Only one active service
                    else
                    {
                        $key = array_shift(array_keys($other_services));
                        $exchange_service = $active_services[$key];
                    }
                    
                    // Change admin access flags
                    log_message('debug', 'Module: amx; Function: clear_expired; action: updated admin. Admin_id: '.$member->username);
                    $this->update_admin($member->username, $this->module->services[$exchange_service->service]['amx_flags']);
                    
                    // Unset other services array for this member
                    unset($other_services);
                }
                
                // Player does not have any other services. Just delete him from admins.
                else
                {
                    log_message('debug', 'Module: amx; Function: clear_expired; action: deleted admin. Admin_id: '.$member->username);
                    $this->del_admin($member->username);
                }
                
                // Delete player's expired service data from shop services table
                log_message('debug', 'Module: amx; Function: clear_expired; action: deleted shop services row. Id: '.$member->id);
                $this->core_model->del_service_data($member->id, $table_name);
            }
        }
    }
    
    
    /**
     * Deletes player's ip ban
     * @param string $ipaddress
     */
    public function del_ban($ipaddress)
    {
        $this->db->where('player_ip', $ipaddress)
                 ->delete('bans');
    }
    
    
    /**
     * Checks for player's ip ban
     * @param type $ipaddress
     * @return boolean
     */
    public function check_ban($ipaddress)
    {
        $q = $this->db->where('player_ip', $ipaddress)
                      ->get('bans');
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    /**
     * Gets server list from amx servers table
     * @param array $exclude list of ids that needs to be excluded
     * @return object
     */
    public function get_servers($exclude = array())
    {
        $this->db->select('id, hostname, rcon, address');
        
        if( ! empty($exclude))
        {
            $this->db->where_not_in('id', $exclude);
        }
        $q = $this->db->get('serverinfo');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @param type $server_id
     * @return type
     */
    public function get_server($server_id)
    {
        $q = $this->db->where('id', $server_id)
                      ->get('serverinfo');
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    /**
     * Adds new admin into amx bans table
     * @param type $username
     * @param type $password
     * @param type $amx_flags
     * @param type $server_id
     */
    public function add_admin($username, $password, $amx_flags, $server_id)
    {
        // Prepare data for amx_amxadmins
        $data = array(
            'username' => $username,
            'password' => $password,
            'access' => $amx_flags,
            'flags' => 'a',
            'nickname' => 'airtelshop',
        );
        
        // Additional params if amx version is 6
        if($this->amx_version == 6)
        {
            $data['steamid'] = $username;
            $data['ashow'] = 0;
            $data['created'] = time();
            $data['expired'] = 0;
            $data['days'] = 0;
        }
        
        // Insert data into table
        $this->db->insert('amxadmins', $data);
        
        // Unset data
        unset($data);
        
        // Get insert id
        $player_id = $this->db->insert_id();
        
        // Prepare data for amx_admins_servers
        $data = array(
            'admin_id' => $player_id,
            'server_id' => $server_id,
        );
        
        // Additional params if amx version is 6
        if($this->amx_version == 6) $data['use_static_bantime'] = 'no';
        
        // Insert data into table
        $this->db->insert('admins_servers', $data);
        
        // Return insert id
        return $player_id;
    }
    
    
    public function update_admin($player_id, $amx_flags)
    {
        $this->db->where('id', $player_id)
                 ->set('access', $amx_flags)
                 ->update('amxadmins');
    }

    
    /**
     * 
     * @param type $username
     * @param type $module
     * @param type $service
     * @param type $days
     * @param type $server_id
     */
    public function add_service_data($username, $module, $service, $days, $server_id)
    {
        // Get user from services table
        $user = $this->get_user_service_data($username, $module, $service, $server_id);
        
        // User already has service
        if($user !== FALSE)
        {
            // Update time
            $time = strtotime('+'.$days.' days', $user->expires);
            
            $this->db->where('username', $username)
                     ->where('module', $module)
                     ->where('service', $service)
                     ->where('server_id', $server_id)
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
                'server_id' => $server_id,
            );
            
            $this->db->insert($this->sql_services_table, $data);
        }
    }
    
    
    /**
     * Gets user from services table
     * @param type $username
     * @param type $module
     * @param type $service
     * @param type $server_id
     * @return type
     */
    public function get_user_service_data($username, $module, $service, $server_id)
    {
        $q = $this->db->where('username', $username)
                      ->where('module', $module)
                      ->where('service', $service)
                      ->where('server_id', $server_id)
                      ->get($this->sql_services_table);
        
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
    }

    
    /**
     * 
     * @param type $username
     * @param type $server_id
     * @return type
     */
    public function search_player($username, $server_id)
    {
        $q = $this->db->where('username', $username)
                      ->join('admins_servers', 'admins_servers.admin_id = amxadmins.id')
                      ->where('admins_servers.server_id', $server_id)
                      ->get('amxadmins');
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    /**
     * 
     * @param type $username
     * @return type
     * @TODO rewrite with active record
     */
    public function get_player($username, $server_id)
    {
        $sql = "SELECT amx_amxadmins.*, amx_admins_servers.*, amx_shop_services.* 
                FROM amx_amxadmins, amx_admins_servers, amx_shop_services 
                WHERE amx_amxadmins.id = amx_admins_servers.admin_id 
                AND amx_admins_servers.admin_id = amx_shop_services.username 
                AND amx_amxadmins.username = ? 
                AND amx_admins_servers.server_id = ?";
        
        $q = $this->db->query($sql, array($username, $server_id)); 
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    /**
     * Deletes admin from amx bans tables
     * @param type $player_id
     */
    public function del_admin($player_id)
    {
        $this->db->where('admin_id', $player_id)
                 ->delete('admins_servers');
        
        $this->db->where('id', $player_id)
                 ->delete('amxadmins');
    }
    
    
    /**
     * 
     * @return object
     */
    public function toplist_unban()
    {
        $q = $this->db->get('bans');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @return object
     */
    public function toplist_amx($service)
    {
        $q = $this->db->select('*')
                  ->from($this->sql_services_table)
                  ->join('amxadmins', 'amxadmins.id = ' . $this->sql_services_table . '.username')
                  ->where($this->sql_services_table . '.service', $service)
                  ->get();
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    

}