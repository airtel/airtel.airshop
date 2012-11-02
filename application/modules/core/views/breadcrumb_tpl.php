<ul class="breadcrumb" id="nav-line">
    <li><i class="icon-home icon-large breadcrumb-home"></i></li>
    <li><?php echo ucfirst($this->uri->segment(1)); ?></li>
    <li>
        <?php
        if($this->uri->segment(2) == 'login'):
            ?>
            <a href="<?php echo site_url($this->module->active_module.'/login/'); ?>"><?php echo ucfirst($this->uri->segment(1)); ?> Profils</a>
            <?php
        else:    
            ?>
            <a href="<?php echo site_url($this->module->active_module.'/index/'.$this->module->active_service); ?>"><?php echo $this->module->services[$this->module->active_service]['title']; ?></a>
            <?php
        endif;
        ?>
    </li>
</ul>