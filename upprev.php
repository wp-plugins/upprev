<?php
/*
Plugin Name: upPrev Previous Post Animated Notification
Plugin URI: http://item-9.com/upPrev/
Description: When scrolling post down upPrev will display a flyout box with a link to the previous post from the same category. <a href="options-general.php?page=upprev">Options configuration panel</a>
Author: Jason Pelker, Grzegorz Krzyminski
Version: 1.4.0
Author URI: http://item-9.com/
*/

if ( !function_exists( 'd' ) ) {
    function d( $array, $params = null )
    {
        $www = isset ( $_SERVER['HTTP_HOST'] );
        if ($www) {
            print '<hr /><pre>';
        }
        print_r( $array );
        if ( isset( $params ) and count ( $params ) ) {
            foreach ( $params as $one ) {
                if ( preg_match ( '/^\d+$/', $one ) ) {
                    $array = preg_replace ( '/\?/', $one, $array, 1 );
                } else {
                    $array = preg_replace ( '/\?/', "'".$one."'", $array, 1 );
                }
            }
            print ($www)? '<hr />':"\n";
            print_r( $array );
        }
        print ($www)? '</pre><hr />':"\n\n";
    }
}

load_plugin_textdomain('upprev', false, dirname( plugin_basename( __FILE__) ).'/languages');

include('upprev_settings.php');

function upprev_box()
{
    //rewind posts;
    global $post;
    if ( is_single() && get_adjacent_post(true, '', true) ) {
        $options = get_option('upprev-settings-group');
        $display_excerpt = $options['upprev_content_excerpt'];
        $display_thumb = $options['upprev_content_thumb'];
        $compare_by = $options['upprev_compare'];
        $args = array(
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array( $post->ID ),
            'posts_per_page' => 1
        );
        $number_of_posts = 0;
        $count_args = array();
        $ids = array();
        $siblings = array();
        if ( $compare_by == 'category' ) {
            foreach((get_the_category()) as $one) {
                $siblings[ get_category_link( $one->term_id ) ] = $one->name;
                $ids[] = $one->cat_ID;
            }
            $args['cat'] = implode(',',$ids);
            $count_args = array ( 'include' => $args['cat'] );
        } else {
            foreach((get_the_tags()) as $one) {
                $siblings[ get_tag_link( $one->term_id ) ] = $one->name;
                $ids[] = $one->term_id;
            }
            $args['tag__in'] = $ids;
            $count_args = array ( 'include' => implode(',', $args['tag__in'] ), 'taxonomy' => 'post_tag' );
        }
        $taxonomies = get_categories( $count_args  );
        foreach( $taxonomies as $one ) {
            $number_of_posts += $one->count;
        }
        add_filter('excerpt_more',   'upprev_excerpt_more', 10, 1 );
        add_filter('excerpt_length', 'upprev_excerpt_length', 10, 1);
        $query = new WP_Query( $args );
        while ( $query->have_posts() ) {
            $query->the_post();

            echo '<div id="upprev_box">';
            echo '<h6>';
            if ( count( $siblings ) ) {
                printf ( '%s ', __('More in', 'upprev' ) );
                $a = array();
                foreach ( $siblings as $url => $name ) {
                    $a[] = sprintf( '<a href="%s">%s</a>', $url, $name );
                }
                echo implode( ', ', $a);
            }
            echo '<span class="num"> (';
            printf(_n('One article', '%1$d articles', $number_of_posts, 'upprev' ), $number_of_posts);
            echo ')</span>';
            echo '</h6><div class="upprev_excerpt">';
            if ( $display_thumb ) {
                printf(
                    '<a href="%s" title="%s">%s</a>',
                    get_permalink(),
                    wptexturize(get_the_title()),
                    get_the_post_thumbnail( get_the_ID(), array( 48, 48),array('title'=>get_the_title(),'class'=>'upprev_thumb')  )
                );
            }
            printf(
                '<a href="%s">%s</a>',
                get_permalink(),
                get_the_title()
            );

            if ($display_excerpt) {
                the_excerpt( $excerpt_length );
            }
            printf( '</div><button id="upprev_close" type="button">%s</button></div>', __('Close', 'upprev') );
        }
        wp_reset_postdata();
        remove_filter('excerpt_more',   'upprev_excerpt_more', 10, 1 );
        remove_filter('excerpt_length', 'upprev_excerpt_length', 10, 1);
    }
}

add_action('wp_footer', 'upprev_box');

function upprev_init()
{
    $plugin_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
    wp_enqueue_script("jquery");
    wp_enqueue_script("upprev-js",$plugin_path.'upprev_js.php'); //wp_enqueue_script("upprev-js",$plugin_path.'upprev_js.php',array(),'1.4.0',true);
    wp_enqueue_style("upprev-css",$plugin_path.'upprev.css');
}
add_action('init', 'upprev_init');

function upprev_styles()
{
    $options = get_option("upprev-settings-group");
    $position = $options['upprev_position'] != 'left' ? "right" : "left";
    if ($options['upprev_animation'] == "fade") {
        echo "<style type='text/css'>#upprev_box {display:none;$position: 0px;}</style>\n";
    } else {
        echo "<style type='text/css'>#upprev_box {display:block;$position: -400px;}</style>\n";
    }
}
add_action('wp_print_styles', 'upprev_styles');

function upprev_excerpt_more($more)
{
    return '...';
}
function upprev_excerpt_length($length)
{
    $options = get_option('upprev-settings-group');
    return isset($options['upprev_excerpt_length']) && $options['upprev_excerpt_length'] != '' ? $options['upprev_excerpt_length'] : 20;
}

