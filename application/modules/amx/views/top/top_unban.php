<table class="table table-striped table-bordered table-hover table-condensed datatable_a">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>IP adrese</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->player_nick; ?></td>
                    <td><?php echo $player->player_ip; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>