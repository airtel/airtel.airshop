<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Core_model extends CI_Model {
    
    
    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * Parbauda airtel tabulas esamību datubāzē, un pēc vajadzības veic tās instalāciju
     * 
     * @param string $table_name 
     */    
    public function check_table($table_name, $table_structure, $add_key = TRUE)
    {
        if ( ! $this->db->table_exists($table_name))
        {
            $this->load->dbforge();
            $this->dbforge->add_field($table_structure);
            if($add_key)
            {
                $this->dbforge->add_key('id', TRUE);
            }
            $this->dbforge->create_table($table_name);
        }
    }
    
    
    /**
     * Function gets object with expired status
     * 
     * @param string $table_name
     * @return object
     */
    public function get_expired_members($module, $table_name)
    {
        $current_time = time();
        
        $q = $this->db->where('module', $module)
                      ->where('expires <', $current_time)
                      ->get($table_name);

        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function get_active_members($module, $table_name)
    {
        $current_time = time();
        
        $q = $this->db->where('module', $module)
                      ->where('expires >', $current_time)
                      ->get($table_name);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @param type $id
     * @param type $table_name
     */
    public function del_service_data($id, $table_name)
    {
        $this->db->where('id', $id)
                 ->delete($table_name);
    }    
    
    
    /**
     * 
     * @param type $username
     * @param type $table_name
     * @param type $username_field
     * @return type
     */
    public function search_valid_user($username, $table_name, $username_field)
    {
        $q = $this->db->where($username_field, $username)
                      ->get($table_name);
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }
    
    
}