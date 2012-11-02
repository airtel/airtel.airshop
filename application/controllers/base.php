<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Base extends MX_Controller {
    
    
    function __construct() 
    {
        parent::__construct();

        $this->load->config('core/master');
    }
    
    
    public function load_header($tab_name)
    {
        $data['loaded_modules'] = $this->config->item('base_modules');
        $data['tab_name'] = $tab_name;
        
        $this->load->view('includes/header_tpl', $data);

        //$this->load->view('includes/upper_head_tpl');
        //$this->load->view('includes/navigation_tpl', $data);
        //$this->load->view('includes/container_open_tpl');
    }
    

    public function load_footer()
    {
        //$this->load->view('includes/container_close_tpl');
        $this->load->view('includes/footer_tpl');
    }
    
}