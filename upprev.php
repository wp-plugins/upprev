<?php
/*
Plugin Name: upPrev
Plugin URI: http://iworks.pl/upprev/
Description: When scrolling post down upPrev will display a flyout box with a link to the previous post from the same category. Based on upPrev Previous Post Animated Notification by Jason Pelker, Grzegorz Krzyminski
Version: 1.0
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
Licence: BSD
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

/**
 * static options
 */
define( 'IWORKS_UPPREV_VERSION', 'trunk' );
define( 'IWORKS_UPPREV_PREFIX',  'iworks_upprev_' );

/**
 * i18n
 */
load_plugin_textdomain('iworks_upprev', false, dirname( plugin_basename( __FILE__) ).'/languages');

require_once dirname(__FILE__).'/includes/options.php';
require_once dirname(__FILE__).'/includes/common.php';

/**
 * install
 */
#register_activation_hook( __FILE__, 'iworks_upprev_activate' );

/**
 * init
 */
add_action( 'init', 'iworks_upprev_init' );


function iworks_upprev_init()
{
    add_action( 'admin_menu', 'iworks_upprev_add_pages' );
    add_action( 'admin_init', 'iworks_upprev_options_init' );
    add_action( 'wp_footer',  'iworks_upprev_box');
    add_action( 'wp_enqueue_scripts',   'iworks_upprev_enqueue_scripts' );
    add_action( 'wp_print_scripts',     'iworks_upprev_print_scripts' );
}

function iworks_upprev_enqueue_scripts()
{
    if ( !is_single()) {
        return;
    }
    wp_enqueue_script( 'iworks_upprev-js', plugins_url('/scripts/upprev.js', __FILE__), array('jquery'), IWORKS_UPPREV_VERSION );
    $plugin_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
    wp_enqueue_style("upprev-css",$plugin_path.'styles/upprev.css');
}

function iworks_upprev_print_scripts()
{
    if ( !is_single()) {
        return;
    }
    global $iworks_upprev_options;
    $data = '';
    foreach ( array( 'animation', 'position', 'offset_percent', 'offset_element' ) as $key ) {
        if ( $data ) {
            $data .= ','."\n";
        }
        $default = isset($iworks_upprev_options['index']['options'][IWORKS_UPPREV_PREFIX.$key]['default'])? $iworks_upprev_options['index']['options'][IWORKS_UPPREV_PREFIX.$key]['default']:'';
        $value   = get_option(IWORKS_UPPREV_PREFIX.$key, $default );
        $data .= sprintf(
            '%s: %s',
            $key,
            is_numeric($value)? $value:(sprintf("'%s'", $value))
        );
    }
    if ( empty($data) ) {
        return;
    }
    echo '<script type="text/javascript">'."\n";
    echo 'var iworks_upprev = {'."\n";
    echo $data;
    echo "\n".'};'."\n";
    echo '</script>'."\n";
}

function iworks_upprev_add_pages()
{
    if (current_user_can( 'manage_options' ) && function_exists('add_theme_page') ) {
        add_theme_page(
            __('upPrev', 'iworks_upprev'),
            __('upPrev', 'iworks_upprev'),
            'manage_options',
            'upprev/admin/index.php'
        );
    }
}

function iworks_upprev_box()
{
    if ( !is_single() ) {
        return;
    }
    global $post;

        $excerpt_length = get_option( IWORKS_UPPREV_PREFIX.'excerpt_length', 2 );
        $display_thumb  = get_option( IWORKS_UPPREV_PREFIX.'show_thumb', 0 );
        $compare_by     = get_option( IWORKS_UPPREV_PREFIX.'compare', 'simple' );
        $show_taxonomy  = true;

        $args = array(
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array( $post->ID ),
            'posts_per_page' => get_option( IWORKS_UPPREV_PREFIX.'number_of_posts', 1 )
        );

        if ( $compare_by == 'category' ) {
            foreach((get_the_category()) as $one) {
                $siblings[ get_category_link( $one->term_id ) ] = $one->name;
                $ids[] = $one->cat_ID;
            }
            $args['cat'] = implode(',',$ids);
            $count_args = array ( 'include' => $args['cat'] );
        } else if ( $compare_by == 'category' ) {
            foreach((get_the_tags()) as $one) {
                $siblings[ get_tag_link( $one->term_id ) ] = $one->name;
                $ids[] = $one->term_id;
            }
            $args['tag__in'] = $ids;
            $count_args = array ( 'include' => implode(',', $args['tag__in'] ), 'taxonomy' => 'post_tag' );
        } else {
            $show_taxonomy   = false;
        }

        add_filter('excerpt_more',   'iworks_upprev_excerpt_more', 10, 1 );
        add_filter('excerpt_length', 'iworks_upprev_excerpt_length', 10, 1);

        $query = new WP_Query( $args );
        printf(
            '<div id="upprev_box" class="position_%s animation_%s offset_%d">',
            get_option( IWORKS_UPPREV_PREFIX.'position', 'right' ),
            get_option( IWORKS_UPPREV_PREFIX.'animation', 'flyout' ),
            get_option( IWORKS_UPPREV_PREFIX.'offset_percent', 100 )
        );
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<div class="entry"><h6>';
            if ( count( $siblings ) ) {
                printf ( '%s ', __('More in', 'iworks_upprev' ) );
                $a = array();
                foreach ( $siblings as $url => $name ) {
                    $a[] = sprintf( '<a href="%s">%s</a>', $url, $name );
                }
                echo implode( ', ', $a);
            }
            echo '</h6><div class="upprev_excerpt">';
            if ( $display_thumb ) {
                printf(
                    '<a href="%s" title="%s">%s</a>',
                    get_permalink(),
                    wptexturize(get_the_title()),
                    get_the_post_thumbnail( get_the_ID(), array( 48, 48),array('title'=>get_the_title(),'class'=>'iworks_upprev_thumb')  )
                );
            }
            printf(
                '<a href="%s">%s</a>',
                get_permalink(),
                get_the_title()
            );
            if ( $excerpt_length > 0 ) {
                the_excerpt( $excerpt_length );
            }
            echo '</div>';
        }
        printf( '</div><button id="upprev_close" type="button">%s</button></div>', __('Close', 'iworks_upprev') );
        wp_reset_postdata();
        remove_filter('excerpt_more',   'iworks_upprev_excerpt_more', 10, 1 );
        remove_filter('excerpt_length', 'iworks_upprev_excerpt_length', 10, 1);
}

function iworks_upprev_excerpt_more($more)
{
    return '...';
}
function iworks_upprev_excerpt_length($length)
{
    return get_option(IWORKS_UPPREV_PREFIX.'excerpt_length', 20);
}

