<?php

require_once dirname( __FILE__ ).'/options.php';
require_once dirname( __FILE__ ).'/iworks.options.class.php';

$iworks_upprev_options = new IworksOptions();
$iworks_upprev_options->set_option_function_name( 'iworks_upprev_options' );
$iworks_upprev_options->set_option_prefix( IWORKS_UPPREV_PREFIX );

function iworks_upprev_options_init()
{
    global $iworks_upprev_options;
    $iworks_upprev_options->options_init();
    add_filter( 'plugin_row_meta', 'iworks_upprev_plugin_links', 10, 2 );
    $text = __("<p>upPrev settings allows you to set the proprites of user notification showed when reader scroll down the page.</p>");
    add_contextual_help( 'upprev/admin/index', $text );
}

function iworks_upprev_activate()
{
    require_once dirname(__FILE__).'/options.php';
    global $iworks_upprev_options;
    $iworks_upprev_options->activate();
}

function iworks_upprev_deactivate()
{
    global $iworks_upprev_options;
    $iworks_upprev_options->deactivate();
}

