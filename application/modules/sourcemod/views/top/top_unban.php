<table class="table table-striped table-bordered table-hover table-condensed datatable_a">
    <thead>
        <tr>
            <th>Lietotājs</th>
            <th>IP adrese / STEAM:ID</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->name; ?></td>
                    <td><?php echo ($this->config->item('use_steam') == FALSE) ? $player->ip : $player->authid; ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>