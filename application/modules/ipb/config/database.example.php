<?php
/**
 * Ipb database.php configuration file.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Uzstādījumi, kurus mainīt nav nepieciešams
 */
$config['ipb']['dbdriver'] = 'mysqli';
$config['ipb']['pconnect'] = FALSE;
$config['ipb']['db_debug'] = TRUE;
$config['ipb']['cache_on'] = FALSE;
$config['ipb']['cachedir'] = '';
$config['ipb']['char_set'] = 'utf8';
$config['ipb']['dbcollat'] = 'utf8_general_ci';