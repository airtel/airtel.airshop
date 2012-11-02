<table class="table table-striped table-bordered table-hover table-condensed datatable_ipb">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>Ipoints daudzums</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->name; ?></td>
                    <td><?php echo $player->points; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>