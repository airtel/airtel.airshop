<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class War_model extends CI_Model {
    
    
    /**
     * Variables
     */
    public $ammo_table = NULL;
    
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Loading database from config
        //foreach($this->module->services as $server)
        //{
            // Example $this->zm = $this->load->database($this->config->item('zm'), TRUE);
        //    $this->{$server['db_array_name']} = $this->load->database($this->config->item($server['db_array_name']), TRUE);
        //}

        //
        
        // Temporary fix
        if($this->uri->segment(2) == 'index')
        {
            $this->{$this->module->services[$this->module->active_service]['db_array_name']} = $this->load->database($this->config->item($this->module->services[$this->module->active_service]['db_array_name']), TRUE);
        }
        elseif($this->uri->segment(2) == 'war_ajax_call')
        {
            $dbname = $this->module->services[$this->uri->segment(4)]['db_array_name'];
            
            $this->$dbname = $this->load->database($this->config->item($dbname), TRUE, TRUE);
        }
        
        
        // Private actions initialization
        $this->individual_init();
    }
    
    
    public function individual_init()
    {
        if(array_key_exists('zmammo', $this->module->services))
        {
            $this->ammo_table = $this->module->services['zmammo']['db_sql_table'];
        }
    }
    
    
    public function get_server_races($db)
    {
        $q = $this->{$db}->select('race_id, race_name')
                         ->where('race_lang', 'en')
                         ->get('web_race');
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }

    
    public function add_exp($db, $player_id, $race_id, $amount)
    {
        $this->{$db}->set('race_xp', 'race_xp + '.$amount, FALSE)
                    ->where('race_id', $race_id)
                    ->where('player_id', $player_id)
                    ->update('player_race');
    }
    
    
    public function get_player($db, $username, $race_id)
    {
        $q = $this->{$db}->select('*')
                         ->from('player')
                         ->join('player_race', 'player_race.player_id = player.player_id')
                         ->where('player.player_name', $username)
                         ->where('player_race.race_id', $race_id)
                         ->get();
         
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
    }
    
    
    public function get_ammo_player($db, $username)
    {
        $q = $this->{$db}->where('auth', $username)
                         ->get($this->ammo_table);
        
        return ($q->num_rows() > 0) ? $q->row() : FALSE;
    }
    
    
    public function add_ammo($db, $username, $amount)
    {
        $this->{$db}->set('amount', 'amount + '.$amount, FALSE)
                    ->where('auth', $username)
                    ->update($this->ammo_table);
    }
    
    
    public function toplist_exp($db)
    {
        $q = $this->{$db}->select('player.player_id, player.player_name')
                         ->select('player_race.player_id, player_race.race_id, player_race.race_xp')
                         ->select('web_race.race_id, web_race.race_lang, web_race.race_name')
                         ->from('player')
                         ->join('player_race', 'player_race.player_id = player.player_id')
                         ->join('web_race', 'web_race.race_id = player_race.race_id')
                         ->where('web_race.race_lang', 'en')

                         ->order_by('player_race.race_xp', 'desc')
                         ->limit(100)
                         ->get();
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
    public function toplist_zmammo($db)
    {
        $q = $this->{$db}->get($this->ammo_table);
        
        if($q->num_rows() > 0)
        {
            return $q->result();
        }
    }
    
    
}