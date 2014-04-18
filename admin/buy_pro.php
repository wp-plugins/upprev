<?php

global $iworks_upprev;
ob_start();
?>
<div style="width:50%;float:right;">
    <h2><?php _e( 'Buy Pro Version', 'iworks_upprev' ); ?></h2>
    <h4><?php _e( 'Single site licence', 'iworks_upprev' ); ?></h4>
    <dl>
        <dt><?php _e( 'Price', 'iworks_upprev'); ?></dt>
        <dd><b>$40</b></dd>
    </dl>
    <p><?php _e( 'Attention! Before buying upPrev Pro, check if the basic version of the plugin works correctly on your blog.', 'iworks_upprev' ) ?></p>
    <p><?php _e( 'To gain access to all the features of <strong>upPrev Pro</strong>, simply buy a lifetime license using <a href="http://paypal.com/" title="PayPal">PayPal</a>.', 'iworks_upprev') ?></p>
    <div id="iworks_upprev_paypal">
        <form name="f" action="https://www.paypal.com/cgi-bin/webscr" method="post" style="margin: 0 0 20px 20px;" target="_blank">
            <input type="hidden" name="amount" value="40" />
            <input type="hidden" name="cmd" value="_xclick" />
            <input type="hidden" name="no_note" value="1" />
            <input type="hidden" name="no_shipping" value="1" />
            <input type="hidden" name="currency_code" value="USD" />
            <input type="hidden" name="notify_url" value="http://iworks.pl/paypal.index.php" />
            <input type="hidden" name="business" value="paypal@iworks.pl" />
            <input type="hidden" name="item_name" value="upPrev" />
            <input type="hidden" name="item_number" value="" />
            <input type="hidden" name="quantity" value="1" />
            <input type="hidden" name="lc" value="US" />
            <input type="hidden" name="return" value="http://iworks.pl" />
            <input type="hidden" name="cancel_return" value="http://iworks.pl" />
            <input type="image" style="display: block;margin: 20px auto;" src="http://www.paypalobjects.com/en_US/GB/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
        </form>
    </div>
    <p><?php _e("Your license will be automatically activated just after the transaction.", 'iworks_upprev') ?></p>
    <h4><?php _e( 'Multiple sites licence', 'iworks_upprev' ); ?></h4>
    <p><?php _e("If you would like to purchase licenses for more of your blogs, please send an e-mail to <a href='mailto:marcin@iworks.pl'>marcin@iworks.pl</a>. You can get a discount when purchasing multiple licenses at one time.", 'iworks_upprev') ?></p>
</div>
<div style="width: 49%; float: left">
    <h2><?php _e( 'Benefits', 'iworks_upprev' ); ?></h2>
    <ul class="ul-square">
        <li><?php _e( 'All templates available.', 'iworks_upprev' ); ?></li>
        <li><?php _e( 'All positions available.', 'iworks_upprev' ); ?></li>
        <li><?php _e( 'You can set up colors.', 'iworks_upprev' ); ?></li>
        <li><?php _e( 'You can exclude categories.', 'iworks_upprev' ); ?></li>
        <li><?php _e( 'You can exclude tags.', 'iworks_upprev' ); ?></li>
    </ul>
</div>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;


