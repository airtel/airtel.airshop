<table class="table table-striped table-bordered table-hover table-condensed datatable_b">
    <thead>
        <tr>
            <th>Vārds</th>
            <th>Summa</th>
            <th>Datums</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        if(count($players) > 0):
            
            $this->load->helper('text');
        
            foreach($players as $player):
                ?>
                <tr>
                    <td>
                        <?php 
                        if($player->message != '')
                        {
                            echo '<i class="icon-comments icon-large" style="opacity: .5;"></i> ';
                            echo '<a data-placement="right" rel="tooltip" href="#" title="'.ellipsize($player->message, 32, .5).'">'.$player->username.'</a>';
                        }
                        else
                        {
                            echo $player->username;
                        }
                        ?>
                    </td>

                    <td><?php echo number_format($player->total, 2, '.', ''); ?></td>
                    <td><?php echo date('d-m-Y H:i:s', $player->datetime); ?></td>
                </tr>
                <?php
            endforeach;
        
        endif;
        ?>

    </tbody>
</table>