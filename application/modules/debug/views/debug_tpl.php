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

    
        <?php echo $this->ui->system_messages(); ?>

        
        <table class="table table-striped table-bordered dTableR" id="dt_a">
            <thead>
                <tr>
                    <th>Param</th>
                    <th>Apraksts</th>
                </tr>
            </thead>
            <tbody>

                <?php
                if(count($report) > 0):

                    foreach($report as $r):
                        ?>
                        <tr>
                            <td><span class="label label-<?php echo $r['type']; ?>"><?php echo $r['param']; ?></span></td>
                            <td><?php echo $r['message']; ?></td>
                        </tr>
                        <?php
                    endforeach;

                endif;
                ?>

            </tbody>
        </table>
    
    
    
    <?php endif; ?>
        
</div>
