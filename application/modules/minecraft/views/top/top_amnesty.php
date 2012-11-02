<table class="table table-striped table-bordered table-hover table-condensed datatable_minecraft">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>Atlikušais laiks</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0 && is_array($players)):
            
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->PlayerName; ?></td>
                    <td><?php echo (ctype_digit($player->RemainTime)) ? $player->RemainTime .' minūtes' : 'Bez termiņa'; ?></td>
                </tr>
                <?php
            endforeach;
            
        endif;
        ?>

    </tbody>
</table>

<?php if($this->module->services[$this->module->active_service]['functionality'] == 'rcon'): ?>
    <br />
    <p>Tabula atjaunojas reizi 5 minūtēs...</p>

<?php endif; ?>