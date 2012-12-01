<?php echo $this->ui->breadcrumb(); ?>
<div id="content-wrapper" style="min-height: 520px">

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


            if($this->module->active_service == 'amnesty' && $this->module->services['amnesty']['functionality'] == 'rcon')
            {
                // Get from rcon query
                $data['players'] = $this->minecraft_lib->toplist_rcon_amnesty();
            }
            elseif($this->module->active_service == 'credits' && $this->module->services['credits']['functionality'] == 'rcon')
            {
                // Get from rcon query
                $data['players'] = $this->minecraft_lib->toplist_rcon_credits();
            }
            else
            {
                // Get players from sql database
                $data['players'] = $this->minecraft_model->{'toplist_'.$this->module->active_service}();
            }

            if($data['players'] == FALSE)
                $data['players'] = array();

            // Load toplist view
            $this->load->view('top/top_'.$this->module->active_service, $data);

        endif;

        ?>

    </div>
    
<?php endif; ?>


<?php if($this->module->active_service == 'groups'): ?>

    <div class="modal hide" id="modaldetails" tabindex="-1" role="dialog" aria-labelledby="modaldetailsLabel" aria-hidden="true">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="modaldetailsLabel">Minecraft grupu detalizēts apraksts</h3>
        </div>

        <div class="modal-body">

            <p>Šīm pakalpojumam rakstūrīgo flagu apraksts, noteikumi, u.c. informācija:</p>

        </div>

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Aizvērt</button>
        </div>

    </div>

<?php endif; ?>