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
load_plugin_textdomain( 'upprev', false, dirname( plugin_basename( __FILE__) ).'/languages' );

require_once dirname(__FILE__).'/includes/options.php';
require_once dirname(__FILE__).'/includes/common.php';

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'iworks_upprev_activate' );
register_deactivation_hook( __FILE__, 'iworks_upprev_deactivate' );

/**
 * init
 */
add_action( 'init', 'iworks_upprev_init' );


function iworks_upprev_init()
{
    add_action( 'admin_menu',         'iworks_upprev_add_pages' );
    add_action( 'admin_init',         'iworks_upprev_options_init' );
    add_action( 'wp_footer',          'iworks_upprev_box');
    add_action( 'wp_enqueue_scripts', 'iworks_upprev_enqueue_scripts' );
    add_action( 'wp_print_scripts',   'iworks_upprev_print_scripts' );
    add_action( 'wp_print_styles',    'iworks_upprev_print_styles' );
}

function iworks_upprev_check()
{
    if ( is_single() ) {
        return false;
    }
    if ( is_page() ) {
        return false;
    }
    return true;
}

function iworks_upprev_enqueue_scripts()
{
    if ( iworks_upprev_check() ) {
        return;
    }
    wp_enqueue_script( 'iworks_upprev-js', plugins_url('/scripts/upprev.js', __FILE__), array('jquery'), IWORKS_UPPREV_VERSION );
    $plugin_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
    wp_enqueue_style("upprev-css",$plugin_path.'styles/upprev.css');
}

