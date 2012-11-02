<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Smscode extends MX_Controller {

    
    public $uid = 0;
    
    public $passkey = '';
    
    public $url = '';
    
    
    function __construct()
    {
        parent::__construct();

        $this->load->config('core/master');
        $this->load->config('core/system');
        
        $this->uid = $this->config->item('user_id');
        $this->passkey = $this->config->item('passkey');
        $this->url = $this->config->item('api_url');
    }    
    

    /**
     * Veic koda izlietošanu, ja ir zināma tā vertība
     * 
     * @param string $code - 8 ciparu kods no sms
     * @param int $price - Koda vērtiba
     * @param boolean $test - Veikt tikai testu, bez koda izlietošanas
     * @return string - Atbilde teksta veidā
     * 
     */
    public function code_response($code, $price, $test = FALSE)
    {
        $test_code = ($test) ? 'yes' : 'no';
        
        $data = array(
            'code' => $code,
            'price' => $price,
            'test' => $test_code,
            'id' => $this->uid
        );  
        
        $response = $this->_send($data, 'charger', 'validate');
        
        return $response;
    }
    
    
    /**
     * Veic koda izlietošanu nezinot tā vērtību
     * 
     * @param string $code - 8 ciparu kods no sms
     * @param boolean $test - Veikt tikai testu, bez koda izlietošanas
     * @return array - Atbilde masīvs ar 3 ierakstiem:
     * 
     * price - 8 ciparu koda vertība
     * country - ISO 3166-1 alpha-2 standarts, 2 zīmju valsts nosaukums
     * answer - Atbilde no servera
     */
    public function code_response_wsearch($code, $test = FALSE)
    {
        $test_code = ($test) ? 'yes' : 'no';

        $data = array(
            'code' => $code,
            'findkey' => 'yes',
            'test' => $test_code,
            'id' => $this->uid
        );        

        $response = $this->_send($data, 'charger', 'validate');

        $temp = explode(':', $response);
        
        if(count($temp) == 3) {
            
            $result = array (
                'price' => $temp[0],
                'country' => $temp[1],
                'answer' => $temp[2]
            );
        }
        else
        {
            $result = array (
                'price' => 0,
                'country' => 0,
                'answer' => $response
            );
        }

        return $result;
    }
    
    
    /**
     * Funckija veic pieprasījumu uz API serveri, nododot datus un saņemot atbildi
     * allow_url_fopen = Off gadījumā tiek vekts pieprasījums izmanojot CURL
     * 
     * @param array $data - Masīvs ar padodamajiem datiem apstrādei
     * @param string $request - Api faila nosaukums, no kura tiek pieprsasīta atbilde
     * @param string $action - Ko mēs vēlamies veikt
     * @return string - Atgriež servera atbildi
     */
    private function _send($data, $request, $action)
    {
        $data['action'] = $action;
        $url = $this->url . $request . '.php?';
        $requrl = $url . http_build_query($data);
        
        
        if (ini_get('allow_url_fopen') == 1)
        {
            $response = file_get_contents($requrl, FALSE, NULL, 0);
        }
        else
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requrl);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'airtel.ci.class.v.1.0.0');
            $response = curl_exec ($ch);
            if (curl_error($ch) == '') {
                return($response);
            }
            else
            {
                return 'fatal_cant_request_remote_server';
            }
        }
        

        return $response;
    }
    
    
    /**
     * Pieprasa no API servera masīvu ar sms vērtībam
     * 
     * @param type $country - ISO 3166-1 alpha-2 standarts, 2 zīmju valsts nosaukums, piemēram "lv"
     * @return array - Masīvs ar sms vērtībam pieprasītajā valstī
     */
    public function get_prices($country)
    {
        $this->load->driver('cache');
        $result = $this->cache->file->get('prices_'.$country);
        
        if($result === FALSE)
        {
            log_message('debug', 'Saving prices array to cache!');
            
            $data = array('country' => $country);
            $response = $this->_send($data, 'api', 'getprices');
            $result = json_decode($response, TRUE);
            
            $this->cache->file->save('prices_'.$country, $result, 600);
        }
        
        return $result;

    }
    
    
}