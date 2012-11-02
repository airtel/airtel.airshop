<table class="table table-striped table-bordered table-hover table-condensed datatable_minecraft">
    <thead>
        <tr>
            <th>Frakcija</th>
            <th>Derīgs līdz</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
       
        if(count($players) > 0 && is_array($players)):
        
            foreach($players as $player):
                ?>
                <tr>
                    <td><?php echo $player->username; ?></td>
                    <td><?php echo date('d-m-Y H:i:s', $player->expires); ?></td>
                </tr>
                <?php
            endforeach;
            
        endif;
        ?>

    </tbody>
</table>