<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Ipb_lib {

    
    private $CI;
    
    private $cookie_name = 'ipb_stronghold';
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    public function check_login($check_only = FALSE)
    {
        // Set redirect var to FALSE by default
        $do_redirect = FALSE;
        
        // Get member id
        $member_id = $this->CI->input->cookie($this->ipb_get_cookie_fullname('member_id'), TRUE);
        
        // Get login key
        $member_log_in_key = $this->CI->ipb_model->get_member_log_in_key($member_id);
        
        // If member id is found
        if($member_id !== FALSE && ctype_digit($member_id) && ! empty($member_id))
        {
            if($this->CI->ipb_model->settings['ipb_version'] == 3)
            {
                if( ! $this->ipb_check_pass_hash($member_log_in_key))
                {
                    $do_redirect = TRUE;
                }
            }
            elseif($this->CI->ipb_model->settings['ipb_version'] == 2)
            {
                if( ! $this->ipbv2_stronghold_check_cookie($member_id, $member_log_in_key))
                {
                    $do_redirect = TRUE;
                }
            }
        }
        
        // Member id not found. Do redirect
        else
        {
            $do_redirect = TRUE;
        }
        
        
        if( ! $check_only)
        {
            if($do_redirect)
            {
                if($this->CI->uri->rsegment(2) != 'login' && $this->CI->uri->rsegment(2) != 'logout' && $this->CI->uri->segment(2) != 'ipb_ajax_call' && $this->CI->uri->rsegment(3) != 'unsuspend')
                {    
                    $this->CI->session->set_userdata('message', 'warning{d}Lai izmantotu šo pakalpojumu ir jāielogojas iekš Web-shop!');
                    redirect('ipb/login');
                }
            }
        }
        else
        {
            return $do_redirect;
        }
    }        
    
    
    /**
     * 
     * @param type $string
     * @param type $separator
     * @return type
     */
    function ipb_convert_to_seoname($string, $separator = '_')
    {
        $string = str_replace(array("ş", "Ş", "Ţ", "ţ", "ă", "î", "â"), array("s", "s", "t", "t", "a", "i", "a"), $string);
        $string = str_replace(array("ā","č", "ē", "ģ", "ķ", "ļ", "ī", "ū", "š", "ņ", "ž"), array("a", "c", "e", "g", "k", "l", "i", "u", "s", "n", "z"), $string);
        $string = str_replace(array("Ё","Ж","Ч","Ш","Щ","Э","Ю","Я","ё","ж","ч","ш","щ","э","ю","я","А","Б","В","Г","Д","Е","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ы","а","б","в","г","д","е","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ь","ы"),array("JO","ZH","CH","SH","SCH","JE","JY","JA","jo","zh","ch","sh","sch","je","jy","ja","A","B","V","G","D","E","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","H","C","Y","a","b","v","g","d","e","z","i","j","k","l","m","n","o","p","r","s","t","u","f","h","c","'","y"), $string);
        $string = strtolower($string);
        $string = trim($string);
        $string = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
        $string = str_replace(" ", $separator, $string);
        return $string;
    }
    
    
    /**
     * 
     * @param type $member_log_in_key
     * @return type
     */
    function ipb_check_pass_hash($member_log_in_key)
    {
        $cookie_pass_hash = $this->CI->input->cookie($this->ipb_get_cookie_fullname('pass_hash'), TRUE);
        
        return ($cookie_pass_hash == $member_log_in_key) ? TRUE : FALSE;
    }
    
    
    /**
     * Function gets full cookie name with prefix for version 3 IPB forums
     * @param string $cookie_name
     * @return string
     */
    function ipb_get_cookie_fullname($cookie_name)
    {
        if($this->CI->ipb_model->settings['ipb_version'] == 3)
        {
            $cookie_prefix = $this->CI->ipb_model->get_cookie_prefix();
            $cookie_fullname = ( ! $cookie_prefix) ? $cookie_name : $cookie_prefix . $cookie_name;
        }
        else
        {
            $cookie_fullname = $cookie_name;
        }
        
        return $cookie_fullname;
    }
    
    
    /**
     * Function checks supplied password with database stored password settings
     * 
     * @param type $db_pass_hash Hash of password stored in ipb v2 database
     * @param type $db_pass_salt Salt of password stored in ipb v2 database
     * @param type $md5_once_password Member suplied password to check
     */
    function ipbv2_authenticate_member($db_pass_hash, $db_pass_salt, $md5_once_password)
    {
        if( ! $db_pass_hash)
        {
            return FALSE;
        }    
        
        if($db_pass_hash == $this->ipbv2_generate_passhash($db_pass_salt, $md5_once_password))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    
    /**
     * Generates a compiled passhash
     * 
     * Returns a new MD5 hash of the supplied salt and MD5 hash of the password
     * 
     * @param string $salt User's salt (5 random chars)
     * @param string $md5_once_password User's MD5 hash of their password
     * @return string MD5 hash of compiled salted password
     */
    function ipbv2_generate_passhash($salt, $md5_once_password)
    {
        return md5(md5($salt) . $md5_once_password);
    }
    
    
    /**
     * Creates an auto-log in strong hold cookie
     * @param type $member_id
     * @param type $member_log_in_key 
     */
    function ipbv2_stronghold_set_cookie($member_id, $member_log_in_key)
    {
        $cookie_expires = time() + 60 * 60 * 24 * 365;
        $ip_octets = explode('.', $this->CI->input->ip_address());
        $crypt_salt = md5($this->CI->ipb_model->settings['password'] . $this->CI->ipb_model->settings['username']);
        
        // Lets put together
        $stronghold = md5(md5($member_id . '-' . $ip_octets[0] . '-' . $ip_octets[1] . '-' . $member_log_in_key) . $crypt_salt);
        
        // Set stronghold cookie
        $data = array(
            'name' => $this->cookie_name,
            'value' => $stronghold,
            'expire' => $cookie_expires,
            'path' => '/',
        );
        
        $this->CI->input->set_cookie($data);
        
        // Set member_id cookie
        $data = array(
            'name' => 'member_id',
            'value' => $member_id,
            'expire' => $cookie_expires,
        );
        
        $this->CI->input->set_cookie($data);
        
        // Set pass_hash cookie
        $data = array(
            'name' => 'pass_hash',
            'value' => $member_log_in_key,
            'expire' => $cookie_expires,
        );
        
        $this->CI->input->set_cookie($data);
    }
    
    
    /**
     * 
     * @param type $member_id
     * @param type $member_log_in_key
     * @return boolean
     */
    public function ipbv2_stronghold_check_cookie($member_id, $member_log_in_key)
    {
        $cookie = $this->CI->input->cookie($this->cookie_name, TRUE);
        $ip_octets = explode('.', $this->CI->input->ip_address());
        $crypt_salt = md5($this->CI->ipb_model->settings['password'] . $this->CI->ipb_model->settings['username']);
        
        // Check if cookie is set
        if( ! $cookie)
        {
            return FALSE;
        }
        
        // Build stronghold
        $stronghold = md5(md5($member_id . '-' . $ip_octets[0] . '-' . $ip_octets[1] . '-' . $member_log_in_key) . $crypt_salt);
     
        
        return ($cookie == $stronghold) ? TRUE : FALSE;
    }
    
    
    /**
     * Destroys cookie settings
     */
    public function ipbv2_logout()
    {
        $this->CI->input->set_cookie(array('name' => 'member_id', 'value' => '0'));
        $this->CI->input->set_cookie(array('name' => 'pass_hash', 'value' => '0'));
        $this->CI->input->set_cookie(array('name' => 'anonlogin', 'value' => '-1'));
    }
    
    
    /**
     * Gets cookie domain
     * @return type
     */
    public function get_domain()
    {
        return preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", "$1", $this->CI->config->slash_item('base_url'));
    }
    
    
}