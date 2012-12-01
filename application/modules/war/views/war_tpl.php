<?php echo $this->ui->breadcrumb(); ?>
<div id="content-wrapper">

    <?php if($this->uri->segment(4) == 'error'):
    
        $output = $this->ui->system_messages();
        
        if(! empty($output))
        {
            echo $output;
        }
        else
        {
            redirect($this->module->active_module.'/index/'.$this->module->active_service);
        }
    
    else: ?>
    
        <?php $this->load->view($this->module->active_module.'/descriptions/dsc_'.$this->module->active_service); ?>

        <?php echo $this->ui->system_messages(); ?>

        <?php $this->load->view('core/payment_module'); ?>
    
    <?php endif; ?>

</div>


<?php if($this->uri->segment(4) != 'error'): ?>

    <!-- Player toplist -->
    <div id="content-toplist" class="hide">

        <h4 class="heading"><?php echo $this->config->item($this->module->active_service, 'submenu_names'); ?></h4>

        <?php 

        // If there is param like toplist and that param is set to TRUE
        if(isset($this->module->services[$this->module->active_service]['toplist']) && $this->module->services[$this->module->active_service]['toplist'] == TRUE):


            if($this->module->active_service == 'zmammo')
            {
                $data['players'] = $this->war_model->{'toplist_'.$this->module->active_service}($this->module->services[$this->module->active_service]['db_array_name']);
                $this->load->view('top/top_'.$this->module->active_service, $data);
            }
            elseif(substr($this->module->active_service, 0, 3) == 'exp')
            {
                $data['players'] = $this->war_model->toplist_exp($this->module->services[$this->module->active_service]['db_array_name']);
                $this->load->view('top/top_exp', $data);
            }

        endif;

        ?>

    </div>
    
<?php endif; ?>    