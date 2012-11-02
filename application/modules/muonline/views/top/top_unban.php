<table class="table table-striped table-bordered table-hover table-condensed datatable_muonline">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>Iemesls</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->memb___id; ?></td>
                    <td><?php echo ($player->block_reason == 0) ? 'Bez iemesla' : $player->block_reason ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>