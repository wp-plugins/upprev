<?php

include_once ABSPATH.'/wp-admin/includes/meta-boxes.php';


$iworks_upprev->update(); 


?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e('upPrev', 'upprev') ?></h2>
<?php
if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ) {
    $iworks_upprev_options->update_option( 'cache_stamp', date('c') );
    echo '<div id="message" class="updated fade"><p>'.__('upPrev options saved.', 'upprev').'</p></div>';
}
?>
    <form method="post" action="options.php" id="iworks_upprev_admin_index">
        <input type="hidden" name="is_pro" id="upprev_is_pro" value="<?php echo $iworks_upprev->is_pro()? 'yes':'no'; ?>" />
        <div class="postbox-container" style="width:75%">
<?php

wp_enqueue_script('post');

$option_name = basename( __FILE__, '.php');
$iworks_upprev_options->settings_fields( $option_name );
$iworks_upprev_options->build_options( $option_name );

$configuration = get_option( 'iworks_upprev_configuration', 'simple' );
if ( !preg_match( '/^(advance|simple)$/', $configuration ) ) {
    $configuration = 'simple';
    $iworks_upprev_options->update_option( 'configuration', $configuration );
}

?>
        </div>
        <div class="postbox-container" style="width:23%;margin-left:2%">
            <div class="metabox-holder">
                <div id="links" class="postbox">
                    <h3 class="hndle"><?php _e( 'Choose configuration mode', 'upprev' ); ?></h3>
                    <div class="inside">
                        <p><?php _e( 'Below are some links to help spread this plugin to other users', 'upprev' ); ?></p>
                        <ul>
                        <li><input type="radio" name="iworks_upprev_configuration" value="simple" id="iworks_upprev_configuration_simple"   <?php checked( $configuration, 'simple' ); ?>/> <label for="iworks_upprev_configuration_simple"><?php _e( 'simple', 'upprev' ); ?></label></li>
                        <li><input type="radio" name="iworks_upprev_configuration" value="advance" id="iworks_upprev_configuration_advance" <?php checked( $configuration, 'advance' ); ?>/> <label for="iworks_upprev_configuration_advance"><?php _e( 'advance', 'upprev' ); ?></label></li>
                        </ul>
                    </div>
                </div>
                <div id="links" class="postbox">
                    <h3 class="hndle"><?php _e( 'Loved this Plugin?', 'upprev' ); ?></h3>
                    <div class="inside">
                        <p><?php _e( 'Below are some links to help spread this plugin to other users', 'upprev' ); ?></p>
                        <ul>
                            <li><a href="http://wordpress.org/support/view/plugin-reviews/upprev#postform"><?php _e( 'Give it a five stars on Wordpress.org', 'upprev' ); ?></a></li>
                            <li><a href="http://wordpress.org/extend/plugins/upprev/"><?php _e( 'Link to it so others can easily find it', 'upprev' ); ?></a></li>
                        </ul>
                    </div>
                </div>
                <div id="help" class="postbox">
                    <h3 class="hndle"><?php _e( 'Need Assistance?', 'upprev' ); ?></h3>
                    <div class="inside">
                        <p><?php _e( 'Problems? The links bellow can be very helpful to you', 'upprev' ); ?></p>
                        <ul>
                            <li><a href="<?php _e( 'http://wordpress.org/tags/upprev', 'upprev' ); ?>"><?php _e( 'Wordpress Help Forum', 'upprev' ); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

