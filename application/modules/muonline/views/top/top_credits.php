<table class="table table-striped table-bordered table-hover table-condensed datatable_muonline">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>Credits daudzums</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->memb___id; ?></td>
                    <td><?php echo $player->credits; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>