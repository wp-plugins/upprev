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
    add_action( 'admin_enqueue_scripts',      'iworks_upprev_admin_enqueue_scripts' );
    add_action( 'admin_init',                 'iworks_upprev_options_init' );
    add_action( 'admin_menu',                 'iworks_upprev_add_pages' );
    add_action( 'wp_before_admin_bar_render', 'iworks_upprev_add_to_admin_bar' );
    add_action( 'wp_enqueue_scripts',         'iworks_upprev_enqueue_scripts' );
    add_action( 'wp_footer',                  'iworks_upprev_box');
    add_action( 'wp_print_scripts',           'iworks_upprev_print_scripts' );
    add_action( 'wp_print_styles',            'iworks_upprev_print_styles' );
}

function iworks_upprev_check()
{
    if ( is_single() ) {
        if ( iworks_upprev_get_option( 'match_post_type' ) ) {
            return !array_key_exists( get_post_type(), iworks_upprev_get_option( 'post_type' ) );
        }
        return false;
    }
    if ( is_page() ) {
        if ( iworks_upprev_get_option( 'match_post_type' ) ) {
            return !array_key_exists( 'page', iworks_upprev_get_option( 'post_type' ) );
        }
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
    $use_cache = iworks_upprev_get_option( 'use_cache' );
    if ( $use_cache ) {
        $cache_key = IWORKS_UPPREV_PREFIX.'scripts_'.get_the_ID().'_'.get_option(IWORKS_UPPREV_PREFIX.'cache_stamp', '' );
        if ( true === ( $content = get_site_transient( $cache_key ) ) ) {
            print $content;
            return;
        }
    }
    $data = '';
    foreach ( array( 'animation', 'position', 'offset_percent', 'offset_element', 'css_width', 'css_side', 'compare', 'url_new_window' ) as $key ) {
        if ( $data ) {
            $data .= ', ';
        }
        $value   = iworks_upprev_get_option( $key );
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
    $content .= 'var iworks_upprev = { ';
    $content .= $data;
    $content .= ' };'."\n";
    $content .= '</script>'."\n";
    if ( $use_cache ) {
        set_site_transient( $cache_key, $content, iworks_upprev_get_option( 'cache_lifetime' ) );
    }
    echo $content;
}

function iworks_upprev_print_styles()
{
    if ( iworks_upprev_check() ) {
        return;
    }
    $use_cache = iworks_upprev_get_option( 'use_cache' );
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
        $values[$key] = iworks_upprev_get_option( $key );
    }
    foreach ( array( 'bottom', 'width', 'side' ) as $key ) {
        $values[$key] = iworks_upprev_get_option( 'css_'.$key );
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
        set_site_transient( $cache_key, $content, iworks_upprev_get_option( 'cache_lifetime' ) );
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

function iworks_upprev_admin_enqueue_scripts()
{
    wp_enqueue_script( 'upprev', plugins_url('/scripts/upprev-admin.js', __FILE__), array('jquery-ui-tabs'), IWORKS_UPPREV_VERSION );
    wp_enqueue_style( 'upprev', plugins_url('/styles/upprev-admin.css', __FILE__), null, IWORKS_UPPREV_VERSION );
}

function iworks_upprev_box()
{
    if ( iworks_upprev_check() ) {
        return;
    }
    global $post;
    $use_cache = iworks_upprev_get_option( 'use_cache' );
    if ( $use_cache ) {
        $cache_key = IWORKS_UPPREV_PREFIX.'box_'.get_the_ID().'_'.iworks_upprev_get_option( 'cache_stamp' );
        if ( true === ( $value = get_site_transient( $cache_key ) ) ) {
            print $value;
            return;
        }
    }
    $iworks_upprev_options = iworks_upprev_options();

    foreach( array(
        'compare',
        'excerpt_length',
        'excerpt_show',
        'number_of_posts',
        'show_thumb',
        'taxonomy_limit',
        'url_prefix',
        'url_sufix'
    ) as $key ) {
        $$key = iworks_upprev_get_option( $key );
    }

    $show_taxonomy   = true;
    $siblings        = array();

    $args = array(
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => array( $post->ID ),
        'posts_per_page' => $number_of_posts,
        'post_type'      => array()
    );
    $post_type = iworks_upprev_get_option( 'post_type' );
    if ( !empty( $post_type ) ) {
        if ( array_key_exists( 'any', $post_type ) ) {
            $args['post_type'] = 'any';
        } else {
            foreach( $post_type as $type ) {
                $args['post_type'][] = $type;
            }
        }
    }
    if ( $compare == 'yarpp' ) {
        if ( defined( 'YARPP_VERSION' ) && version_compare( YARPP_VERSION, '3.3' ) > -1 ) {
            $a = array();
            if ( array_key_exists( 'post', $post_type ) && array_key_exists( 'page', $post_type ) ) {
                $yarpp_posts = related_entries( $a, false, $post->ID );
            } else if ( array_key_exists( 'post', $post_type ) ) {
                $yarpp_posts = related_posts( $a, false, $post->ID );
            } else if ( array_key_exists( 'page', $post_type ) ) {
                $yarpp_posts = related_pages( $a, false, $post->ID );
            }
            if ( !$yarpp_posts ) {
                return;
            }
        } else {
            $compare = 'simple';
        }
    }
    switch ( $compare ) {
        case 'category':
            $categories = get_the_category();
            if ( count( $categories ) < 1 ) {
                break;
            }
            $max = count( $categories );
            if ( $taxonomy_limit > 0 && $taxonomy_limit > $max ) {
                $max = $taxonomy_limit;
            }
            $ids = array();
            for ( $i = 0; $i < $max; $i++ ) {
                $siblings[ get_category_link( $categories[$i]->term_id ) ] = $categories[$i]->name;
                $ids[] = $categories[$i]->cat_ID;
            }
            $args['cat'] = implode(',',$ids);
            break;
        case 'tag':
            $count_args = array ( 'taxonomy' => 'post_tag' );
            $tags = get_the_tags();
            $max = count( $tags );
            if ( $max < 1 ) {
                break;
            }
            if ( $taxonomy_limit > 0 && $taxonomy_limit > $max ) {
                $max = $taxonomy_limit;
            }
            if ( count( $tags ) ) {
                $ids = array();
                $i = 1;
                foreach( $tags as $tag ) {
                    if ( ++$i > $max ) {
                        continue;
                    }
                    $siblings[ get_tag_link( $tag->term_id ) ] = $tag->name;
                    $ids[] = $tag->term_id;
                }
                $args['tag__in'] = $ids;
            }
            break;
        case 'random':
            $args['orderby'] = 'rand';
            unset($args['order']);
        default:
            $show_taxonomy   = false;
    }
    $value = '<div id="upprev_box">';
    if ( $compare != 'yarpp' ) {
        if ( $compare != 'random' ) {
            add_filter( 'posts_where',  'iworks_upprev_filter_where',   72, 1 );
        }
        add_filter( 'excerpt_more', 'iworks_upprev_excerpt_more',   72, 1 );

        if ( $excerpt_length > 0 ) {
            add_filter( 'excerpt_length', 'iworks_upprev_excerpt_length', 72, 1 );
        }
        $query = new WP_Query( $args );
        if (!$query->have_posts()) {
            return;
        }
        remove_all_filters( 'the_content' );
        ob_start();
        do_action( 'iworks_upprev_box_before' );
        $value .= ob_get_flush();
        if ( iworks_upprev_get_option( 'header_show' ) ) {
            $value .= '<h6>';
            if ( count( $siblings ) ) {
                $value .= sprintf ( '%s ', __('More in', 'upprev' ) );
                $a = array();
                foreach ( $siblings as $url => $name ) {
                    $a[] = sprintf( '<a href="%s">%s</a>', $url, $name );
                }
                $value .= implode( ', ', $a);
            } else if ( $compare == 'random' ) {
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
            $permalink = sprintf(
                '%s%s%s',
                $url_prefix,
                get_permalink(),
                $url_sufix
            );
            if ( current_theme_supports('posts-thumbnails') && $show_thumb && has_post_thumbnail( get_the_ID() ) ) {
                $item_class .= ' upprev_thumbnail';
                $image = sprintf(
                    '<a href="%s" title="%s" class="upprev_thumbnail">%s</a>',
                    $permalink,
                    wptexturize(get_the_title()),
                    get_the_post_thumbnail(
                        get_the_ID(),
                        array(
                            iworks_upprev_get_option( 'thumb_width' ),
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
                $permalink,
                get_the_title()
            );
            if ( $excerpt_length > 0 ) {
                $value .= sprintf( '<p>%s</p>', get_the_excerpt() );
            } else if ( $image ) {
                $value .= '<br />';
            }
            $value .= '</div>';
        }
    } else {
        $value .= $yarpp_posts;
    }
    if ( iworks_upprev_get_option( 'close_button_show' ) ) {
        $value .= sprintf( '<a id="upprev_close" href="#">%s</a>', __('Close', 'upprev') );
    }
    if ( iworks_upprev_get_option( 'promote' ) ) {
        $value .= '<p class="promote"><small>'.__('Next posts box brought to you by <a href="http://iworks.pl/produkty/wordpress/wtyczki/upprev/en/">upPrev plugin</a>.', 'upprev').'</small></p>';
    }
    ob_start();
    do_action( 'iworks_upprev_box_after' );
    $value .= ob_get_flush();
    $value .= '</div>';
    if ( !$compare != 'yarpp' ) {
        wp_reset_postdata();
        remove_filter( 'excerpt_more',   'iworks_upprev_filter_where', 72, 1 );
        remove_filter( 'excerpt_more',   'iworks_upprev_excerpt_more', 72, 1 );
        if ( $excerpt_length > 0 ) {
            remove_filter( 'excerpt_length', 'iworks_upprev_excerpt_length', 72, 1 );
        }
    }
    if ( $use_cache && $compare != 'random' ) {
        set_site_transient( $cache_key, $value, iworks_upprev_get_option( 'cache_lifetime' ) );
    }
    echo apply_filters( 'iworks_upprev_box', $value );
}

function iworks_upprev_excerpt_more($more)
{
    return '...';
}

function iworks_upprev_excerpt_length($length)
{
    return iworks_upprev_get_option( 'excerpt_length' );
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
        if ( !is_multisite() ) {
            $links[] = '<a href="themes.php?page=upprev/admin/index.php">' . __('Settings') . '</a>';
        }
        $links[] = '<a href="http://iworks.pl/donate/upprev.php">' . __('Donate') . '</a>';
    }
    return $links;
}


function iworks_upprev_add_to_admin_bar()
{
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }
    global $wp_admin_bar;
    $wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'upprev', 'title' => __('upPrev', 'upprev'), 'href' => admin_url('themes.php?page=upprev/admin/index.php') ) );
}


