<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class Rcon_srcds {
    
    
    /**
     * Variables 
     */
    protected $server_rcon = '';
    
    protected $server_hostname = '';
    
    protected $server_port = 0;
    
    protected $timeout = 5;
    
    protected $connected = FALSE;
    
    protected $socket = FALSE;
    
    protected $req_id = 0;
    
    
    /**
     * Contants 
     */
    const SERVERDATA_EXECCOMMAND = 2;
    
    const SERVERDATA_AUTH = 3;
    
    const SERVERDATA_RESPONSE_VALUE = 0;
    
    const SERVERDATA_AUTH_RESPONSE = 2;
  
    
    /**
     * Klases konstruktors
     * 
     * @param array $config 
     */
    public function __construct($config = array())
    {
        if (count($config) > 0)
        {
            $this->init($config);
        }
        
        log_message('debug', 'SRCDS Rcon class initialized');
        
        if($this->server_hostname != '' AND $this->server_port > 0 AND $this->server_rcon != '')
        {
            if($this->connect())
            {
                log_message('debug', 'SRCDS Server: '.$this->server_hostname.':'.$this->server_port.' connection OK');
            }
            else
            {
                log_message('debug', 'SRCDS Server: '.$this->server_hostname.':'.$this->server_port.' connection FAILED');
            }
        }
    }
    
    
    function __destruct()
    {
        $this->clear();
    }
   
    
    /**
     * Klases preferenču inicializācija
     * 
     * @param array $config
     * @return \Rcon_srcds
     */
    public function init($config = array())
    {
        foreach ($config as $key => $val)
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
    
    
    private function clear()
    {
        $this->disconnect();

        $this->server_rcon = '';
        $this->server_hostname = '';
        $this->server_port = 0;
        $this->timeout = 5;
        $this->connected = FALSE;
        $this->socket = FALSE;
        $this->req_id = 0;        
    }
    
    
    /**
     * Aizver socketu un uzstāda connected uz FALSE
     * @return boolean 
     */
    public function disconnect()
    {
        if($this->socket !== FALSE)
        {
            socket_close($this->socket);
        }    
        $this->connected = FALSE;
        return TRUE;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function connect()
    {
        if(($this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE)
        {
            log_message('debug', 'SRCDS socket_create: FAILED');
            return FALSE;
        }
        else
        {
            log_message('debug', 'SRCDS socket_create: SUCCESS');
        }
        
        if(socket_connect($this->socket, $this->server_hostname, $this->server_port) === FALSE)
        {
            log_message('debug', 'SRCDS socket_connect: FAILED');
            return $this->socket = FALSE;
        }
        else
        {
            log_message('debug', 'SRCDS socket_connect: SUCCESS');
        }
        
        
        $this->send_packet($this->server_rcon, NULL, self::SERVERDATA_AUTH);
        $result = $this->read_packet();
        
        
        if( ! isset($result[0]['CommandResponse']) OR $result[0]['CommandResponse'] != self::SERVERDATA_AUTH_RESPONSE)
        {
            //$this->__destruct();
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    
    private function send_packet($string1, $string2 = NULL, $command = self::SERVERDATA_EXECCOMMAND)
    {
        $packet = $string1 . "\x00" . $string2 . "\x00";
        $packet = pack('VV', ++$this->req_id, $command) . $packet;
        $packet = pack('V', strlen($packet)) . $packet;
        
        if(socket_send($this->socket, $packet, strlen($packet), 0x00) === FALSE)
        {
            log_message('debug', 'SRCDS socket_send: FAILED');
        }
        else
        {
            log_message('debug', 'SRCDS socket_send: SUCCESS');
            return TRUE;
        }
    }
    
    
    private function read_packet()
    {
        
        $packets = array();
        
        $read = array($this->socket);
        
        $buffer = '';
        
        $write = NULL;
        
        $except = NULL;
        
        while(socket_select($read, $write, $except, 0, $this->timeout))
        {
            // Get packet length
            if (strlen($buffer) == 0) 
            {
                $packet_length = unpack('V1PacketLength', socket_read($read[0], 4));
            }
            
            $buffer .= socket_read($read[0], $packet_length['PacketLength'] - strlen($buffer));
            
            if (strlen($buffer) == $packet_length['PacketLength'])
            {
                $packet = unpack('V1RequestID/V1CommandResponse/a*String1/a*String2', $buffer);
                $buffer = '';

                if (isset($packets[$packet['RequestID']]) && $packet['CommandResponse'] != self::SERVERDATA_AUTH_RESPONSE)
                {
                    $packets[$packet['RequestID']]['String1'] .= $packet['String1'];
                    $packets[$packet['RequestID']]['String2'] .= $packet['String2'];
                } 
                else 
                {
                    $packets[$packet['RequestID']] = $packet;
                }
            }
        }
        
        return array_values($packets);
    }
    
    
    public function rcon($command) 
    {
        if($this->socket === FALSE)
        {
            return FALSE;
        }
        else
        {
            $this->send_packet($command);
            return $this->read_packet();
        }
    }
    
    
}