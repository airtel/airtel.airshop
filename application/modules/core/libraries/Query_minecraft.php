<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class Query_minecraft {
    
    
    /**
     * Pointer
     */
    private $CI;
    
    
    /**
     * Variables 
     */
    public $server_hostname = '';
    
    public $query_port = 0;
    
    public $socket_timeout = 2;
    
    public $socket = FALSE;
    
    public $connected = FALSE;
    
    public $throw_erros = TRUE;
    
    /**
     * Contants 
     */
    const STATISTIC = 0x00;
    
    /**
     * http://dinnerbone.com/blog/2011/10/14/minecraft-19-has-rcon-and-query/
     * Challange type is 0x09
     * 
     */
    const HANDSHAKE = 0x09;
    
    
    /**
     * Class constructor
     * 
     * @param array $config 
     */
    public function __construct($config = array())
    {
        $this->CI =& get_instance();
        
        if (count($config) > 0)
        {
            $this->init($config);
            log_message('debug', 'Minecraft Query class initialized');
        }
        else
        {
            log_message('debug', 'Minecraft Query class - missing config settings');
        }
        
        
        if( ! empty($this->server_hostname) && ! empty($this->query_port))
        {
            if($this->connect())
            {
                log_message('debug', '(Query class) Minecraft Server: '.$this->server_hostname.':'.$this->query_port.' connection OK');
            }
            else
            {
                if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav iespējams pieslēgties pie minecraft servera. Mēģini velāk.');
            }
        }
        else
        {
            log_message('debug', '(Query class) Minecraft Server: Missing connection settings!');
            if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Minecraft Server is missing connection settings. Check config file');
        }
    }
    
    
    /**
     * Class preferences initialization
     * 
     * @param array $config
     * @return \Rcon_hlds
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
    
    
    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->clear();
    }
    
    
    private function clear()
    {
        $this->disconnect();

        $this->server_hostname = '';
        $this->query_port = 0;
        $this->socket_timeout = 2;
        $this->socket = NULL;
    }  
    
    
    /**
     * Closes socket and sets connected boolean to false
     * @return boolean
     */
    public function disconnect()
    {
        if($this->socket !== FALSE)
        {
            fclose($this->socket);
        }    
        $this->connected = FALSE;
        return TRUE;
    }  
    
    
    public function connect()
    {
        $fp = @fsockopen('udp://'.gethostbyname($this->server_hostname), $this->query_port, $errno, $errstr, $this->socket_timeout);
        if($fp)
        {
            $this->socket = $fp;
        
            stream_set_timeout($this->socket, $this->socket_timeout);
            stream_set_blocking($this->socket, TRUE);
            
            return $this->connected = TRUE;
        }
        else
        {
            return $this->connected = FALSE;
        }
    }
    
    
    public function get_challenge()
    {
        $packet = $this->send_packet(self::HANDSHAKE);
        
        if($packet === FALSE)
        {
            if($this->throw_errors) $this->CI->error_handler->show_error('error', '(Minecraft Query) Nav iespējams saņemt "Challange" uz serveri!');
            exit;
        }
        
        return pack('N', $packet);
    }
    
    
    private function get_status($challenge)
    {
        if($this->connected == TRUE)
        {
            $packet = $this->send_packet(self::STATISTIC, $challenge . pack('c*', 0x00, 0x00, 0x00, 0x00));

            if( ! $packet)
            {
                // Failed to get status
            }

            $packet = substr($packet, 11);
            $packet = explode("\x00\x00\x01player_\x00\x00", $packet);
            
            if( ! empty($packet[1]))
            {
                $players = substr($packet[1], 0, -2);
            }
            
            if(isset($players) && ! empty($players))
            {
                return explode("\x00", $players);;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
    
    public function send_packet($command, $append = '')
    {
        if($this->connected == TRUE)
        {
            $command = pack('c*', 0xFE, 0xFD, $command, 0x01, 0x02, 0x03, 0x04) . $append;
            $length  = strlen($command);

            if($length !== fwrite($this->socket, $command, $length))
            {
                // Failed to write into socket
            }

            $packet = fread($this->socket, 2048);


            if($packet === FALSE)
            {
                // Failed to read
            }


            if( strlen($packet) < 5 || $packet[0] != $command[2])
            {
                return FALSE;
            }

            return substr($packet, 5);
        }
    }
    
    
    public function get_players()
    {
        $challenge = $this->get_challenge();
        
        $players = $this->get_status($challenge);
        
        return $players;
    }
    
    
}