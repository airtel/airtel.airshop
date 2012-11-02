<?php echo $this->ui->breadcrumb(); ?>
<div id="content-wrapper">


    <?php echo $this->ui->system_messages(); ?>


    <?php
    // show login form
    if($this->{$this->module->active_module.'_lib'}->check_login(TRUE) == TRUE)
    {
        ?>

        <h4 class="heading">Login</h4>

        <?php
        if($this->config->item('ipb_inline_login') == FALSE)
        {
            ?>
            <p>Tu nēesi ielogojies IPB forumā.</p>
            <p>Login funkcija caur maksājumu sistēmu ir atslēgta.</p>
            <p>Lūdzu pārej uz forumu un veic ielogošanās procedūru pirms lietot pakalpojumus, vai ja tavs akaunts ir "Suspend" režīmā izmanto "Unspusned" funkciju, kurai nav nepieciešama ielogošanās iekš IPB foruma!</p>	
            <?php
        }
        else
        {
            ?>
            <p>Mēs dodam tev iespēju tikt ielogotam, jo aktīvu IPB sessiju neesam atraduši.</p>
            <p>Tava akaunta suspend gadījumā izmanto "Unsuspend" funkciju, kurai nav nepieciešama ielogošanās iekš IPB foruma!</p>

            <?php echo form_open($this->module->active_module.'/login/', $this->config->item('ipbform_attr')); ?>

                <fieldset>

                    <p class="f_legend">Ienākt IPB:</p>

                    <div class="control-group">
                        <label class="control-label">IPB lietotājvārds:</label>
                        <div class="controls">
                            <input name="ipb_username" type="text" />
                        </div>
                    </div>


                    <div class="control-group">
                        <label class="control-label">IPB parole:</label>
                        <div class="controls">
                            <input name="ipb_password" type="password" />
                        </div>
                    </div>


                    <div class="control-group">
                        <label class="control-label"></label>
                        <div class="controls">
                            <button class="btn btn-info">Ienākt</button>
                        </div>
                    </div>

                </fieldset>

            <?php echo form_close();
            
        }


    }
    // show profile
    else
    {
    ?>

        <h4 class="heading">Tavs IPB shop profils <?php echo anchor('ipb/logout', '( Iziet )'); ?></h4>    


        <table class="table table-striped table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th>Pakalpojums</th>
                    <th>Daudzums / vērtība</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Tavs title</td>
                    <td><?php echo ($this->ipb_member_data->title != '') ? $this->ipb_member_data->title : 'Nav uzstādīts'; ?></td>
                </tr>

                <tr>
                    <td>Tavs display name</td>
                    <td><?php echo ($this->ipb_member_data->members_display_name != '') ? $this->ipb_member_data->members_display_name : 'Nav uzstādīts'; ?></td>
                </tr>

                <?php if(array_key_exists('ipoints', $this->module->services)): ?>
                    <tr>
                        <td>I-points</td>
                        <td><?php echo $this->ipb_model->get_member_ipoints($this->ipb_member_id); ?></td>
                    </tr>
                <?php endif; ?>

                <?php if(array_key_exists('unwarn', $this->module->services)): ?>
                    <tr>
                        <td>Warn līmenis</td>
                        <td><?php echo (int)$this->ipb_member_data->warn_level; ?> %</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    <?php

    }