<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Debug_lib {

    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function test_mysql_connection($dbhost, $dbuser, $dbpass, $dbname)
    {
        if ( ! $link = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) 
        {
            throw new Exception(mysqli_connect_errno() . ' ' . mysqli_connect_error());
            return FALSE;
        }
        
        else
        {
            return TRUE;
        }
    }
    
    
    public function test_mssql_connection($dbhost, $dbuser, $dbpass, $dbname)
    {
        if ( ! $link = @mssql_connect($dbhost, $dbuser, $dbpass)) 
        {
            throw new Exception('Nav iespejams pieslēgties izmantojot šos uzstādījumus. (MSSQL driver)');
            return FALSE;
        }
        
        elseif( ! mssql_select_db($dbname, $link))
        {
            throw new Exception('Pārbaudiet vai lietotājam ir tiesības izmantot šo datubāzi. (MSSQL driver)');
            return FALSE;
        }
        
    }
    
    
    public function test_sqlsrv_connection($dbhost, $dbuser, $dbpass, $dbname)
    {
        $serverName = $dbhost; //serverName\instanceName
        $connectionInfo = array('Database' => $dbname, 'UID' => $dbuser, 'PWD' => $dbpass);
        
        if(function_exists('sqlsrv_connect'))
        {
            if ( ! $conn = @sqlsrv_connect( $serverName, $connectionInfo)) 
            {
                throw new Exception('Nav iespējams pieslēgties ar dotajiem uzstādījumiem! (SQLsrv driver)');
                return FALSE;
            }
        }
        else
        {
            throw new Exception('sqlsrv draiveris nav uzinstalēts kopā ar PHP instalāciju!');
            return FALSE;
        }
        
    }
    
}