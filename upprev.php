<?php
/*
Plugin Name: upPrev
Plugin URI: http://upprev.com/
Description: When scrolling post down upPrev will display a flyout box with a link to post in chosen configuration.
Version: trunk
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*

Copyright 2011-2015 Marcin Pietrzak (marcin@iworks.pl)

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

if ( !defined( 'WPINC' ) ) {
    die;
}

/**
 * static options
 */
define( 'IWORKS_UPPREV_VERSION', 'trunk' );
define( 'IWORKS_UPPREV_PREFIX',  'iworks_upprev_' );

require_once dirname(__FILE__).'/includes/common.php';

$iworks_upprev = new IworksUpprev();

/**
 * install & uninstall
 */
register_activation_hook  ( __FILE__, 'iworks_upprev_activate'   );
register_deactivation_hook( __FILE__, 'iworks_upprev_deactivate' );
