<table class="table table-striped table-bordered table-hover table-condensed datatable_minecraft">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>Iemesls</th>
            <th>Līdz</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0 && is_array($players)):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->name; ?></td>
                    <td><?php echo $player->reason; ?></td>
                    <td><?php echo ($player->temptime > 0) ? date('d-m-Y H:i:s', $player->temptime) : 'Bez termiņa'; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>