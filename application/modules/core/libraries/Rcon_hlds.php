<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed');


class Rcon_hlds {
    
    /**
     *
     * @var type 
     */
    private $challenge_number = 0;
    
    /**
     * Servera konekcijas stavoklis
     * @var boolean
     */
    public $connected = FALSE;
    
    /**
     * Servera ip adrese vai hosts
     * @var type 
     */
    public $server_ip = '';
    
    /**
     * Servera rcon parole
     * @var string
     */
    public $server_password = '';
    
    /**
     * Servera ports
     * @var integer
     */
    public $server_port = '0';
    
    /**
     * Satur socketa stream'u
     * @var string
     */
    private $socket = '';
    
    
    /**
     * Pieslēgšanās timeout sekundēs.
     * @var integer
     */
    public $socket_timeout = 2;
    
    
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
        
        log_message('debug', 'Hlds Rcon Class Initialized');
        
        if($this->server_ip != '' AND $this->server_port != '' AND $this->server_password != '')
        {
            if($this->connect())
            {
                log_message('debug', 'Server: '.$this->server_ip.':'.$this->server_port.' connected OK');
            }    
        }
    }
    
    
    /**
     * Klases preferenču inicializācija
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
     * Funkcija atslēdzas no servera un attīra visus datus.
     * Tiek izpildīta pēc darbību veikšanas ar konkrēto serveri.
     */
    public function clear()
    {
        $this->disconnect();
        
        $this->challenge_number = 0;
        $this->server_ip = '';
        $this->server_password = '';
        $this->server_port = 0;
        $this->socket = '';
    }
    
    
    /**
     * Funkcija atslēdzas no servera un uzstāda konekciju uz FALSE 
     * 
     * @return boolean
     */
    public function disconnect()
    {
        @fclose($this->socket);
        $this->connected = FALSE;
        return TRUE;
    }
    
    
    /**
     * Funkcija atgriež konekcijas statusu
     * 
     * @return boolean
     */
    public function is_connected()
    {
        return ($this->socket) ? TRUE : FALSE;
    }
    
    
    /**
     * Funkcija pieslēdzas pie hlds servera
     * 
     * @return stream or FALSE
     */
    public function connect()
    {
        /**
         * HLDS serveri izmanto UDP, SRCDS serveri izmanto TCP lai pieslēgtos
         */
        $fp = fsockopen('udp://'.gethostbyname($this->server_ip), $this->server_port, $errno, $errstr, $this->socket_timeout);
        stream_set_timeout($fp, $this->socket_timeout);
        
        // $errno un $errstr saturēs errora gadījuma skaitli ar erroru un iemeslu
        // būtu labi viņus iebāzt pectam kautkur.
        
        if( ! $fp)
        {
            $this->connected = FALSE;
        }
        else 
        {
            $this->socket = $fp;
            return $this->connected = TRUE;
        }
    }
    
    
    /**
     * Funkcija nosūta komandu hlds serverim un atgriež servera atbildi
     * 
     * @param string $command
     * @param integer $pagenumber
     * @param boolean $single
     * @return string
     */
    public function rconcommand($command, $pagenumber = 0, $single = TRUE)
    {
        //If there is no open connection return false
        if( ! $this->connected)
        {
            //return $this->connected;
            return FALSE;
        }    
        else
        {    
            //get challenge number
            $this->get_challenge();

            $command = "\xff\xff\xff\xffrcon $this->challenge_number \"$this->server_password\" $command\n";

            //get specified page
            $result = '';
            $buffer = '';
            while($pagenumber >= 0)
            {
                //send rcon command
                $buffer .= substr($this->communicate($command), 1);

                //get only one package
                if($single == TRUE)
                    $result = $buffer;

                //get more then one package and put them together
                else
                    $result .= $buffer;

                
                //clear command for higher iterations
                $command = '';
                $pagenumber--;
            }
            return trim($result);
        }
    }
    
    
    private function get_challenge()
    {
        if(empty($this->challenge_number))
        {
            $challenge = "\xff\xff\xff\xffchallenge rcon\n";
            $buffer = $this->communicate($challenge);

            // Ja nav atvērta konekcija
            if(trim($buffer) == '')
            {
                return $this->connected = FALSE;
            }
            else
            {
                $this->challenge_number = trim(substr($buffer, 15));
                return TRUE;
            }
        }    
    }
    
    
    function communicate($command)
    {
        if( ! $this->connected)
        {
            return FALSE;
        }
        else
        {
            if( ! empty ($command))
                fwrite($this->socket, $command, strlen($command));
            
            $buffer = fread($this->socket, 1);
            $status = socket_get_status($this->socket);
            
            // Sander's fix:
            if ($status['unread_bytes'] > 0) {
                $buffer .= fread($this->socket, $status['unread_bytes']);
            }
            
            //If there is another package waiting
            if(substr($buffer, 0, 4) == "\xfe\xff\xff\xff")
            {
                //get requestid from split packages
                $requestid = substr($buffer, 4, 4);

                //get number of packages
                $po = ord(substr($buffer, 8, 1));
                $panum = ($po & 1) + ($po & 2) + ($po & 4) + ($po & 8);

                //get number from current package
                $po = $po >> 4;
                $pacur = ($po & 1) + ($po & 2) + ($po & 4) + ($po & 8);

                //add the first package to the array
                
                $splitbuffer[$pacur] = ($pacur == ($panum - 1)) ? substr($buffer, 9) : substr($buffer, 14);
                

                //get all missing packages, the fist one we have, so start with 1
                for ($i = 1; $i < $panum; $i++) 
                {
                    //get next package
                    $buffer2 = fread($this->socket, 1);
                    $status = socket_get_status($this->socket);
                    $buffer2 .= fread($this->socket, $status['unread_bytes']);

                    
                    //get number from current package
                    $requestid2 = substr($buffer, 4, 4);
                    $po = ord(substr($buffer2, 8, 1));
                    $po = $po >> 4;
                    $pacur = ($po & 1) + ($po & 2) + ($po & 4) + ($po & 8);


                    //check the requestid from every package and add to array
                    if($requestid == $requestid2) 
                    {
                        $splitbuffer[$pacur] = ($pacur == ($panum - 1)) ? substr($buffer2, 9) : substr($buffer2, 14);
                    }
                }
                
                //add to main packet, the array is ordered by package num
                for($i = 0; $i < $panum; $i++) $bufferret .= $splitbuffer[$i];
            }

            //In case there is only one package
            else
                $bufferret = substr($buffer, 4);

            //return complete package including the type byte
            return $bufferret;
        }
    }

    
}
