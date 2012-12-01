<?php
/**
 * War3 database.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Database settings for war3 standart server
 */
$config['war3']['hostname'] = 'localhost';
$config['war3']['username'] = 'username';
$config['war3']['password'] = 'password';
$config['war3']['database'] = 'database';
$config['war3']['dbdriver'] = 'mysqli';
$config['war3']['dbprefix'] = 'wc3_';
$config['war3']['db_debug'] = TRUE;
$config['war3']['char_set'] = 'utf8';


/**
 * Database settings for war3 / surf mod server
 */
$config['surf']['hostname'] = 'localhost';
$config['surf']['username'] = 'username';
$config['surf']['password'] = 'password';
$config['surf']['database'] = 'database';
$config['surf']['dbdriver'] = 'mysqli';
$config['surf']['dbprefix'] = 'wc3_';
$config['surf']['db_debug'] = TRUE;
$config['surf']['char_set'] = 'utf8';


/**
 * Database settings for war3 / zm mod server
 */
$config['zm']['hostname'] = 'localhost';
$config['zm']['username'] = 'username';
$config['zm']['password'] = 'password';
$config['zm']['database'] = 'database';
$config['zm']['dbdriver'] = 'mysqli';
$config['zm']['dbprefix'] = 'wc3_';
$config['zm']['db_debug'] = TRUE;
$config['zm']['char_set'] = 'utf8';


/**
 * Database settings for war3 / halo mod server
 */
/*$config['halo']['hostname'] = 'localhost';
$config['halo']['username'] = 'username';
$config['halo']['password'] = 'password';
$config['halo']['database'] = 'database';
$config['halo']['dbdriver'] = 'mysqli';
$config['halo']['dbprefix'] = 'wc3_';
$config['halo']['db_debug'] = TRUE;
$config['halo']['char_set'] = 'utf8';*/