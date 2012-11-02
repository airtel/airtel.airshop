                        <div class="banner_holder"><a href="http://airtel.lv" target="_blank"><img alt="airtel" src="<?php echo base_url(); ?>img/art.png"></a></div>


                    </div> <!-- End Module wrapper -->
                    
                </div> <!-- End Module content holder -->
                
            </div> <!-- End cotainer holder row -->
            
        </div> <!-- End Basic wrapper -->
            
    </div> <!-- End Basic container -->
    
    <br />
    <br />
    
    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-1.7.2.min.js"></script>

    <!-- Bootstrap -->
    <script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.min.js"></script>
    
    <!-- dataTables -->
    <script src="<?php echo base_url(); ?>lib/datatables/jquery.dataTables.min.js"></script>
    
    <!-- Form validation and Form wizard -->
    <script src="<?php echo base_url(); ?>lib/wizard/jquery.form.js"></script>
    <script src="<?php echo base_url(); ?>lib/validation/jquery.validate.min.js"></script>
    <script src="<?php echo base_url(); ?>lib/wizard/bbq.js"></script>
    <script src="<?php echo base_url(); ?>lib/wizard/jquery.ui-1.8.5.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>lib/wizard/jquery.form.wizard.js"></script>
    
    <!-- Validation localization -->
    <script src="<?php echo base_url(); ?>lib/validation/localization/messages_lv.js"></script>
    
    <!-- Chosen select -->
    <script src="<?php echo base_url(); ?>lib/chosen/chosen.jquery.js"></script>    
    
    <!-- Variables -->
    <script>
        
        // General variables
        var active_module = '<?php echo $this->module->active_module; ?>';
        var active_service = '<?php echo $this->module->active_service; ?>';
        var iframe_mode = <?php echo ($this->config->item('iframe_mode')) ? 'true' : 'false'; ?>;
        var base_url = '<?php echo base_url(); ?>';
        var site_url = '<?php echo site_url(); ?>';

        // Payment variables
        var short_number = '<?php echo $this->config->item('short_number'); ?>';
        var base_keyword = '<?php echo $this->config->item('base_keyword'); ?>';
        var default_prices = <?php echo json_encode($this->module->sms_prices); ?>;

        var pay_methods = <?php echo json_encode($this->config->item('minecraft_payments')); ?>;
        
    </script>    
    
    <!-- Init -->
    <script src="<?php echo base_url(); ?>js/airtel.init.js"></script>
    
    <!-- Iframe unit -->
    <script src="<?php echo base_url(); ?>js/airtel.iframe.js"></script>
    
    <!-- Wizard -->
    <script src="<?php echo base_url(); ?>js/airtel.wizard.js"></script>
    
    <!-- Datatables -->
    <script src="<?php echo base_url(); ?>js/airtel.datatables.js"></script>
    
    <!-- Additional jquery validation functions -->
    <script src="<?php echo base_url(); ?>js/airtel.validation.js"></script>
    
    <?php
    // Custom module javascript
    if($this->config->item('load_custom_js') == TRUE):
        $this->load->view($this->module->active_module.'/js/'.$this->module->active_module.'_js');
        $this->load->view($this->module->active_module.'/js/validation_js');
    endif;
    ?>
    
    <script>
        $(document).ready(function() {
            
            setTimeout(function() {
                $('.module-wrapper').hide();
                $('html').removeClass('js');
                $('.module-wrapper').fadeIn(400);
            }, 700);
            
        });
    </script>
    
</body>
</html>