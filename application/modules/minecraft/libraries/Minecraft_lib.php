<?php

if ( ! defined('BASEPATH')) 
    exit('No direct script access allowed'); 


class Minecraft_lib
{
    
    
    private $CI;
    
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    
    /**
     * Prepare groups array
     * @param type $groups
     * @return type
     */
    public function prepare_groups($groups)
    {
        $data = array();
        $group_names = $this->CI->module->services['groups']['group_names'];

        foreach($groups as $group => $value)
        {
            if(isset($group_names[$group]) && ! empty($group_names[$group]))
            {
                $data[$group] = $group_names[$group];
            }
            else
            {
                $data[$group] = 'Uzstādiet nosaukumu!';
            }

        }
        
        return $data;
    }
    
    
    public function pex_str_replace($user, $group, $world, $command)
    {
        $replace = array('<user>', '<group>', '[world]');
        $data = array($user, $group, $world);
        
        $result = str_replace($replace, $data, $command);
        
        return $result;
    }
    
    
    public function toplist_rcon_amnesty()
    {
        // Set variables
        $list = array();
        $i = 0;
        
        // Get rcon command
        // get command from config...
        
        // Loading cache driver
        $this->CI->load->driver('cache');
        
        // Get result from cache
        $result = $this->CI->cache->file->get('rcon_amnesty');
        
        
        if($result === FALSE)
        {
            $response = $this->CI->rcon_minecraft->communicate('jailcheck', TRUE);
            
            if(strlen($response) > 0)
            {
                $list = explode(') ', $response);
                
                foreach($list as $l)
                {
                    if($i == 0)
                    {
                        $l = str_replace('Jailed players: ', '', $l);
                    }
                    
                    $players = explode('(', $l);
                    
                    
                    if(! empty($players[1]))
                    {
                        $result[$i]['PlayerName'] = $players[0];

                        if( ! strpos($players[1], 'min'))
                        {
                            $result[$i]['RemainTime'] = 'Permanent';
                        }
                        else
                        {
                            $result[$i]['RemainTime'] = str_replace('min', '', $players[1]);

                        }

                    }
                    $i++;
                }
                
                // Save to cache
                $this->CI->cache->file->save('rcon_amnesty', $result, 300);
            }
        }
        
        
        // Convert array elements to objects
        if(! empty($result))
        {
            // Convert only second dimension arrays to objects.
            foreach($result as $index => $element)
            {
                $result[$index] = (object)$element;
            }
        }

        
        return $result;
    }
    
    
    public function toplist_rcon_credits()
    {
        // Set variables
        $i = 0;
        
        // Get plugin
        $plugin = $this->CI->module->services['credits']['plugin'];
        
        $commands = $this->CI->config->item('plugin_commands');
        
        // Get rcon command
        $command = $commands['credits'][$plugin]['command_top'];
        
        // Loading cache driver
        $this->CI->load->driver('cache');
        
        // Get result from cache
        $list = $this->CI->cache->file->get('rcon_credits');
        
        
        if($list === FALSE)
        {
            $response = $this->CI->rcon_minecraft->communicate($command, TRUE);
            $top = explode("\n", $response);
            $top_last_row = count($top) - 1;
            
            if(count($top) > 0)
            {
                if($plugin == 'essentials')
                {
                    // Remove first, second and third row.
                    // Remove last empty row
                    unset($top[0]);
                    unset($top[1]);
                    unset($top[2]);
                    
                    // Remove last row
                    unset($top[$top_last_row]);

                    // Remove row that displays text: Type /balancetop 2 to read the next page
                    unset($top[$top_last_row - 1]);
                    
                    foreach($top as $t)
                    {
                        $pos = strpos($t, '.');
                        $string = substr($t, $pos+2);
                        $players = explode(',', $string);

                        if(str_replace('$', '', $players[1]) > 0)
                        {
                            $list[$i]['username'] = $players[0];
                            $list[$i]['balance'] = str_replace('$', '', $players[1]);
                        }
                        $i++;
                    }
                }
                elseif($plugin == 'iconomy')
                {
                    // Remove first row
                    // Remove last empty row
                    unset($top[0]);
                    unset($top[$top_last_row]);

                    foreach($top as $t)
                    {
                        $t = html_entity_decode($t);
                        $pos = strpos($t, '&sect;2');
                        $string = substr($t, $pos + strlen('&sect;2'));
                        $array = explode('&sect;8- &sect;f', $string);

                        $list[$i]['username'] = $array[0];
                        $list[$i]['balance'] = str_replace(',', '', trim(str_replace('Dollars', '', $array[1])));                            

                        $i++;
                    }
                }
            }
            
            // Save to cache
            $this->CI->cache->file->save('rcon_credits', $list, 300);            
        }
        
        
        // Convert array elements to objects
        if(! empty($list))
        {
            // Convert only second dimension arrays to objects.
            foreach($list as $index => $element)
            {
                $list[$index] = (object)$element;
            }
        }

        
        return $list;
    }
    
    
    
}