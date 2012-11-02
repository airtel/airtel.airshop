<table class="table table-striped table-bordered table-hover table-condensed datatable_war">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>Exp</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
            $this->load->helper('text');
            
            foreach($players as $player):
                ?>
                <tr>
                    <td><a data-placement="right" rel="tooltip" href="#" title="<?php echo $player->race_name; ?>"><?php echo $player->player_name; ?></a></td>
                    <td><?php echo $player->race_xp; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>