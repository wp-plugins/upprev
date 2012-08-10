<?php
/*
Plugin Name: upPrev
Plugin URI: http://iworks.pl/upprev/
Description: When scrolling post down upPrev will display a flyout box with a link to post in choosen configuration.
Version: trunk
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*

Copyright 2011-2012 Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

/**
 * static options
 */
define( 'IWORKS_UPPREV_VERSION', 'trunk' );
define( 'IWORKS_UPPREV_PREFIX',  'iworks_upprev_' );

require_once dirname(__FILE__).'/includes/common.php';

new IworksUpprev();

/**
 * install & uninstall
 */
register_activation_hook  ( __FILE__, 'iworks_upprev_activate'   );
register_deactivation_hook( __FILE__, 'iworks_upprev_deactivate' );

function iworks_upprev_check()
{
    if ( !is_single() && !is_page() ) {
        return true;
    }
    /**
     * check mobile devices
     */
    if ( iworks_upprev_check_mobile_device() ) {
        return true;
    }
    /**
     * global option object
     */
    global $iworks_upprev_options;
    /**
     * check post types
     */
    $post_types = $iworks_upprev_options->get_option( 'post_type' );
    if ( empty( $post_types ) ) {
        $post_types = iworks_upprev_get_default_value( 'post_type' );
    }
    if ( is_page() && $iworks_upprev_options->get_option( 'match_post_type' ) ) {
        return !array_key_exists( 'page', $post_types );
    } else if ( $iworks_upprev_options->get_option( 'match_post_type' ) ) {
        global $post;
        return !array_key_exists( get_post_type( $post ), $post_types );
    }
    return false;
}




