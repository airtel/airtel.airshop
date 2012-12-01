<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class General_model extends CI_Model {
    
    
    /**
     * Main variables
     */
    public $table_structure = array();
    
    public $donate_table = NULL;
    
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Loading database from config
        $this->load->database($this->config->item($this->module->active_module), FALSE, TRUE);
        
        // Load table structure
        $this->table_structure = $this->config->item($this->module->active_service.'_table_structure');
        
        // Private actions initialization
        $this->individual_init();
    }
    
    
    public function individual_init()
    {
        // Get name of services table
        $this->donate_table = $this->config->item('donate_sql_table');
    }
    
    
    public function get_donor($username)
    {
        $q = $this->db->where('username', $username)
                      ->get($this->donate_table);
        
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
    }
    
    
    public function add_donor($username, $message, $pay_method, $goods_amount)
    {
        $data = array(
            'user_id' => 0,
            'username' => $username,
            'message' => $message,
            $pay_method.'_money' => $goods_amount,
            'total' => $goods_amount,
            'datetime' => time(),
        );
        
        $this->db->insert($this->donate_table, $data);
    }

    
    public function update_donor($username, $message, $pay_method, $goods_amount)
    {
        $this->db->where('username', $username)
                 ->set('message', $message)
                 ->set($pay_method.'_money', $pay_method.'_money + '.$goods_amount, FALSE)
                 ->set('total', 'total + '.$goods_amount, FALSE)
                 ->set('datetime', time())
                 ->update($this->donate_table);
    }
    
    
    public function toplist_donate()
    {
        $q = $this->db->get($this->donate_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
}