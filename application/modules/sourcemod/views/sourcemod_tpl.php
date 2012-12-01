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


            if($this->module->active_service == 'unban')
            {
                $data['players'] = $this->sourcemod_model->{'toplist_'.$this->module->active_service}();
                $this->load->view('top/top_'.$this->module->active_service, $data);
            }
            else
            {
                $data['players'] = $this->sourcemod_model->toplist_sourcemod($this->module->active_service);
                $data['servers'] = $this->servers;
                $this->load->view('top/top_sourcemod', $data);
            }

        endif;

        ?>

    </div>
    
<?php endif; ?>


<?php if($this->settings[$this->module->active_service]['type'] == 'subscription'): ?>

    <div class="modal hide" id="modaldetails" tabindex="-1" role="dialog" aria-labelledby="modaldetailsLabel" aria-hidden="true">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="modaldetailsLabel">Sourcemod Detalizēts apraksts</h3>
        </div>

        <div class="modal-body">

            <p>Šīm pakalpojumam rakstūrīgo flagu apraksts, noteikumi, u.c. informācija:</p>

            <?php echo $this->ui->show_access_flags(); ?>

            <p><strong>Kā lietot:</strong></p>

            <p>Pēc pakalpojuma pasūtīšanas atvērt spēlē konsoli un ierakstīt setinfo _pw 'Jūsu_parole'</p>

            <p><strong>Cits info:</strong></p>
            <p>Atceries, ka Tev ir jārada piemērs citiem!
            Noteikumi neievērošanas vai pārkāpšanas gadījumā ACC var tikt atņemts bez brīdinājuma!</p>


        </div>

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Aizvērt</button>
        </div>

    </div>

<?php endif; ?>