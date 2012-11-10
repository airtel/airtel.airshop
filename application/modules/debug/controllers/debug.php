<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Debug extends MX_Controller {
    
    
    /**
     * Constructor
     */
    public function __construct()        
    {
        parent::__construct();
        
        // Init library
        $this->load->library('core/module');
        
        // Module specific variables and functions initialization and execution
        $this->module->module_init();

        // Private actions initialization
        $this->individual_init();
    }
    
    
    private function individual_init()
    {
        // Load debug library
        $this->load->library('debug/debug_lib');
    }
    
    
    public function index()
    {   
        $data['report'] = array();
        
        // param
        // type [warning|info|error]
        // message
        
        // General error checking
        if($this->module->active_service == 'debug')
        {
            
            if($this->config->item('user_id') == '1')
            {
                $data['report'][] = array(
                    'param' => 'user_id',
                    'type' => 'important',
                    'message' => 'Ievadīts nepareizs airtel sms lietotāja id'
                );
            }
            else 
            {
                $data['report'][] = array(
                    'param' => 'user_id',
                    'type' => 'info',
                    'message' => 'airtel sms lietotaja id ir: '.$this->config->item('user_id'),
                );
            }
            
            
            if($this->config->item('passkey') == '')
            {
                $data['report'][] = array(
                    'param' => 'passkey',
                    'type' => 'important',
                    'message' => 'Nav ievadīts airtel sms lietotāja passkey'
                );
            }
            
            if($this->config->item('testing') == TRUE)
            {
                $data['report'][] = array(
                    'param' => 'testing',
                    'type' => 'warning',
                    'message' => 'Konfigurācijas parametrs "testing" ir ieslēgts. Kodi netiks realizēti'
                );
            }
            

            $data['report'][] = array(
                'param' => 'iframe_mode',
                'type' => 'info',
                'message' => 'Iframe mode ir '.$iframe = ($this->config->item('iframe_mode') == TRUE) ? '<strong>Ieslēgts</strong>' : '<strong>Izslēgts</strong>',
            );
            
            
            $data['report'][] = array(
                'param' => 'writable',
                'type' => 'warning',
                'message' => 'Lūdzu parbaudiet lai <strong>"application/cache"</strong> un <strong>"application/logs"</strong> folderi būtu ar writable <strong>( 777 )</strong> tiesībām!',
            );
            
            
        }
        
        // AMX services
        elseif($this->module->active_service == 'amx')
        {
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');

            try 
            {
                $this->debug_lib->test_mysql_connection($this->config->item('hostname', 'amx'), $this->config->item('username', 'amx'), $this->config->item('password', 'amx'), $this->config->item('database', 'amx'));
                
                $data['report'][] = array(
                    'param' => 'AMX db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'AMX db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            
        }
        
        // IPB services
        elseif($this->module->active_service == 'ipb')
        {
            
            $this->load->config($this->module->active_service.'/master');

            
            $config_path = $this->config->item('ipb_board_path') . 'conf_global.php';
            
            if( ! file_exists($config_path))
            {
                $data['report'][] = array(
                    'param' => 'conf_global.php',
                    'type' => 'important',
                    'message' => 'Nevar atrast conf_global.php. Pārbaudiet uzstādījumus!',
                );
            }
            else
            {
                $data['report'][] = array(
                    'param' => 'conf_global.php',
                    'type' => 'info',
                    'message' => 'Fails ir atrasts!',
                );
                
                require_once($config_path);

                try
                {
                    $this->debug_lib->test_mysql_connection($INFO['sql_host'], $INFO['sql_user'], $INFO['sql_pass'], $INFO['sql_database']);
                    
                    $data['report'][] = array(
                        'param' => 'IPB db',
                        'type' => 'info',
                        'message' => 'Konekcija pie datubāzes veiksmīga!',
                    );
                }
                catch(Exception $e)
                {
                    $data['report'][] = array(
                        'param' => 'IPB db',
                        'type' => 'important',
                        'message' => $e->getMessage(),
                    );
                }
                
                $data['report'][] = array(
                    'param' => 'IPB version',
                    'type' => 'info',
                    'message' => 'Atrasta IPB versija ' . $ipb_version = ( ! defined('IN_DEV')) ? '<strong>2.x</strong>' : '<strong>3.x</strong>',
                );
            }
            
        }
        
        // Minecraft services
        elseif($this->module->active_service == 'minecraft')
        {
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            $this->load->config($this->module->active_service.'/system');
            
            $minecraft_services = $this->config->item('minecraft_services');
            
            /**
             *  Database and tables checking
             */
            try 
            {
                $this->debug_lib->test_mysql_connection($this->config->item('hostname', 'minecraft'), $this->config->item('username', 'minecraft'), $this->config->item('password', 'minecraft'), $this->config->item('database', 'minecraft'));
                
                $data['report'][] = array(
                    'param' => 'Minecraft db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
                $sql_params = $this->config->item('sql_params');
                unset($sql_params['auth_params']);
                
                $check = array('unban', 'amnesty', 'credits');
                
                $database = $this->config->item('minecraft');
                
                
                $this->load->model('debug/debug_model');
                
                
                foreach($check as $service)
                {
                    if($minecraft_services[$service]['functionality'] == 'both')
                    {
                        if( ! $this->debug_model->check_table($database, $sql_params[$service.'_params']['table_name']))
                        {
                            $data['report'][] = array(
                                'param' => 'Minecraft table',
                                'type' => 'important',
                                'message' => 'SQL tabula: <strong>"'.$sql_params[$service.'_params']['table_name'].'"</strong> neeksistē!',
                            );
                        }
                        else
                        {
                            $data['report'][] = array(
                                'param' => 'Minecraft table',
                                'type' => 'info',
                                'message' => 'SQL tabula <strong>"'.$sql_params[$service.'_params']['table_name'].'"</strong> atrasta!',
                            );
                        }
                    }
                }
                
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'Minecraft db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            

            /**
             *  Rcon and Query connection
             */
            
            // Load connection settings from config
            $server_settings = $this->config->item('minecraft_server_settings');
            $server_settings['throw_errors'] = FALSE;
            $server_settings['socket_timeout'] = 1;
            
            // Load minecraft rcon library
            $this->load->library('core/rcon_minecraft', $server_settings);
            
            if($this->rcon_minecraft->connected == FALSE)
            {
                $data['report'][] = array(
                    'param' => 'Minecraft Rcon',
                    'type' => 'important',
                    'message' => 'Rcon klase nevar pieslēgties pie servera: '.$server_settings['server_hostname'].':'.$server_settings['server_port'],
                );
            }
            else 
            {
                $data['report'][] = array(
                    'param' => 'Minecraft Rcon',
                    'type' => 'info',
                    'message' => 'Rcon klase veiksmīgi pieslēdzās pie: '.$server_settings['server_hostname'].':'.$server_settings['server_port'],
                );
            }
            
            // Load minecraft query library
            $this->load->library('core/query_minecraft', $server_settings);
            
            if($this->query_minecraft->send_packet(0x09) === FALSE)
            {
                $data['report'][] = array(
                    'param' => 'Minecraft Query',
                    'type' => 'important',
                    'message' => 'Query klase nevar pieslēgties pie servera: '.$server_settings['server_hostname'].':'.$server_settings['query_port'],
                );
            }
            else
            {
                $data['report'][] = array(
                    'param' => 'Minecraft Query',
                    'type' => 'info',
                    'message' => 'Query klase veiksmīgi pieslēdzās pie: '.$server_settings['server_hostname'].':'.$server_settings['query_port'],
                );
            }
            
            
            /**
             *  Minecraft plugins
             */
            if($this->rcon_minecraft->connected == TRUE)
            {
                $plugins = $this->rcon_minecraft->communicate('plugins', TRUE);
                
                //echo 'plugins:';
                //print_r($plugins);
                //echo PHP_VERSION_ID;
                
                unset($minecraft_services['broadcast']);
                unset($minecraft_services['exp']);
                
                foreach($minecraft_services as $service)
                {
                    if(stristr($plugins, $service['plugin']) === FALSE)
                    {
                        $data['report'][] = array(
                            'param' => 'Plugin',
                            'type' => 'important',
                            'message' => 'Minecraft plugins <strong>"'.$service['plugin'].'"</strong> serverī nav uzinstalēts!',
                        );
                    }
                    else
                    {
                        $data['report'][] = array(
                            'param' => 'Plugin',
                            'type' => 'info',
                            'message' => 'Minecraft plugins <strong>"'.$service['plugin'].'"</strong> serverī ir uzinstalēts!',
                        );
                    }
                }
            }
            
        }
        
        // muOnline services
        elseif($this->module->active_service == 'muonline')
        {
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            
            $mu_services = $this->config->item('muonline_services');
            
            if($this->config->item('dbdriver', 'muonline') == 'mssql')
            {
                $data['report'][] = array(
                    'param' => 'muOnline db driver',
                    'type' => 'warning',
                    'message' => 'Konekcijai tiek izmantos <strong>"mssql"</strong> draiveris. Windows PHP 5.3 instalācija nesatur šādu draiveri! Lūdzu izmantot <strong>"sqlsrv"</strong> draiveri',
                );
            }
            else
            {
                $data['report'][] = array(
                    'param' => 'muOnline db driver',
                    'type' => 'warning',
                    'message' => 'Konekcijai tiek izmantos <strong>"sqlsrv"</strong> draiveris. Šis draiveris pieejams tikai uz Windows O/S',
                );
            }

            // Check connection
            try 
            {
                $this->debug_lib->{'test_'.$this->config->item('dbdriver', 'muonline').'_connection'}($this->config->item('hostname', 'muonline'), $this->config->item('username', 'muonline'), $this->config->item('password', 'muonline'), $this->config->item('database', 'muonline'));
                
                $data['report'][] = array(
                    'param' => 'muOnline db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'muOnline db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            
            if(array_key_exists('skillbooster', $mu_services))
            {
                $data['report'][] = array(
                    'param' => 'skillbooster',
                    'type' => 'warning',
                    'message' => 'Lūdzu parbaudiet <strong>"max_skill"</strong>. Tai var būt divas vērtības 32000 vai 65000, atkarība no Jūsu muonline servera spefifikācijas. Nepareizs uzstādījums var novest pie skill zaudēšanas!',
                );
            }
            
        }        
        
        // Sourcemod services
        elseif($this->module->active_service == 'sourcemod')
        {
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            
            try 
            {
                $this->debug_lib->test_mysql_connection($this->config->item('hostname', 'sourcemod'), $this->config->item('username', 'sourcemod'), $this->config->item('password', 'sourcemod'), $this->config->item('database', 'sourcemod'));
                
                $data['report'][] = array(
                    'param' => 'Sourcemod db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'Sourcemod db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            
        }
        
        // War3 services
        elseif($this->module->active_service == 'war')
        {
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            
            $war_services = $this->config->item('war_services');
            unset($war_services['zmammo']);
            
            foreach($war_services as $service)
            {
                $dbname = $service['db_array_name'];
                
                try 
                {
                    $this->debug_lib->test_mysql_connection($this->config->item('hostname', $dbname), $this->config->item('username', $dbname), $this->config->item('password', $dbname), $this->config->item('database', $dbname));

                    $data['report'][] = array(
                        'param' => $dbname.' db',
                        'type' => 'info',
                        'message' => 'Konekcija pie datubāzes veiksmīga!',
                    );

                }
                catch(Exception $e) 
                {
                    $data['report'][] = array(
                        'param' => $dbname.' db',
                        'type' => 'important',
                        'message' => $e->getMessage(),
                    );
                }
                
            }
            
        }        
        
        // Project general services ( donation, adverts )
        elseif($this->module->active_service == 'general')
        {
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            
            try 
            {
                $this->debug_lib->test_mysql_connection($this->config->item('hostname', 'general'), $this->config->item('username', 'general'), $this->config->item('password', 'general'), $this->config->item('database', 'general'));
                
                $data['report'][] = array(
                    'param' => 'General db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'General db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            
        }
        
        // CWservers services
        elseif($this->module->active_service == 'cwservers')
        {      
            
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            
            
            try 
            {
                $this->debug_lib->test_mysql_connection($this->config->item('hostname', 'cwservers'), $this->config->item('username', 'cwservers'), $this->config->item('password', 'cwservers'), $this->config->item('database', 'cwservers'));
                
                $data['report'][] = array(
                    'param' => 'Cwservers db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'Cwservers db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            

            
            
            if( ! function_exists('ssh2_exec'))
            {
                $data['report'][] = array(
                    'param' => 'pecl SSH2',
                    'type' => 'important',
                    'message' => 'Nav uzinstalēts SSH2 php extension. Moduļa darbība nav iespējama.'
                );
            }
            else 
            {
                $data['report'][] = array(
                    'param' => 'pecl SSH2',
                    'type' => 'info',
                    'message' => 'SSH2 php extension ir uzinstalēts.',
                );

                // Load lib
                $this->load->library('cwservers/cw');
                
                // Continue checking only if extension is installed
                foreach($this->config->item('cwservers_services') as $service => $item)
                {
                    $server_settings = $item['ssh'];
                    $server_settings['throw_errors'] = FALSE;
                    
                    
                    $this->load->library('core/ssh', $server_settings);
                    
                    if($this->ssh->_is_conn() == FALSE)
                    {
                        $data['report'][] = array(
                            'param' => $service. ' SSH',
                            'type' => 'important',
                            'message' => 'Nevar pieslēgties ar dotajiem SSH uzstādījumiem: '.$server_settings['hostname'].':'.$server_settings['port'],
                        );
                    }
                    else 
                    {
                        $data['report'][] = array(
                            'param' => $service. ' SSH',
                            'type' => 'info',
                            'message' => 'SSH klase veiksmīgi pieslēdzās pie: '.$server_settings['hostname'].':'.$server_settings['port'],
                        );
                    }
                    
                    if( ! $this->cw->ping_host($server_settings, FALSE))
                    {
                        $data['report'][] = array(
                            'param' => $service. ' Ping',
                            'type' => 'error',
                            'message' => 'Ping: nav iespējams atvērt socketu ar dotajiem uzstādījumiem.',
                        );
                    }
                    else
                    {
                        $data['report'][] = array(
                            'param' => $service. ' Ping',
                            'type' => 'info',
                            'message' => 'Ping: servera pārbaude notika veiksmīgi.',
                        );
                    }
                    
                    // Destrosy ssh connection
                    //$this->ssh->__destruct();
                    //unset($server_settings);
                }
                
                
                if($this->config->item('passkey') != '')
                {
                    $data['report'][] = array(
                        'param' => 'Crontab link',
                        'type' => 'info',
                        'message' => 'Crontab Cwservers check link: <strong>'.site_url('cwservers/check_status/'.$this->config->item('passkey')).'</strong>',
                    );
                }
                else
                {
                    $data['report'][] = array(
                        'param' => 'Crontab link',
                        'type' => 'error',
                        'message' => 'Nav uzlikts lietotāja passkey. Cwserveru pārbaude nav iepsējama!!!',
                    );
                }

                
            }
        }
        
        // World of warcraft services
        elseif($this->module->active_service == 'wow')
        {         
        
            $this->load->config($this->module->active_service.'/master');
            $this->load->config($this->module->active_service.'/database');
            
            try 
            {
                $this->debug_lib->test_mysql_connection($this->config->item('hostname', 'wow'), $this->config->item('username', 'wow'), $this->config->item('password', 'wow'), $this->config->item('database', 'wow'));
                
                $data['report'][] = array(
                    'param' => 'Wow db',
                    'type' => 'info',
                    'message' => 'Konekcija pie datubāzes veiksmīga!',
                );
                
            }
            catch(Exception $e) 
            {
                $data['report'][] = array(
                    'param' => 'Wow db',
                    'type' => 'important',
                    'message' => $e->getMessage(),
                );
            }
            
        }
        
    
        /**
         * Load HTML
         */
        $this->base->load_header($this->module->active_module);
        $this->load->view($this->module->active_module.'_tpl', $data);
        $this->base->load_footer();
    }
    
    
}