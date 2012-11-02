<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Get superclass pointer
$CI =& get_instance();


define('ipbwi_BOARD_PATH', $CI->config->item('ipb_board_path'));


$forum_path = $this->config->item('ipb_board_path');
$config_path = $forum_path . 'initdata.php';
require_once($config_path);
define('ipbwi_BOARD_ADMIN_PATH', $CI->config->item('ipb_board_path') . CP_DIRECTORY . '/');


//define('ipbwi_BOARD_ADMIN_PATH', $CI->config->item('ipb_board_admin_path'));


define('ipbwi_ROOT_PATH', APPPATH . 'helpers/ipbwi/');


function do_ipb_login($username, $password, $cookie = TRUE, $anon = FALSE)
{
    require_once('ipbwi/ipbwi.inc.php');

    $ipbwi->member->login($username, $password, $cookie, $anon);
    
    $response = $ipbwi->printSystemMessages(FALSE, TRUE);
    
    return $response;
}


function do_ipb_logout()
{
    require_once('ipbwi/ipbwi.inc.php');
    
    $ipbwi->member->logout();
}