<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Sourcemod_model extends CI_Model {
    
    
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
        $this->sql_services_table = $this->config->item('sql_table');
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
                            $other_services[$key] = strlen($this->module->services[$service->service]['access_group']['flags']);
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
                    log_message('debug', 'Module: sourcemod; Function: clear_expired; action: updated admin. Admin_id: '.$member->username);
                    $this->update_admin($member->username, $exchange_service->service);
                    
                    // Unset other services array for this member
                    unset($other_services);
                }
                
                // Player does not have any other services. Just delete him from admins.
                else
                {
                    log_message('debug', 'Module: sourcemod; Function: clear_expired; action: deleted admin. Admin_id: '.$member->username);
                    $this->del_admin($member->username);
                }
                
                // Delete player's expired service data from shop services table
                log_message('debug', 'Module: sourcemod; Function: clear_expired; action: deleted shop services row. Id: '.$member->id);
                $this->core_model->del_service_data($member->id, $table_name);
            }
        }
    }
    
    
    public function check_groups()
    {
        $settings = $this->config->item('services_settings');
        
        foreach($this->module->services as $service => $value)
        {
            // Checking is made only for services with type = subscription
            if($settings[$service]['type'] == 'subscription')
            {
                
                // Check if flag group exists. If not then create it
                if( ! $this->check_group_exists($value['access_group']['name'], 'srvgroups'))
                {
                    $this->add_flag_group($service);
                }
                // If flag group exists then sync that group with config
                else
                {
                    $this->update_flag_group($service);
                }

                
                // Check if web group exists. If not then create it
                if( ! $this->check_group_exists($value['web_access_group']['name'], 'groups'))
                {
                    $this->add_web_group($service);
                }
                // If web group exists then sync that group with config
                else
                {
                    $this->update_web_group($service);
                }
            }    
        }
    }
    
    
    private function check_group_exists($group_name, $group_table)
    {
        $q = $this->db->where('name', $group_name)
                      ->get($group_table);
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    public function get_group($group_name, $group_table)
    {
        $q = $this->db->where('name', $group_name)
                      ->get($group_table);
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    private function add_web_group($service)
    {
        $data = array(
            'type' => 1,
            'name' => $this->module->services[$service]['web_access_group']['name'],
            'flags' => $this->module->services[$service]['web_access_group']['extraflags'],
        );
        
        $this->db->insert('groups', $data);
    }
    
    
    private function update_web_group($service)
    {
        $data = array(
            'flags' => $this->module->services[$service]['web_access_group']['extraflags'],
        );
        
        $this->db->where('name', $this->module->services[$service]['web_access_group']['name'])
                 ->update('groups', $data); 
    }  
    
    
    private function add_flag_group($service)
    {
        $data = array(
            'flags' => $this->module->services[$service]['access_group']['flags'],
            'immunity' => $this->module->services[$service]['access_group']['immunity'],
            'name' => $this->module->services[$service]['access_group']['name'],
            
        );
         
        $this->db->insert('srvgroups', $data);
    }
    
    
    private function update_flag_group($service)
    {
        $data = array(
            'flags' => $this->module->services[$service]['access_group']['flags'],
            'immunity' => $this->module->services[$service]['access_group']['immunity'],
        );

        $this->db->where('name', $this->module->services[$service]['access_group']['name'])
                 ->update('srvgroups', $data); 
    } 
    
    
    /**
     * Deletes player's ip ban
     * @param string $ipaddress
     */
    public function del_ban($ipaddress)
    {
        $this->db->where('ip', $ipaddress)
                 ->delete('bans');
    }
    
    
    /**
     * Deletes player's authid ban
     * @param string $ipaddress
     */
    public function del_ban_authid($authid)
    {
        $this->db->where('authid', $authid)
                 ->delete('bans');
    }
    
    
    /**
     * Checks for player's ip ban
     * @param type $ipaddress
     * @return boolean
     */
    public function check_ban($ipaddress)
    {
        $q = $this->db->where('ip', $ipaddress)
                      ->get('bans');
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    /**
     * Checks for player's authid ban
     * @param type $ipaddress
     * @return boolean
     */
    public function check_ban_authid($authid)
    {
        $q = $this->db->where('authid', $authid)
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
        $this->db->where('enabled', 1);
        
        if( ! empty($exclude))
        {
            $this->db->where_not_in('sid', $exclude);
        }
        $q = $this->db->get('servers');
        
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
        $q = $this->db->where('sid', $server_id)
                      ->get('servers');
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    public function add_admin($username, $password, $email, $service, $server_id, $authid)
    {
        $web_gid = $this->get_group($this->module->services[$service]['web_access_group']['name'], 'groups')->gid;
        
        $data = array(
            'user' => $username,
            'authid' => $authid,
            'password' => $password,
            'gid' => $web_gid,
            'email' => $email,
            'srv_group' => $this->module->services[$service]['access_group']['name'],
            'srv_flags' => NULL,
            'srv_password' => NULL,
        );
        
        $this->db->insert('admins', $data);
        
        // Unset data
        unset($data);        
        
        // Get insert id
        $player_id = $this->db->insert_id();
        
        // Get gid
        $srv_gid = $this->get_group($this->module->services[$service]['access_group']['name'], 'srvgroups')->id;
        
        $data = array(
            'admin_id' => $player_id,
            'group_id' => $srv_gid,
            'srv_group_id' => '-1',
            'server_id' => $server_id,
        );
        
        $this->db->insert('admins_servers_groups', $data);
        
        // Return insert id
        return $player_id;
    }
    
    
    public function update_admin($player_id, $service)
    {
        $data = array(
            'gid' => $this->get_group($this->module->services[$service]['web_access_group']['name'], 'groups')->gid,
            'srv_group' => $this->module->services[$service]['access_group']['name'],
        );
        
        $this->db->where('aid', $player_id)
                 ->update('admins', $data);
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
    
    
    
    public function search_player($username, $server_id)
    {
        $q = $this->db->where('user', $username)
                      ->join('admins_servers_groups', 'admins_servers_groups.admin_id = admins.aid')
                      ->where('admins_servers_groups.server_id', $server_id)
                      ->get('admins');
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    /**
     * 
     * @param type $username
     * @param type $server_id
     * @return type
     * @todo rewrite with active record
     */
    public function get_player($username, $server_id)
    {
        $sql = "SELECT sb_admins.*, sb_admins_servers_groups.*, sb_shop_services.* 
                FROM sb_admins, sb_admins_servers_groups, sb_shop_services
                WHERE sb_admins.aid = sb_admins_servers_groups.admin_id 
                AND sb_admins_servers_groups.admin_id = sb_shop_services.username
                AND sb_admins.user = ? 
                AND sb_admins_servers_groups.server_id = ?";
        
        $q = $this->db->query($sql, array($username, $server_id)); 
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    public function del_admin($player_id)
    {
        $this->db->where('admin_id', $player_id)
                 ->delete('admins_servers_groups');
        
        $this->db->where('aid', $player_id)
                 ->delete('admins');
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
    public function toplist_sourcemod($service)
    {
        $q = $this->db->select('*')
                      ->from($this->sql_services_table)
                      ->join('admins', 'admins.aid = ' . $this->sql_services_table . '.username')
                      ->where($this->sql_services_table . '.service', $service)
                      ->get();
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }    
            
            
}