<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Muonline_model extends CI_Model {
    
    
    /**
     * Main variables
     */
    public $table_structure = array();
    
    public $sql_services_table = NULL;
    
    
    /**
     * Muonline variables
     */
    public $auth_params = array();
    
    public $unban_params = array();
    
    public $credits_params = array();
    
    public $cspoints_params = array();
    
    public $skillbooster_params = array();
            
    public $gmaccess_params = array();


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
        // Load sql settings for muonline from config file
        $sql_params = $this->config->item('sql_params');
        
        // Get name of services table
        $this->sql_services_table = $this->config->item('sql_table');
        
        // Class sql params variables initialization
        $this->sql_init($sql_params);
    }
    
    
    /**
     * 
     * @param type $table_name
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
                // Gmaccess service
                if($member->service == 'gmaccess')
                {
                    // Remove gmaccess status
                    $this->del_gmaccess($member->username, $member->character);
                }
                
                // Vipserver service
                if($member->service == 'vipserver')
                {
                    // Remove vipserver status
                    $this->muonline_lib->del_vip_member($member->username);
                }
                
                // Delete from database
                $this->core_model->del_service_data($member->id, $table_name);
            }
        }
        
    }
    
    
    /**
     * 
     * @param type $username
     * @param type $credits
     */
    public function add_credits($username, $credits)
    {
        $this->db->where('memb___id', $username)
                 ->set('credits', 'credits + ' . $credits, FALSE)
                 ->update($this->credits_params['table_name']);
    } 
    
    
    /**
     * 
     * @param type $username
     * @param type $cspoints
     */
    public function add_cspoints($username, $cspoints)
    {
        $this->db->where('memb___id', $username)
                 ->set('cspoints', 'cspoints + ' . $cspoints, FALSE)
                 ->update($this->cspoints_params['table_name']);
    }
    
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function get_credits($username)
    {
        $q = $this->db->where('memb___id', $username)
                      ->get($this->credits_params['table_name']);
        
        return ($q->num_rows() > 0) ? $q->row()->credits : 0;
    }
    
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function get_cspoints($username)
    {
        $q = $this->db->where('memb___id', $username)
                      ->get($this->cspoints_params['table_name']);
        
        return ($q->num_rows() > 0) ? $q->row()->cspoints : 0;
    }
    
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function check_ban($username)
    {
        $q = $this->db->where('memb___id', $username)
                      ->where('bloc_code >', 0)
                      ->get($this->unban_params['table_name']);
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    /**
     * 
     * @param type $username
     */
    public function del_ban($username)
    {
        $this->db->where('memb___id', $username)
                 ->set('bloc_code', 0)
                 ->update($this->unban_params['table_name']);
    }
    
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function get_characters($username)
    {
        $q = $this->db->select('Name', 'CtlCode')
                      ->where('AccountID', $username)
                      ->get('Character');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Boost character skill
     * @param type $username
     * @param type $character
     * @param type $skill
     * @param type $amount
     */
    public function boost_skill($username, $character, $skill, $amount)
    {
        // Get max server skill amount
        $max_skill = $this->module->services['skillbooster']['max_skill'];
        
        // Get current skill amount
        $current_amount = (int)$this->get_skill_amount($username, $character, $skill);
        
        // Calculate new skill amount
        $new_amount = (($current_amount + $amount) > $max_skill) ? $max_skill : ($current_amount + $amount);

        $this->db->where('Name', $character)
                 ->where('AccountID', $username)
                 ->set($skill, $new_amount)
                 ->update($this->skillbooster_params['table_name']);
    }
    
    
    /**
     * Gets character skill amount
     * @param type $username
     * @param type $character
     * @param type $skill
     */
    public function get_skill_amount($username, $character, $skill)
    {
        $q = $this->db->select($skill)
                      ->where('Name', $character)
                      ->where('AccountID', $username)
                      ->get($this->skillbooster_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->row()->$skill;
        }
    }
    
    
    /**
     * Adds GM access for user's character
     * @param type $username
     * @param type $character
     */
    public function add_gmaccess($username, $character)
    {
        $this->db->where('Name', $character)
                 ->where('AccountID', $username)
                 ->set('CtlCode', $this->gmaccess_params['admin_code'])
                 ->update($this->gmaccess_params['table_name']);
    }
    
    
    /**
     * 
     * @param type $username
     * @param type $character
     */
    public function del_gmaccess($username, $character)
    {
        $this->db->where('Name', $character)
                 ->where('AccountID', $username)
                 ->set('CtlCode', 0)
                 ->update($this->gmaccess_params['table_name']);
    }
    
    
    /**
     * Add or update user data in services table
     * @param type $username
     * @param type $module
     * @param type $service
     * @param type $days
     * @param type $character
     */
    public function add_service_data($username, $module, $service, $days, $character = NULL)
    {
        // Get user from services table
        $user = $this->get_user_service_data($username, $module, $service, $character);
        
        // User already has service
        if($user !== FALSE)
        {
            // Update time
            $time = strtotime('+'.$days.' days', $user->expires);
            
            $this->db->where('username', $username)
                     ->where('module', $module)
                     ->where('service', $service)
                     ->where('character', $character)
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
                'character' => $character,
            );
            
            $this->db->insert($this->sql_services_table, $data);
        }
    }
    
    
    /**
     * Gets user from services table
     * @param type $username
     * @param type $module
     * @param type $service
     * @param type $character
     * @return object
     */
    public function get_user_service_data($username, $module, $service, $character = NULL)
    {
        $q = $this->db->where('username', $username)
                      ->where('module', $module)
                      ->where('service', $service)
                      ->where('character', $character)
                      ->get($this->sql_services_table);
        
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
    }
    
    
    /**
     * Custom toplist for gmaccess service
     * @return type
     */
    public function toplist_unban()
    {
        $q = $this->db->select('memb___id, memb_name, bloc_code, block_reason')
                      ->where('bloc_code >', 0)
                      ->get($this->unban_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Custom toplist for gmaccess service
     * @return type
     */
    public function toplist_credits()
    {
        $q = $this->db->get($this->credits_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Custom toplist for gmaccess service
     * @return type
     */
    public function toplist_cspoints()
    {
        $q = $this->db->select('memb___id, memb_name, cspoints')
                      ->get($this->cspoints_params['table_name']);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Custom toplist for gmaccess service
     * @return type
     */
    public function toplist_gmaccess()
    {
        $q = $this->db->where('service', 'gmaccess')
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * Custom toplist for vipserver service
     * @return type
     */
    public function toplist_vipserver()
    {
        $q = $this->db->where('service', 'vipserver')
                      ->get($this->sql_services_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
}