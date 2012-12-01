<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Ipb_model extends CI_Model {
    
    
    /**
     * Variables
     */
    public $settings = '';

    public $ibeconomy = '';
    
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Get ipb settings
        $this->settings = $this->ipb_db->get_ibp_settings();        
        
        // Loading database from config
        $this->load->database($this->settings, FALSE, TRUE);
        
        // Private actions initialization
        $this->individual_init();
    }
    
    
    public function individual_init()
    {
        // Get ibeconomy settings
        if(array_key_exists('ipoints', $this->module->services))
        {
            $this->ibeconomy = $this->module->services['ipoints']['ibeconomy'];
        }   
    }
    
    
    /**
     * Updates member's title
     * @param type $member_id
     * @param type $title
     */
    public function update_member_title($member_id, $title)
    {
        $this->db->where($this->settings['id_column'], $member_id)
                 ->set('title', $title)
                 ->update('members');
    }
    
    
    /**
     * Removes suspended member's suspended account status
     * @param type $member_id
     */
    public function unsuspend_member($member_id)
    {
        $this->db->where($this->settings['id_column'], $member_id)
                 ->set('restrict_post', 0)
                 ->set('temp_ban', 0)
                 ->update('members');
    }
    
    
    /**
     * Changes member's display name
     * @param type $member_id
     * @param type $display_name
     */
    public function change_displayname($member_id, $display_name)
    {
        $this->db->where($this->settings['id_column'], $member_id);
        
        /**
         * v3 columns
         * members_display_name - real display name
         * members_l_display_name - small caps
         * members_seo_name - web friendly name
         * 
         * v2 columns
         * members_display_name - real display name
         * members_l_display_name - small caps
         */
        
        $this->db->set('members_display_name', $display_name);
        $this->db->set('members_l_display_name', strtolower($display_name));
        
        if($this->ipb_version == 3)
        {
            $seo_display_name = $this->ipb_lib->ipb_convert_to_seoname($display_name);
            
            $this->db->set('members_seo_name', $seo_display_name);
        }
        
        $this->db->update('members');
    }
    
    
    /**
     * Updates warn level
     * @param type $member_id
     * @param type $new_level
     */
    public function update_member_warnlevel($member_id, $new_level)
    {
        $this->db->where($this->settings['id_column'], $member_id)
                 ->set('warn_level', $new_level)
                 ->update('members');
    }
    
    
    /**
     * Function adds ipoints to member account
     * @param type $member_id
     * @param type $ipoints
     */
    public function add_member_ipoints($member_id, $ipoints)
    {
        if($this->ibeconomy AND $this->settings['ipb_version'] == 3)
        {
            $this->db->where('member_id', $member_id)
                     ->set('eco_points', 'eco_points + ' . $ipoints, FALSE)
                     ->update('pfields_content');
        }
        else
        {
            $this->db->where($this->settings['id_column'], $member_id)
                     ->set('points', 'points + '.$ipoints, FALSE)
                     ->update('members');
        }
    }
    
    
    /**
     * 
     * @param type $member_id
     * @return type
     */
    public function get_member_ipoints($member_id)
    {
        if($this->ibeconomy AND $this->settings['ipb_version'] == 3)
        {
            $q = $this->db->select('eco_points')
                          ->where('member_id', $member_id)
                          ->get('pfields_content');
            
            if($q->num_rows() > 0)
            {
                return $q->row()->eco_points;
            }
        }
        else
        {
            $q = $this->db->select('points')
                          ->where($this->settings['id_column'], $member_id)
                          ->get('members');
            
            if($q->num_rows() > 0)
            {
                return $q->row()->points;
            }
        }
    }
    
    
    /**
     * Checks if ibeconomy table is installed
     * @return boolean
     */
    public function check_ibeconomy_module()
    {
        return ( ! $this->db->table_exists('pfields_content')) ? FALSE : TRUE;
    }
    
    
    /**
     * Works on 2.x and 3.x versions
     * Gets member login name by its member ID extracted from cookiezzz...
     * @param int $member_id
     * @return string member name
     */
    public function get_data_by_id($member_id)
    {
        $q = $this->db->where($this->settings['id_column'], $member_id)
                      ->get('members');
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    public function get_data_by_login($member_login)
    {
        $q = $this->db->where('name', $member_login)
                      ->get('members');
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
    public function check_suspended($username)
    {
        $q = $this->db->where('name', $username)
                      ->where('temp_ban >', 0)
                      ->or_where('restrict_post >', 0)
                      ->get('members');
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    public function check_displayname($username)
    {
        $q = $this->db->where('members_display_name', $username)
                      ->get('members');
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
    public function toplist_ipoints()
    {
        if($this->ibeconomy && $this->settings['ipb_version'] == 3)
        {
            $this->db->select('m.member_id as id')
                     ->select('m.members_display_name as name')
                     ->select('c.eco_points as points')
                     ->from('members as m')
                     ->join('pfields_content as c', 'c.member_id = m.member_id');
        }
        else
        {
            $this->db->select($this->settings['id_column'])
                     ->select('members_display_name as name, points')
                     ->from('members');
        }
        
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : FALSE;
    }
    
    
    /***********************************************************************************************
     * Login login functions
     */    
   
    
    /**
     * Version 3.x only
     * Gets cookie prefix from database or returns FALSE if no prefix found.
     * @return string Cookie prefix
     */
    public function get_cookie_prefix()
    {
        $q = $this->db->select('conf_value')
                      ->where('conf_title', 'cookie prefix')
                      ->get('core_sys_conf_settings');
        
        if($q->num_rows() > 0)
        {
            return $q->row()->conf_value;
        }
        else
        {
            return FALSE;
        }
    }
    
    
    /**
     * Works on 2.x and 3.x versions
     * For v2 Function gets member_log_in_key
     * For v3 Function gets pass_hash ( column name is same with v2 )
     * @param int $member_id 
     */
    public function get_member_log_in_key($member_id)
    {
        $q = $this->db->select('member_login_key')
                      ->where($this->settings['id_column'], $member_id)
                      ->get('members');
        
        if($q->num_rows() > 0)
        {
            return $q->row()->member_login_key;
        }
    }
    
    
    /**
     * Needed ONLY for 2.x versions
     * Gets security data from DB table for passhash generation
     * @param type $email
     * @return type
     */
    public function get_member_converge_data($email)
    {
        $q = $this->db->where('converge_email', $email)
                      ->get('members_converge');
        
        if($q->num_rows() > 0)
        {
            return $q->row();
        }
    }
    
    
}