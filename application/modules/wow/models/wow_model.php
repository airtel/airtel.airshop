<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Wow_model extends CI_Model {
    
    
    /**
     * Main variables
     */
    public $table_structure = array();
    
    public $sql_services_table = NULL;
    
    private $auth_db = NULL;
    
    private $chars_db = NULL;
    
    private $web_db = NULL;
    
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->auth_db = $this->load->database($this->config->item('auth'), TRUE, TRUE);
        $this->chars_db = $this->load->database($this->config->item('chars'), TRUE, TRUE);
        $this->web_db = $this->load->database($this->config->item('web'), TRUE, TRUE);
    }
    

    /**
     * 
     * @param type $account
     */
    public function check_ban_account($account)
    {
        $q = $this->auth_db->select('a.id, a.username')
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
    
    
    /**
     * 
     * @param type $account
     */
    public function unban_account($account)
    {
        $this->auth_db->set('active', '0')
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
        $q = $this->chars_db->select('c.guid, c.name')
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
    
    
    /**
     * 
     * @param type $character
     */
    public function unban_character($character)
    {
        $this->chars_db->set('active', '0')
                       ->set('unbandate', time())
                       ->where("guid = (SELECT guid FROM characters WHERE name = '$character')", NULL, FALSE)
                       ->update('character_banned');
    }
    
    
    /**
     * Donate points
     * @param type $account
     */
    public function search_valid_web_user($account)
    {
        $q = $this->auth_db->where('username', $account)
                           ->get('account');

        if($q->num_rows() > 0)
        {
            $id = $this->web_db->where('id', $q->row()->id)->get('account_data')->row()->id;

            if($id != NULL)
            {
                return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
    
    public function add_donate_points($account, $dpoints)
    {
        $id = $this->auth_db->where('username', $account)
                            ->get('account')
                            ->row()->id;
        
        $this->web_db->where('id', $id)
                 ->set('dp', 'dp + '.$dpoints, FALSE)
                 ->update('account_data');
    }
    
    
    /**
     * 
     * @param type $ip
     */
    public function check_ban_ip($ip)
    {
        $q = $this->auth_db->where('ip', $ip)
                           ->get('ip_banned');
        
        if($q->num_rows() > 0)
        {
            return TRUE;
        }
    }
    
    
    /**
     * 
     * @param type $ip
     * auth db
     */
    public function unban_ip($ip)
    {
        $this->auth_db->where('ip', $ip)->delete('ip_banned');
    }
    

    
    /**
     * Function checks if player is online
     * @param type $username
     * @return boolean
     */
    public function check_online($character)
    {
        $q = $this->chars_db->select('name, online')
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
        $this->chars_db->set('money', 'money + '.$gold, FALSE)
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
        $this->chars_db->set('xp', 'xp + '.$exp, FALSE)
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
        $current_level = $this->chars_db->where('name', $character)->get('characters')->row()->level;
        
        // Get boosted level
        $boost = (($current_level + $level) >= $max_level) ? $max_level : $current_level + $level;
        
        // Do level boost
        $this->chars_db->set('level', $boost)
                       ->where('name', $character)
                       ->update('characters');
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_unban_account()
    {
        $q = $this->auth_db->select('b.bandate, b.bannedby, b.banreason')
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
        $q = $this->chars_db->select('b.bandate, b.bannedby, b.banreason')
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
        $q = $this->auth_db->get('ip_banned');
        
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
        $q = $this->chars_db->select('name, money')
                            ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_levelup()
    {
        $q = $this->chars_db->select('name, level')
                            ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    /**
     * 
     * @return type
     */
    public function toplist_exp()
    {
        $q = $this->chars_db->select('name, xp')
                            ->get('characters');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function search_valid_user($username, $table_name, $username_field)
    {
        $q = $this->chars_db->where($username_field, $username)
                      ->get($table_name);
        
        return ($q->num_rows() > 0) ? TRUE : FALSE;
    }    

    
}