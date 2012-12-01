<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class Rcon_minecraft {
    
    
    /**
     * Pointer
     */
    private $CI;
    
    
    /**
     * Variables 
     */
    public $server_rcon = '';
    
    public $server_hostname = '';
    
    public $server_port = '0';
    
    public $socket_timeout = 2;
    
    public $connected = FALSE;
    
    public $socket = FALSE;
    
    public $req_id = 0;
    
    public $max_retries = 1;
    
    public $retries = 0;
    
    public $throw_errors = TRUE;
    
    /**
     * Contants 
     */
    const SERVERDATA_EXECCOMMAND = 2;

    const SERVERDATA_AUTH = 3;

    const SERVERDATA_RESPONSE_VALUE = 0;

    const SERVERDATA_AUTH_RESPONSE = 2;
    
    
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
            log_message('debug', 'Minecraft Rcon class initialized');
        }
        else
        {
            log_message('debug', 'Minecraft Rcon class - missing config settings');
        }
        
        
        if( ! empty($this->server_hostname) && ! empty($this->server_port) && ! empty($this->server_rcon))
        {
            if($this->connect())
            {
                log_message('debug', 'Minecraft Server: '.$this->server_hostname.':'.$this->server_port.' connection OK');
            }
            else
            {
                if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav iespējams pieslēgties pie minecraft servera. Mēģini velāk.');
            }
        }
        else
        {
            log_message('debug', 'Minecraft Server: Missing connection settings!');
            
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

        $this->server_rcon = '';
        $this->server_hostname = '';
        $this->server_port = 0;
        $this->timeout = 5000;
        $this->socket = NULL;
        $this->req_id = 0;        
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
        $fp = @fsockopen(gethostbyname($this->server_hostname), $this->server_port, $errno, $errstr, $this->socket_timeout);
        if($fp)
        {
            $this->socket = $fp;
            
            stream_set_timeout($this->socket, $this->socket_timeout);
            
            if( ! $this->auth())
            {
                log_message('debug', 'Minecraft Server: '.$this->server_hostname.':'.$this->server_port.' authentication FAILED!');
                $this->disconnect();
                if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav iespējams autentificēties ar doto minecraft rcon paroli. Pārbaudiet uzstādījumus.');
                return FALSE;
            }
            else
            {
                return $this->connected = TRUE;
            }
        }
        else
        {
            log_message('debug', 'Minecraft Server: '.$this->server_hostname.':'.$this->server_port.' connection FAILED');
            return FALSE;
        }
    }
    
    
    private function auth()
    {
        if( ! $this->send_packet(self::SERVERDATA_AUTH, $this->server_rcon))
        {
            return FALSE;
        }

        $data = $this->read_packet();
        return $data['RequestId'] > -1 && $data['Response'] == self::SERVERDATA_AUTH_RESPONSE;
    }
    
    
    private function send_packet($command, $string)
    {
        $data = pack('VV', $this->req_id++, $command) . $string . "\x00\x00\x00"; 
        $data = pack('V', strlen($data)) . $data;
        $length = strlen($data);

        return $length === fwrite($this->socket, $data, $length);
    }
    
    
    private function read_packet()
    {
        $packet = array();
        $size = fread($this->socket, 4);

        if(strlen($size) == 4)
        {
            $size = unpack('V1Size', $size);
            $size = $size['Size'];
            $packet = fread($this->socket, $size);
            
            return unpack( 'V1RequestId/V1Response/a*String/a*String2', $packet );
        }
        else
        {
            if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Kļūda lasot minecraft savienojumu. Mēģiniet vēlāk vai pārbaudiet uzstādījumus!');
        }
    }
    
    
    public function communicate($string, $convert = FALSE)
    {
        if($this->connected)
        {    
            if( ! $this->send_packet(self::SERVERDATA_EXECCOMMAND, $string))
            {
                if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Kļūda sūtot datus minecraft serverim. Lūdzu mēģiniet vēlāk!');
                return FALSE;
            }

            $data = $this->read_packet();

            if($data['RequestId'] < 1 OR $data['Response'] != self::SERVERDATA_RESPONSE_VALUE)
            {
                return FALSE;
            }
            
            if(empty($data))
            {
                usleep(500000);
                
                if($this->retries == $this->max_retries)
                {
                    return $data['String'];
                }
                
                $this->retries++;
                
                return $this->communicate($string, TRUE);
            }
            else
            {
                $this->retries = 0;
            }
            
            
            if(defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50400)
            {
                return ($convert) ? htmlspecialchars(htmlentities($data['String'], ENT_IGNORE), ENT_IGNORE) : $data['String'];
            }
            else
            {
                return ($convert) ? htmlspecialchars($data['String']) : $data['String'];
            }
        }
    }
    
    
}