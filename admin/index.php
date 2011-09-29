<div class="wrap">
    <div id="icon-themes" class="icon32"><br /></div>
    <h2><?php _e('upPrev', 'upprev') ?></h2>
<?php
if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ) {
    $iworks_upprev_options->update_option( 'cache_stamp', date('c') );
    echo '<div id="message" class="updated fade"><p>'.__('upPrev options saved.', 'upprev').'</p></div>';
}
?>
    <form method="post" action="options.php" id="iworks_upprev_admin_index">
<?php
$option_name = basename( __FILE__, '.php');
$iworks_upprev_options->build_options( $option_name );
$iworks_upprev_options->settings_fields( $option_name );
?>
    </form>
</div>

