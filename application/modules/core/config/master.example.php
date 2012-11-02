<?php
/**
 * Core master.php konfigurācijas fails.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * Jūsu ID no statistikas sistēmas, var redzēt statistikas augšdaļā, vai Profila
 * sadaļā
 */
$config['user_id'] = '1';


/**
 * Jūsu passkey no statistikas sistemas, var atrast Profila sadaļā
 */
$config['passkey'] = '';


/**
 * Jūsu atslēgas vārds, ja neesat pasūtījuši atsevišķu, tad šim jāpaliek tādam
 * kāds tas ir. 
 */
$config['base_keyword'] = 'ART';


/**
 * Īsais numurs uz kuru sūtam SMS.
 * ART atslēgas vārdam tas ir 159
 */
$config['short_number'] = '1800';


/**
 * Norādiet moduļus, kurus vēlaties pielādēt.
 * 
 * Debug modulis:
 * Pēc noklusējuma jāizslēdz ( ieliek // pirms moduļa ) arā kad veikals atrodas produkcijas stadijā
 * Ar ieslēgtu debug parametru tiek parādīta sadaļa ar nosaukumu Kļūdu pārbaude, kuru atverot notiek visu 
 * pieslēgto moduļu diagnostika, kā arī galveno konfigurācijas failu pārbaude uz pareizu aizpildījumu.
 *
 */
$config['base_modules'] = array (
    
    /**
     * Login pieejams pašlaik tikai diviem moduļiem.
     * ipb un muonline.
     */
    
    'amx' => array(
        'title' => 'AMX pakalpojumi',
        'login' => NULL,
    ),
    
    'ipb' => array(
        'title' => 'IPB foruma pakalpojumi',
        'login' => TRUE,
    ),
    
    'minecraft' => array(
        'title' => 'Minecraft',
        'login' => FALSE,
    ),
    
    'muonline' => array(
        'title' => 'muOnline',
        'login' => TRUE,
    ),
    
    'sourcemod' => array(
        'title' => 'Sourcemod',
        'login' => NULL,
    ),
    
    'war' => array(
        'title' => 'War3 serveru pakalpojumi',
        'login' => NULL,
    ),
    
    'general' => array(
        'title' => 'Projekta pakalpojumi',
        'login' => NULL,
    ),
    
    'cwservers' => array(
        'title' => 'CW serveri',
        'login' => NULL,
    ),
    
    'wow' => array(
        'title' => 'World of warcraft',
        'login' => NULL,
    ),
    
    'debug' => array(
        'title' => 'Kļūdu pārbaude',
        'login' => NULL,
    ),
    
);


/**
 * Shop nosaukums kas tiek parādīts veikala augšējā daļā
 */
$config['shop_header'] = 'SMS Shop <span class="small">v2</span>';


/**
 * Shop krāsu tēma:
 * Pieejamie varianti:
 * 
 * blue
 * black
 * green
 * tamarillo
 * 
 * Default: blue
 * 
 */
$config['shop_theme'] = 'blue';


/**
 * Jābut FALSE ja vēlaties pelnīt naudu. Citādak kodi netiek realizēti, bet tikai pārbaudīti.
 * Atbilde tiek saņemta nevis code_charged_ok bet code_test_ok.
 * Varam slēgt iekšā ja vēlamies tikai pārbaudīt skripta darbību.
 */
$config['testing'] = FALSE;


/**
 * Aplikācijai ir iepsēja ieslēgt iframe mode, tas nozīmē, ka veikals tiks pieladēts bez lieka koda.
 * Nebūs header, footer atstarpju, kā arī shop tiks maksimāli nobīdīts pie augšējā kreisā stūra.
 * Tiks pieslēgts jQuery kods, kurš atbild par atsaucīgu veikala reaģēšanu uz augstuma izmaiņām.
 */
$config['iframe_mode'] = FALSE;