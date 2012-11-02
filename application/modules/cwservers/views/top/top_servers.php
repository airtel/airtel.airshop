<table class="table table-striped table-bordered table-hover table-condensed datatable_b">
    <thead>
        <tr>
            <th>Serveris</th>
            <th>Nosaukums</th>
            <th>Aizņemts līdz</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($servers) > 0):
        
            foreach($servers as $server):
                ?>
                <tr>
                    <td><?php echo $server->server_hostname; ?>:<?php echo $server->port; ?></td>
                    <td><?php echo $server->name; ?></td>
                    <td><?php echo ($server->expires < time()) ? 'Nav laika' : date('d.m.Y H:i', $server->expires); ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>