<?php echo $this->ui->breadcrumb(); ?>
<div id="content-wrapper">
    
    <?php echo $this->ui->system_messages(); ?>
    
    <?php if($this->{$this->module->active_module.'_lib'}->check_login(TRUE) == TRUE): ?>
    
        <h4 class="heading">Login</h4>
        
        <p>Tu nēesi ielogojies muonline Web-shop aplikācijā.</p>
        <p>Login funkcija caur maksājumu sistēmu ir atslēgta.</p>
        <p>Lūdzu pārej uz Web-shop un veic ielogošanās procedūru pirms lietot pakalpojumus, vai arī vari lietot tos pakalpojumus, kuriem nav nepieciešams obligātais log-in!</p>

        
    <?php else: ?>            
         
        <h4 class="heading">Tavs Muonline shop profils</h4>    
            
        
        <table class="table table-striped table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th>Pakalpojums</th>
                    <th>Daudzums / vērtība</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Username:</td>
                    <td><?php echo $this->mu_username; ?></td>
                </tr>
                
                <tr>
                    <td>Ban statuss:</td>
                    <td><?php echo ($this->muonline_model->check_ban($this->mu_username) == TRUE) ? 'Ir' : 'Nav'; ?></td>
                </tr>
                
                <?php if(array_key_exists('credits', $this->module->services)): ?>
                <tr>
                    <td>Credits:</td>
                    <td><?php echo $this->muonline_model->get_credits($this->mu_username); ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if(array_key_exists('cspoints', $this->module->services)): ?>
                    <tr>
                        <td>CSpoints:</td>
                        <td><?php echo $this->muonline_model->get_cspoints($this->mu_username); ?></td>
                    </tr>
                <?php endif; ?>

                <?php if(array_key_exists('vipserver', $this->module->services)): ?>
                    <tr>
                        <td>Vip server access:</td>
                        <td>
                            <?php
                            $data = $this->muonline_model->get_user_service_data($this->mu_username, 'muonline', 'vipserver', NULL);
                            
                            if($data !== FALSE)
                            {
                                echo 'Līdz: <strong>'.date('d.m.Y H:i:s', $data->expires).'</strong>';
                            }
                            else
                            {
                                echo 'Nav';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>
                
            </tbody>
        </table>
      
    <?php endif; ?>    
    
</div>