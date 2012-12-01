<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Wow_model extends CI_Model {
    
    
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
    }

    
    /**
     * 
     * @param type $account
     */
    public function check_ban_account($account)
    {
        $q = $this->db->select('a.id, a.username')
                      ->from('account a')
                      ->join('account_banned b', 'a.id = b.id')
                      ->where('a.username', $account)
                      ->where('b.active', '1')
                      ->get('');

        if($q->num_rows() > 0)
        {
            return TRUE;
        }
    }
    
    
    public function unban_account($account)
    {
        $this->db->set('active', '0')
                 ->set('unbandate', time())
                 ->where("id = (SELECT id FROM account WHERE username = '$account')", NULL, FALSE)
                 ->update('account_banned');
    }
    
    
    /**
     * 
     * @param type $character
     */
    public function check_ban_character($character)
    {
        $q = $this->db->select('c.guid, c.name')
                      ->from('characters c')
                      ->join('character_banned b', 'c.guid = b.guid')
                      ->where('c.name', $character)
                      ->where('b.active', '1')
                      ->get('');
        
        if($q->num_rows() > 0)
        {
            return TRUE;
        }
    }
    
    
    public function unban_character($character)
    {
        $this->db->set('active', '0')
                 ->set('unbandate', time())
                 ->where("guid = (SELECT guid FROM characters WHERE name = '$character')", NULL, FALSE)
                 ->update('character_banned');
    }
    
    
    /**
     * 
     * @param type $ip
     */
    public function check_ban_ip($ip)
    {
        $q = $this->db->where('ip', $ip)
                      ->get('ip_banned');
        
        if($q->num_rows() > 0)
        {
            return TRUE;
        }
    }
    
    
    public function unban_ip($ip)
    {
        $this->db->where('ip', $ip)->delete('ip_banned');
    }
    

    
    /**
     * Function checks if player is online
     * @param type $username
     * @return boolean
     */
    public function check_online($character)
    {
        $q = $this->db->select('name, online')
                      ->where('name', $character)
                      ->where('online', '1')
                      ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return TRUE;
        }
    }
    
    
    /**
     * Adds money amount to specified character
     * @param type $character
     * @param type $gold
     */
    public function add_gold($character, $gold)
    {
        $this->db->set('money', 'money + '.$gold, FALSE)
                 ->where('name', $character)
                 ->update('characters');
    }
    
    
    /**
     * Adds XP amount to specified character
     * @param type $character
     * @param type $gold
     */
    public function add_exp($character, $exp)
    {
        $this->db->set('xp', 'xp + '.$exp, FALSE)
                 ->where('name', $character)
                 ->update('characters');
    }
    
    
    /**
     * Adds level to specified character
     * @param type $character
     * @param type $gold
     */
    public function add_levelup($character, $level)
    {
        // Get limit
        $max_level = $this->module->services['levelup']['max_level'];
        
        // Get current level
        $current_level = $this->db->where('name', $character)->get('characters')->row()->level;
        
        // Get boosted level
        $boost = (($current_level + $level) >= $max_level) ? $max_level : $current_level + $level;
        
        // Do level boost
        $this->db->set('level', $boost)
                 ->where('name', $character)
                 ->update('characters');
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_unban_account()
    {
        $q = $this->db->select('b.bandate, b.bannedby, b.banreason')
                      ->select('a.username')
                      ->from('account_banned b')
                      ->join('account a', 'a.id = b.id')
                      ->where('b.active', '1')
                      ->get('');
                      
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_unban_character()
    {
        $q = $this->db->select('b.bandate, b.bannedby, b.banreason')
                      ->select('c.name')
                      ->from('character_banned b')
                      ->join('characters c', 'c.guid = b.guid')
                      ->where('b.active', '1')
                      ->get('');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_unban_ip()
    {
        $q = $this->db->get('ip_banned');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_gold()
    {
        $q = $this->db->select('name, money')
                      ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function toplist_levelup()
    {
        $q = $this->db->select('name, level')
                      ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function toplist_exp()
    {
        $q = $this->db->select('name, xp')
                      ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    
}