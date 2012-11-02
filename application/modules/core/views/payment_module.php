<ul class="stepy-titles clearfix">
    <li class="current-step" id="title-step1">
        <div>Solis 1</div>
        <span>Ievadi savus datus&hellip;</span>
        <span class="stepNb">1</span>
    </li>

    <li class="" id="title-step2">
        <div>Solis 2</div>
        <span>Veic pakalpojumu apmaksu&hellip;</span>
        <span class="stepNb">2</span>
    </li>
</ul>


<?php echo (isset($form_action)) ? form_open($form_action, $this->config->item('wizard_attr')) : form_open($this->module->active_module.'/index/'.$this->module->active_service, $this->config->item('wizard_attr')); ?>
        
    <fieldset>

        <!-- First step -->
        <div class="step" id="step1" style="min-height: 250px;">
            <?php
            foreach($fields as $key => $value):
                
                $form_error = form_error($key);
                ?>

                <div class="control-group <?php echo ( ! empty($form_error)) ? 'error f_error' : ''; ?>">
                    <label class="control-label"><?php echo $value['label']; ?>:</label>
                    <div class="controls">
                        
                        <?php
                        if($value['type'] == 'text')
                        {
                            echo '<label class="input-label"><strong>'.$value['value'].'</strong></label>';
                        }
                        
                        elseif($value['type'] == 'input')
                        {
                            $value['options']['class'] = $value['ajax_validation'];
                            echo form_input($value['options']);
                        }

                        elseif($value['type'] == 'dropdown')
                        {
                            if(empty($value['data']))
                            {
                                $value['data'] = array();
                            }

                            echo form_dropdown($key, $value['data'], $value['value'], $value['options']);
                        }
                        
                        // CI error reporting
                        echo ( ! empty($form_error)) ? '<label class="error" for="'.$key.'" generated="true">'.$form_error.'</label>' : ''; 
                        ?>

                    </div>
                </div>

                <?php
                
            endforeach;
            ?>
        </div>
        
        
        <!-- Second step -->
        <div class="step" id="step2" style="min-height: 250px;">

            
            
            <div class="tabbable tabbable-bordered">
                <ul id="pay_tabs" class="nav nav-tabs">

                    <?php
                    $i = 1;
                    foreach($this->module->pay_methods as $pay_method):
                        ?>
                        <li class="<?php echo ($i == 1) ? 'active' : ''; ?>">
                            <a href="#tab_br<?php echo $i; ?>" data-toggle="tab">
                                <i class="payment <?php echo $pay_method; ?>"></i><?php echo $this->config->item($pay_method, 'paymethod_names'); ?>
                            </a>
                        </li>
                        <?php
                        $i++;
                    endforeach;
                    ?>

                </ul>

                <div class="tab-content">

                    <?php
                    $i = 1;
                    foreach($this->module->pay_methods as $pay_method):

                        ?>
                        <div class="tab-pane <?php echo ($i == 1) ? 'active' : ''; ?>" id="tab_br<?php echo $i; ?>">
                            <p>                

                                <?php
                                $system_fields = $this->config->item('fields_'.$pay_method);
                                foreach($system_fields as $field => $options):
                                    
                                    $form_error = form_error($field);
                                    ?>

                                    <div class="control-group <?php echo ( ! empty($form_error)) ? 'error f_error' : ''; ?>">
                                        <label class="control-label"><?php echo $options['label']; ?>:</label>
                                        <div class="controls">

                                            <?php


                                            if($options['type'] == 'input')
                                            {
                                                $options['options']['class'] = $options['ajax_validation'];
                                                echo form_input($options['options']);
                                            }

                                            elseif($options['type'] == 'dropdown')
                                            {

                                                if($field == 'prices_'.$pay_method)
                                                {
                                                    $options['data'] = $this->priceplan[$pay_method];
                                                }
                                                elseif($field == 'countries')
                                                {
                                                    $options['data'] = $this->config->item('countries');
                                                }

                                                echo form_dropdown($field, $options['data'], $options['value'], $options['options']);
                                            }

                                            // CI error reporting
                                            echo ( ! empty($form_error)) ? '<label class="error" for="'.$field.'" generated="true">'.$form_error.'</label>' : ''; 
                                            
                                            ?>

                                        </div>
                                    </div>

                                    <?php

                                endforeach;
                                ?>

                            <?php
                            if($pay_method == 'sms'):

                                echo $this->ui->sms_sendtext($this->priceplan['sms'], $this->module->sms_prices); 

                            elseif($pay_method == 'ibank'):

                                ?>
                            
                                <div class="control-group">
                                    <label class="control-label"></label>
                                    <div class="controls">
                                        <strong><a rel="tooltip" title="Ātrais apmaksas veids. iBank kods ir pieejams uzreiz pēc apmaksas." id="airtel_ibank_system" href="#">Iegādāties airtel iBank kodu <i class="icon-external-link"></i></a></strong>
                                    </div>
                                </div>
                                
                                <?php
                                
                            elseif($pay_method == 'paypal'):
                                
                                ?>
                            
                                <div class="control-group">
                                    <label class="control-label"></label>
                                    <div class="controls">
                                        <strong><a rel="tooltip" title="Ātrais apmaksas veids. Paypal kods ir pieejams uzreiz pēc apmaksas." id="airtel_paypal_system" href="#">Iegādāties airtel Paypal kodu <i class="icon-external-link"></i></a></strong>
                                    </div>
                                </div>
                            
                                <?php

                            endif;
                            
                            ?>
                            
                        </div>
                        <?php

                        $i++;
                    endforeach;

                    ?>

                </div>
            </div>
        </div>
        
        
        <!-- Navigation -->
        <div id="navigation2">
            <div class="form-actions">
                <input class="navigation_button btn" id="back2" value="Back" type="reset" />
                <input class="navigation_button btn btn-inverse" id="next2" name="submit" value="submit" type="submit" />
            </div>
        </div>
        
        
        <!-- notification -->
        <div class="modal hide" id="payment-window-notification">
            <div class="modal-header">
                <h3>Notification</h3>
            </div>
            <div class="modal-body">
                
                <p>Pašlaik ir atvērts airtel koda apmaksas popup logs. Lūdzu veiciet tajā visas nepieciešamās darbības koda iegādei un pēc darbību veikšanas aizveriet apmaksas logu.
                Tad šim paziņojumam vajadzētu pazust.</p>
                
                <div class="progress progress-striped active">
                    <div class="bar" style="width: 100%;"></div>
                </div>
                
            </div>
        </div>

    </fieldset>
    
<?php echo form_close(); ?>