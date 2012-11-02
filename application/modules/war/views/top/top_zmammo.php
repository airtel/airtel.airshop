<table class="table table-striped table-bordered table-hover table-condensed datatable_war">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>ZM Ammo</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->auth; ?></td>
                    <td><?php echo $player->amount; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>