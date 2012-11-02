<?php

global $iworks_upprev;
ob_start(); 

?>
<div style="width:50%;float:right;">


<h2><?php _e( 'Buy Pro Version', 'iworks_upprev' ); ?></h2>

<h4><?php _e( 'Single site licence', 'iworks_upprev' ); ?></h4>

<dl>
    <dt><?php _e( 'Price', 'iworks_upprev'); ?></dt>
    <dd><b>$40</b> - <a href="<?php $iworks_upprev->link_buy('this-site'); ?>">buy now for this site</a> or <a href="">buy for other</a></dd>
</dl>

</div>
<div style="width: 49%; float: left">
<h2><?php _e( 'Benefits', 'iworks_upprev' ); ?></h2>
<ul class="ul-square">
    <li><?php _e( 'All templates available.', 'iworks_upprev' ); ?></li>
    <li><?php _e( 'All positions available.', 'iworks_upprev' ); ?></li>
    <li><?php _e( 'You can set up colors.', 'iworks_upprev' ); ?></li>
    <li><?php _e( 'You can exclude categories.', 'iworks_upprev' ); ?></li>
</div>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;


