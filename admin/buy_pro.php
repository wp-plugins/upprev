<?php

global $iworks_upprev;
ob_start(); 

?>
<h2><?php _e( 'Buy Pro Version', 'iworks_upprev' ); ?></h2>

<h4><?php _e( 'Single site licence', 'iworks_upprev' ); ?></h4>
<dl>
    <dt><?php _e( 'Price', 'iworks_upprev'); ?></dt>
    <dd><b>$40</b> - <a href="<?php $iworks_upprev->link_buy('this-site'); ?>">buy now for this site</a> or <a href="">buy for other</a></dd>
</dl>

<h4><?php _e( '3-pack licence', 'iworks_upprev' ); ?></h4>
<dl>
    <dt><?php _e( 'Price', 'iworks_upprev'); ?></dt>
    <dd><b>$90</b> - 3 sites</dd>
</dl>

<h4><?php _e( 'Developer pack licence', 'iworks_upprev' ); ?></h4>
<dl>
    <dt><?php _e( 'Price', 'iworks_upprev'); ?></dt>
    <dd><b>$200</b> - 10 sites</dd>
</dl>

</dl>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;


