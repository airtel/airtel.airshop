<table class="table table-striped table-bordered table-hover table-condensed datatable_b">
    <thead>
        <tr>
            <th>Account</th>
            <th>Iemesls</th>
            <th>Datums</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->username; ?></td>
                    <td><?php echo $player->banreason; ?></td>
                    <td><?php echo date('d.m.Y H:i', $player->bandate); ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>