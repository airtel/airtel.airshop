<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Paypalcode extends MX_Controller {
    
    
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
    
    
    public function code_response_wsearch($code, $test = FALSE)
    {
        $test_code = ($test) ? 'yes' : 'no';

        $data = array(
            'code' => $code,
            'findkey' => 'yes',
            'test' => $test_code,
            'id' => $this->uid
        );        

        $response = $this->_send($data, 'paypalcharger', 'validate');

        $temp = explode(':', $response);
        
        if(count($temp) == 3) {
            
            $result = array (
                'price' => $temp[0],
                'currency' => $temp[1],
                'answer' => $temp[2]
            );
        }
        else
        {
            $result = array (
                'price' => 0,
                'currency' => 0,
                'answer' => $response
            );
        }

        return $result;
    }
    
    
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
            curl_setopt($ch, CURLOPT_USERAGENT, 'airtel.ci.class.v.1.1.0');
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
       
    
}