function iworks_upprev_print_scripts()
{
    if ( iworks_upprev_check() ) {
        return;
    }
    $use_cache = get_option( IWORKS_UPPREV_PREFIX.'use_cache', 1 );
    if ( $use_cache ) {
        $cache_key = IWORKS_UPPREV_PREFIX.'scripts_'.get_the_ID().'_'.get_option(IWORKS_UPPREV_PREFIX.'cache_stamp', '' );
        if ( true === ( $content = get_site_transient( $cache_key ) ) ) {
            print $content;
            return;
        }
    }
    $iworks_upprev_options = iworks_upprev_options();
    $data = '';
    foreach ( array( 'animation', 'position', 'offset_percent', 'offset_element', 'css_width', 'css_side' ) as $key ) {
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
    $content  = '<script type="text/javascript">'."\n";
    $content .= 'var iworks_upprev = {'."\n";
    $content .= $data;
    $content .= "\n".'};'."\n";
    $content .= '</script>'."\n";
    if ( $use_cache ) {
        set_site_transient( $cache_key, $content, get_option( IWORKS_UPPREV_PREFIX.'cache_lifetime', 360 ) );
    }
    echo $content;
}

function iworks_upprev_print_styles()
{
    if ( iworks_upprev_check() ) {
        return;
    }
    $use_cache = get_option( IWORKS_UPPREV_PREFIX.'use_cache', 1 );
    if ( $use_cache ) {
        $cache_key = IWORKS_UPPREV_PREFIX.'style_'.get_the_ID().'_'.get_option(IWORKS_UPPREV_PREFIX.'cache_stamp', '' );
        if ( true === ( $content = get_site_transient( $cache_key ) ) ) {
            print $content;
            return;
        }
    }
    $content = '<style type="text/css">'."\n";
    $content .= '#upprev_box{';
    $values = array();
    foreach ( array( 'position', 'animation' ) as $key ) {
        $values[$key] = get_option(IWORKS_UPPREV_PREFIX.$key, iworks_upprev_get_default_value( 'index', $key) );
    }
    foreach ( array( 'bottom', 'width', 'side' ) as $key ) {
        $values[$key] = get_option(IWORKS_UPPREV_PREFIX.'css_'.$key, iworks_upprev_get_default_value( 'index', 'css_'.$key) );
        switch ( $key ) {
        case 'position':
            break;
        case 'side':
            $content .= sprintf( '%s:%dpx;', $values['position'], $values[ $key ] ) ;
            break;
        default:
            $content .= sprintf( '%s:%dpx;', $key, $values[ $key ] ) ;
        }
    }
    $content .= sprintf ( 'display:%s;', $values['animation'] == 'flyout'? 'block':'none' );
    if ( $values['animation'] == 'flyout' ) {
        $content .= sprintf( '%s:-%dpx;', $values['position'], $values['width'] + $values['side'] + 50 );
    }
    $content .= sprintf ( 'display:%s;', $values['animation'] == 'flyout'? 'block':'none' );
    $content .= '}'."\n";
    $content .= '</style>'."\n";
    if ( $use_cache ) {
        set_site_transient( $cache_key, $content, get_option( IWORKS_UPPREV_PREFIX.'cache_lifetime', 360 ) );
    }
    echo $content;
}

function iworks_upprev_add_pages()
{
    if (current_user_can( 'manage_options' ) && function_exists('add_theme_page') ) {
        add_theme_page(
            __('upPrev', 'upprev'),
            __('upPrev', 'upprev'),
            'manage_options',
            'upprev/admin/index.php'
        );
    }
}

function iworks_upprev_box()
{
    if ( iworks_upprev_check() ) {
        return;
    }
    global $post;
    $use_cache = get_option( IWORKS_UPPREV_PREFIX.'use_cache', 1 );
    if ( $use_cache ) {
        $cache_key = IWORKS_UPPREV_PREFIX.'box_'.get_the_ID().'_'.get_option(IWORKS_UPPREV_PREFIX.'cache_stamp', '' );
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
    $siblings        = array();

    $args = array(
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => array( $post->ID ),
        'posts_per_page' => $number_of_posts,
        'post_type'      => array()
    );
    foreach ( array( 'post', 'page', 'any' ) as $post_type ) {
        if ( get_option( IWORKS_UPPREV_PREFIX.'post_type_'.$post_type, 0 ) == 1 ) {
            $args['post_type'][] = $post_type;
        }
    }
    switch ( $compare_by ) {
        case 'category':
            foreach((get_the_category()) as $one) {
                $siblings[ get_category_link( $one->term_id ) ] = $one->name;
                $ids[] = $one->cat_ID;
            }
            $args['cat'] = implode(',',$ids);
            $count_args = array ( 'include' => $args['cat'] );
            break;
        case 'tag':
            foreach((get_the_tags()) as $one) {
                $siblings[ get_tag_link( $one->term_id ) ] = $one->name;
                $ids[] = $one->term_id;
            }
            $args['tag__in'] = $ids;
            $count_args = array ( 'include' => implode(',', $args['tag__in'] ), 'taxonomy' => 'post_tag' );
            break;
        case 'random':
            $args['orderby'] = 'rand';
            unset($args['order']);
        default:
            $show_taxonomy   = false;
    }

    add_filter( 'posts_where',  'iworks_upprev_filter_where',   72, 1 );
    add_filter( 'excerpt_more', 'iworks_upprev_excerpt_more',   72, 1 );

    if ( $excerpt_length > 0 ) {
        add_filter( 'excerpt_length', 'iworks_upprev_excerpt_length', 72, 1 );
    }

    $query = new WP_Query( $args );

    if (!$query->have_posts()) {
        return;
    }

    remove_all_filters( 'the_content' );

    $value = '<div id="upprev_box">';
    ob_start();
    do_action( 'iworks_upprev_box_before' );
    $value .= ob_get_flush();
    if ( get_option( IWORKS_UPPREV_PREFIX.'header_show', 1 ) ) {
        $value .= '<h6>';
        if ( count( $siblings ) ) {
            $value .= sprintf ( '%s ', __('More in', 'upprev' ) );
            $a = array();
            foreach ( $siblings as $url => $name ) {
                $a[] = sprintf( '<a href="%s">%s</a>', $url, $name );
            }
            $value .= implode( ', ', $a);
        } else if ( $compare_by == 'random' ) {
            $value .= __('Read more:', 'upprev' );
        } else {
            $value .= __('Read next post:', 'upprev' );
        }
        $value .= '</h6>';
    }
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
            $value .= sprintf( '<p>%s</p>', get_the_excerpt() );
        } else if ( $image ) {
            $value .= '<br />';
        }
        $value .= '</div>';
    }
    if ( get_option( IWORKS_UPPREV_PREFIX.'close_button_show', 1 ) == 1 ) {
        $value .= sprintf( '<a id="upprev_close" href="#">%s</a>', __('Close', 'upprev') );
    }
    ob_start();
    do_action( 'iworks_upprev_box_after' );
    $value .= ob_get_flush();
    $value .= '</div>';
    wp_reset_postdata();
    remove_filter( 'excerpt_more',   'iworks_upprev_filter_where', 72, 1 );
    remove_filter( 'excerpt_more',   'iworks_upprev_excerpt_more', 72, 1 );
    if ( $excerpt_length > 0 ) {
        remove_filter( 'excerpt_length', 'iworks_upprev_excerpt_length', 72, 1 );
    }
    if ( $use_cache && $compare_by != 'random' ) {
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

function iworks_upprev_plugin_links ( $links, $file )
{
    if ( $file == plugin_basename(__FILE__) ) {
        $links[] = '<a href="themes.php?page=upprev/admin/index.php">' . __('Settings') . '</a>';
        $links[] = '<a href="http://iworks.pl/donate/upprev.php">' . __('Donate') . '</a>';
    }
    return $links;
}

