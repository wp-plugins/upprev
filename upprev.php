<?php
/*
Plugin Name: upPrev
Plugin URI: http://iworks.pl/upprev/
Description: When scrolling post down upPrev will display a flyout box with a link to the previous post from the same category. Based on upPrev Previous Post Animated Notification by Jason Pelker, Grzegorz Krzyminski
Version: trunk
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
Licence: BSD
*/

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
    add_image_size(
        'upPrev',
        get_option( IWORKS_UPPREV_PREFIX.'thumb_width',  48 ),
        get_option( IWORKS_UPPREV_PREFIX.'thumb_height', 48 ),
        true
    );
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

    $use_cache = get_option( IWORKS_UPPREV_PREFIX.'use_cache', 1 );

    if ( $use_cache ) {
        $cache_key = IWORKS_UPPREV_PREFIX.'cache_'.get_the_ID();
        if ( true === ( $value = get_site_transient( $cache_key ) ) ) {
            print $value;
            return;
        }
    }

    $excerpt_length  = get_option( IWORKS_UPPREV_PREFIX.'excerpt_show', 1 )? get_option( IWORKS_UPPREV_PREFIX.'excerpt_length', 20 ):0;
    $display_thumb   = get_option( IWORKS_UPPREV_PREFIX.'show_thumb', 0 ) && current_theme_supports( 'post-thumbnails' );
    $compare_by      = get_option( IWORKS_UPPREV_PREFIX.'compare', 'simple' );
    $number_of_posts = get_option( IWORKS_UPPREV_PREFIX.'number_of_posts', 1 );

    $show_taxonomy   = true;
    $siblings = array();

    $args = array(
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => array( $post->ID ),
        'posts_per_page' => $number_of_posts
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

    add_filter( 'posts_where',    'iworks_upprev_filter_where',   99, 1 );
    add_filter( 'excerpt_more',   'iworks_upprev_excerpt_more',   10, 1 );
    if ( $excerpt_length > 0 ) {
        add_filter( 'excerpt_length', 'iworks_upprev_excerpt_length', 10, 1 );
    }

    $query = new WP_Query( $args );

    if (!$query->have_posts()) {
        return;
    }

    $value = sprintf(
        '<div id="upprev_box" class="position_%s animation_%s offset_%d">',
        get_option( IWORKS_UPPREV_PREFIX.'position', 'right' ),
        get_option( IWORKS_UPPREV_PREFIX.'animation', 'flyout' ),
        get_option( IWORKS_UPPREV_PREFIX.'offset_percent', 100 )
    );
    $value .= '<h6>';
    if ( count( $siblings ) ) {
        $value .= sprintf ( '%s ', __('More in', 'iworks_upprev' ) );
        $a = array();
        foreach ( $siblings as $url => $name ) {
            $a[] = sprintf( '<a href="%s">%s</a>', $url, $name );
        }
        $value .= implode( ', ', $a);
    } else {
        $value .= __('Read next post:', 'iworks_upprev' );
    }
    $value .= '</h6>';
    $i = 1;
    while ( $query->have_posts() ) {
        $query->the_post();
        $item_class = 'upprev_excerpt';
        if ( $i > $number_of_posts ) {
            break;
        }
        if ( $i++ < $number_of_posts ) {
            $item_class .= ' upprev_space';
        }
        $image = '';
        if ( $display_thumb && has_post_thumbnail( get_the_ID() ) ) {
            $item_class .= ' upprev_thumbnail';
            $image = sprintf(
                '<a href="%s" title="%s" class="upprev_thumbnail">%s</a>',
                get_permalink(),
                wptexturize(get_the_title()),
                get_the_post_thumbnail(
                    get_the_ID(),
                    array(
                        get_option( IWORKS_UPPREV_PREFIX.'thumb_width',  48 ),
                        9999
                    ),
                    array(
                        'title'=>get_the_title(),
                        'class'=>'iworks_upprev_thumb'
                    )
                )
            );
        }
        $value .= sprintf( '<div class="%s">%s', $item_class, $image );
        $value .= sprintf(
            '<h5><a href="%s">%s</a></h5>',
            get_permalink(),
            get_the_title()
        );
        if ( $excerpt_length > 0 ) {
            $value .= get_the_excerpt();
        }
        if ( $image ) {
            $value .= '<br />';
        }
        $value .= '</div>';
    }
    $value .= sprintf( '<a id="upprev_close" href="#">%s</a></div>', __('Close', 'iworks_upprev') );
    wp_reset_postdata();
    remove_filter( 'excerpt_more',   'iworks_upprev_filter_where',   99, 1 );
    remove_filter( 'excerpt_more',   'iworks_upprev_excerpt_more',   10, 1 );
    if ( $excerpt_length > 0 ) {
        remove_filter( 'excerpt_length', 'iworks_upprev_excerpt_length', 10, 1 );
    }
    if ( $use_cache ) {
        set_site_transient( $cache_key, $value, get_option( IWORKS_UPPREV_PREFIX.'cache_lifetime', 360 ) );
    }
    echo $value;
}

function iworks_upprev_excerpt_more($more)
{
    return '...';
}

function iworks_upprev_excerpt_length($length)
{
    return get_option(IWORKS_UPPREV_PREFIX.'excerpt_length', 20);
}

function iworks_upprev_filter_where( $where = '' )
{
    global $post;
    $where .= " AND post_date < '" . $post->post_date . "'";
    return $where;
}
