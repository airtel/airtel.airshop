<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class SSH {
    
    
    /**
     * Superclass Pointer
     */
    private $CI;
    
    
    /**
     * Variables 
     */
    public $hostname = '';
    
    public $username = '';
    
    public $password = '';
    
    public $port = 22;
    
    public $debug = TRUE;
    
    public $conn_id = FALSE;
    
    public $data = array();
    
    public $throw_errors = TRUE;
    
    
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
            log_message('debug', 'SSH2lib Class initialized');
        }
        else
        {
            log_message('debug', 'SSH2lib Class - missing config settings');
            if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav ievadīti visi nepieciešamie parametri konfigurācijas failā!');
        }
        
        if($this->connect())
        {
            log_message('debug', 'SSH2Lib Class: '.$this->hostname.':'.$this->port.' connection OK');
        }
        else
        {
            if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav iespējams pieslēgties pie Hosting servera. Mēģini velāk.');
        }
    }
    
    
    public function __destruct() 
    {
        $this->disconnect();
        
        $this->hostname = '';
        $this->username = '';
        $this->password = '';
        $this->port = 22;
        $this->debug = TRUE;
        $this->conn_id = FALSE;
        $this->data = array();
        $this->throw_errors = TRUE;
    }
    
    
    public function disconnect()
    {
        if($this->_is_conn()) 
        {
            ssh2_exec($this->conn_id, 'echo "EXITING" && exit;');
            unset($this->conn_id);
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
    
    
    public function connect() 
    {
        //if (FALSE === ($this->conn_id = ssh2_connect($this->hostname, $this->port)))
        if (FALSE === ($this->conn_id = @ssh2_connect($this->hostname, $this->port)))
        {
            log_message('debug', 'SSH2lib: unable to connect to romete server');
            return FALSE;
        }

        if ( ! $this->_login())
        {
            if ($this->debug == TRUE)
            {
                log_message('debug', 'SSH2lib: unable to login with provided auth settings');
                if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav iespējams autentificēties ar doto SSH paroli. Pārbaudiet uzstādījumus.');
            }
            return FALSE;
        }

        return TRUE;
    }

    
    /**
     * Performs login operation
     * @return type
     */
    private function _login() 
    {
        return @ssh2_auth_password($this->conn_id, $this->username, $this->password);
    }
    
    
    /**
     * Checks if there is open connection
     * @return boolean
     */
    public function _is_conn() 
    {
        if ( ! is_resource($this->conn_id)) 
        {
            if ($this->debug == TRUE) 
            {
                log_message('debug', 'SSH2lib: Lost connection. Connection stream is not resource.');
            }
            return FALSE;
        }
        return TRUE;
    }
    
    
    /**
     * Exec's string commmand on remote server
     * @param type $command
     * @return boolean
     */
    public function execute($command = '', $get_output = FALSE) 
    {
        if($this->_is_conn()) 
        {
            $stream = ssh2_exec($this->conn_id, $command);

            return ($get_output) ? $this->_get_output($stream) : TRUE;

            //return $this->_get_stream_data($stream);
        }
        else 
        {
            log_message('debug', 'SSH2lib: Unable to execute command');
            if($this->throw_errors) $this->CI->error_handler->show_error('error', 'Nav iespējams izpildīt SSH komandu.');
            return FALSE;
        }
    }
    
    
    /**
     * This shit can slow down all shop
     */
    private function _get_output($stream)
    {
        stream_set_blocking($stream, TRUE);
        
        $this->data = array();
        while ($get = fgets($stream))
        {
            $this->data[] = $get;
        }
        return $this->data;
        
    }
    
    
    private function _get_stream_data($stream) 
    {
        stream_set_blocking($stream, TRUE);
        while($buf = fread($stream, 4096)) 
        {
            $this->data .= $buf.'~';
        }
        return TRUE;
    }

    
}