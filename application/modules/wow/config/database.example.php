<?php
/**
 * WOW database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Divi uzstādīšanas varianti:
 * 
 * 1. Visas tabulas ir vienā datubāzē:
 * Tad visos trīs masīvos norādam vienādus uzstādījumus.
 * 
 * 2. Tabulas sadalītas pa datubāzēm.
 * Ievadam uzstādījumus katrā masīvā atsevišķi.
 */


/**
 * Auth database settings
 */
$config['auth']['hostname'] = 'localhost';
$config['auth']['username'] = 'username';
$config['auth']['password'] = 'password';
$config['auth']['database'] = 'art_wow_auth';
$config['auth']['dbdriver'] = 'mysqli';
$config['auth']['dbprefix'] = '';
$config['auth']['db_debug'] = TRUE;
$config['auth']['char_set'] = 'utf8';


/**
 * Characters database settings
 */
$config['chars']['hostname'] = 'localhost';
$config['chars']['username'] = 'username';
$config['chars']['password'] = 'password';
$config['chars']['database'] = 'art_wow_chars';
$config['chars']['dbdriver'] = 'mysqli';
$config['chars']['dbprefix'] = '';
$config['chars']['db_debug'] = TRUE;
$config['chars']['char_set'] = 'utf8';


/**
 * Web database settings
 */
$config['web']['hostname'] = 'localhost';
$config['web']['username'] = 'username';
$config['web']['password'] = 'password';
$config['web']['database'] = 'art_wow_web';
$config['web']['dbdriver'] = 'mysqli';
$config['web']['dbprefix'] = '';
$config['web']['db_debug'] = TRUE;
$config['web']['char_set'] = 'utf8';
