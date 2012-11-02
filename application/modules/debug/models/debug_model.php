<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Debug_model extends CI_Model {
    

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function check_table($database, $table_name)
    {
        // Loading database
        $this->load->database($database);
        
        if( ! $this->db->table_exists($table_name))
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
}