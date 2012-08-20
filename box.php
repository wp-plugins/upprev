<?php
define( 'WP_USE_THEMES', false );
require '../../../wp-load.php';

if ( isset( $_GET['p'] ) && $_GET['p'] ) {
    query_posts( array( 'p'  => intval( $_GET['p'] ), 'posts_per_page' => 1 ) );
    while ( have_posts() ) {
        the_post();
        $iworks_upprev->the_box();
    }
}

