<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Airtel sms shop ( v2 )</title>
    
    <!-- Custom header font -->
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=PT+Sans&subset=latin,cyrillic" />
    
     <!-- Loading css styles -->
    <style type="text/css">
        
        /* Boostrap */
        @import url("<?php echo base_url(); ?>bootstrap/css/bootstrap.min.css");
        /* Awesome */
        @import url("<?php echo base_url(); ?>awesome/css/font-awesome.css");
        /* Side accordion */
        @import url("<?php echo base_url(); ?>css/accordion.css");
        /* Breadcrumb */
        @import url("<?php echo base_url(); ?>css/breadcrumb.css");
        /* Chosen */
        @import url("<?php echo base_url(); ?>lib/chosen/chosen.css");
        /* Datatables */
        @import url("<?php echo base_url(); ?>css/datatables.css");
        /* Step wizard */
        @import url("<?php echo base_url(); ?>lib/wizard/jquery.form.wizard.css");
        
        /* Base style */
        @import url("<?php echo base_url(); ?>css/base.css");
        
        /* Default theme */
        @import url("<?php echo base_url(); ?>css/themes/theme-<?php echo $this->config->item('shop_theme'); ?>.css");
    </style>
    
    <script>
        document.documentElement.className += 'js';
    </script>
    
</head>
<body>
    
    <?php 
    if( ! $this->config->item('iframe_mode')): 
        ?>
        <br /><br /><br />
        <?php 
    endif; 
    ?>
    
    <!-- Basic container -->
    <div class="container" id="container">
        
        <!-- Basic wrapper -->
        <div class="wrapper">
        
            <div id="header">
                <?php echo $this->config->item('shop_header'); ?>
            </div>
            
            <!-- Content holder row -->
            <div class="row">
                
                <!-- Side navigation holder -->
                <div class="span4 sidebar">
                    
                    <div id="side_accordion" class="accordion">
                        
                        <?php
                        
                        //$i = 1;
                        //$k = 1;
                        
                        // For each loaded module show row
                        foreach($loaded_modules as $module => $name): 
                        
                            $this->load->config($module.'/master.php');
                            $services = $this->config->item($module.'_services');
                            ?>
                        
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a href="#collapse_<?php echo $module; ?>" data-parent="#side_accordion" data-toggle="collapse" class="accordion-toggle">
                                        <?php echo $name['title']; ?>
                                    </a>
                                </div>
                                <div class="accordion-body collapse <?php echo ($module == $this->module->active_module) ? 'in' : ''; ?>" id="collapse_<?php echo $module; ?>">
                                    <div class="accordion-inner">
                                        <ul class="nav nav-list">
                                            
                                            <?php 
                                            if(isset($name['login']) && $name['login'] === TRUE): 
                                                ?>
                                                <li class="nav-header">Profils</li>
                                                <li class="<?php echo ($this->uri->segment(2) == 'login' && $module == $this->module->active_module) ? 'active' : ''; ?>"><?php echo anchor($module.'/login', 'Apskatīt'); ?></li>
                                                <?php
                                            endif; 
                                            ?>
                                            
                                            
                                            <li class="nav-header">Pakalpojumi</li>
                                            
                                            <?php
                                            // For each loaded module service show service anchor
                                            foreach($services as $service => $item):
                                                
                                                $style = '';
                                                $state = ($service == $this->module->active_service && $module == $this->module->active_module) ? 'selected' : '';
                                            
                                                if($module == $this->module->active_module):
                                                    
                                                    if(isset($name['login']) && $name['login'] === TRUE):
                                                        
                                                        if(class_exists($this->module->active_module.'_lib'))
                                                        {
                                                            if($this->{$this->module->active_module.'_lib'}->check_login(TRUE) == TRUE)
                                                            {
                                                                $settings = $this->config->item('services_settings');

                                                                if($settings[$service]['login_required'] == TRUE && $state != 'selected')
                                                                {
                                                                    $style = 'style="color: #888;"';
                                                                }
                                                            }
                                                        }
                                        
                                                    endif;
                                                    
                                                endif;
                                                
                                                
                                                ?>
                                                <li class="<?php echo ($service == $this->module->active_service && $module == $this->module->active_module) ? 'active' : ''; ?>"><a <?php echo $style; ?> href="<?php echo site_url($module . '/index/' . $service); ?>"><?php echo $item['title']; ?></a></li>
                                            
                                                
                                                <?php
                                                // If foreach service is equal with active service and foreach module is equal with active module
                                                if($service == $this->module->active_service && $module == $this->module->active_module):
                                                    
                                                    // If service has toplist let's show toplist anchor
                                                    if(isset($this->module->services[$this->module->active_service]['toplist']) && $this->module->services[$this->module->active_service]['toplist'] == TRUE):
                                                        
                                                        ?>
                                                        <li class="top-li top-list-anchor">
                                                            <a href="javascript:void(0);"><i class="icon-bar-chart icon-large"></i><?php echo $this->config->item($this->module->active_service, 'submenu_names'); ?></a>
                                                        </li>
                                                        <?php
                                                        
                                                    endif;
                                                    
                                                endif;
                                                
                                            endforeach;
                                            ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        
                            <?php
                            
                        endforeach;
                        
                        ?>
                    </div>
                    
                </div> <!-- End Side navigation holder -->


                <!-- Module content holder -->
                <div id="preload_mark" class="span8">
                    
                    <div id="loading_layer" style="display: none;"><img src="<?php echo base_url(); ?>img/ajax_loader.gif" alt="" /></div>
                    
                    <!-- Module wrapper -->
                    <div class="module-wrapper">