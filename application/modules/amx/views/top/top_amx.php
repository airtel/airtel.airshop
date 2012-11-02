<div id="amx_accordion" class="accordion">
    
    <?php 
    $i = 1;
  
    if(count($servers) > 0):

        foreach($servers as $server): 

            ?>

            <div class="accordion-group">
                
                <div class="accordion-heading">
                    <a href="#collapse<?php echo $i; ?>" data-parent="#amx_accordion" data-toggle="collapse" class="accordion-toggle">
                        <?php echo $server->hostname; ?>
                    </a>
                </div>
                
                <div class="accordion-body collapse <?php echo ($i == 1) ? 'in' : ''; ?>" id="collapse<?php echo $i; ?>">
                    <div class="accordion-inner">

                        <table class="table table-striped table-bordered table-hover table-condensed datatable_b">
                            <thead>
                                <tr>
                                    <th>Lietotājs</th>
                                    <th>Pakalpojums</th>
                                    <th>Derīgs līdz</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if(count($players) > 0):

                                    foreach($players as $player):

                                        if($player->server_id == $server->id):
                                        ?>

                                            <tr>
                                                <td><?php echo $player->username; ?></td>
                                                <td><?php echo $player->service; ?></td>
                                                <td><?php echo date('d-m-Y H:i:s', $player->expires); ?></td>
                                            </tr>

                                        <?php
                                        endif;

                                    endforeach;

                                endif;
                                ?>

                            </tbody>
                        </table>

                    </div>
                    
                </div>
                
            </div>

            <?php
            $i++;
        endforeach;
        
    endif;
    ?>
    
</div>