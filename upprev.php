<?php
/*
Plugin Name: upPrev
Plugin URI: http://iworks.pl/upprev/
Description: When scrolling post down upPrev will display a flyout box with a link to the previous post from the same category. Based on upPrev Previous Post Animated Notification by Jason Pelker, Grzegorz Krzyminski
Version: 2.3
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
Licence: BSD
*/

/**
 * static options
 */
define( 'IWORKS_UPPREV_VERSION', '2.3' );
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
    foreach ( array( 'animation', 'position', 'offset_percent', 'offset_element', 'css_width', 'css_side', 'compare' ) as $key ) {
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
        set_site_transient( $cache_key, $content, iworks_upprev_get_option( 'cache_lifetime' ) );
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
        $values[$key] = get_option(IWORKS_UPPREV_PREFIX.$key, iworks_upprev_get_default_value( $key ) );
    }
    foreach ( array( 'bottom', 'width', 'side' ) as $key ) {
        $values[$key] = get_option(IWORKS_UPPREV_PREFIX.'css_'.$key, iworks_upprev_get_default_value( 'css_'.$key ) );
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
    add_action( 'admin_head-upprev/admin/index.php', 'iworks_upprev_admin_head' );
}

function iworks_upprev_admin_head()
{
        ?>
<style type="text/css">
#hasadmintabs ul.ui-tabs-nav {border-bottom:1px solid #dfdfdf; font-size:12px; height:29px; list-style-image:none; list-style-position:outside; list-style-type:none; margin:13px 0 0; overflow:visible; padding:0 0 0 8px;}
#hasadmintabs ul.ui-tabs-nav li {display:block; float:left; line-height:200%; list-style-image:none; list-style-position:outside; list-style-type:none; margin:0; padding:0; position:relative; text-align:center; white-space:nowrap; width:auto;}
#hasadmintabs ul.ui-tabs-nav li a {background:transparent none no-repeat scroll 0 50%; border-bottom:1px solid #dfdfdf; display:block; float:left; line-height:28px; padding:1px 13px 0; position:relative; text-decoration:none;}
#hasadmintabs ul.ui-tabs-nav li.ui-tabs-selected a{-moz-border-radius-topleft:4px; -moz-border-radius-topright:4px;border:1px solid #dfdfdf; border-bottom-color:#f9f9f9; color:#333333; font-weight:normal; padding:0 12px;}
#hasadmintabs ul.ui-tabs-nav a:focus, a:active {outline-color:-moz-use-text-color; outline-style:none; outline-width:medium;}
</style>
<script type="text/javascript">
/* <![CDATA[ */
    jQuery(function()
    {
        iworks_upprev_tabulator_init();
     });
    /**
     * Tabulator Bootup
     */
    function iworks_upprev_tabulator_init()
    {
        if (!jQuery("#hasadmintabs").length) {
            return;
        }
        jQuery('#hasadmintabs').prepend("<ul><\/ul>");
        jQuery('#hasadmintabs > fieldset').each(function(i){
            id      = jQuery(this).attr('id');
            caption = jQuery(this).find('h3').text();
            jQuery('#hasadmintabs > ul').append('<li><a href="#'+id+'"><span>'+caption+"<\/span><\/a><\/li>");
            jQuery(this).find('h3').hide();
        });
        jQuery("#hasadmintabs").tabs({ selected: <?php echo get_option( IWORKS_UPPREV_PREFIX.'last_used_tab', 0 ); ?> });
        jQuery('#hasadmintabs ul a').click(function(i){
            jQuery('#hasadmintabs input[name=<?php echo IWORKS_UPPREV_PREFIX;?>last_used_tab]').val(jQuery(this).parent().index());
        });
    }
/* ]]> */
</script>
<?php
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
    $iworks_upprev_options = iworks_upprev_options();

    $compare         = iworks_upprev_get_option( 'compare'         );
    $display_thumb   = iworks_upprev_get_option( 'show_thumb'      );
    $excerpt_length  = iworks_upprev_get_option( 'excerpt_show'    );
    $number_of_posts = iworks_upprev_get_option( 'number_of_posts' );
    $taxonomy_limit  = iworks_upprev_get_option( 'taxonomy_limit'  );
    $url_new_window  = iworks_upprev_get_option( 'url_new_window'  );
    $url_prefix      = iworks_upprev_get_option( 'url_prefix'      );
    $url_sufix       = iworks_upprev_get_option( 'url_sufix'       );

    $show_taxonomy   = true;
    $siblings        = array();

    $args = array(
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => array( $post->ID ),
        'posts_per_page' => $number_of_posts,
        'post_type'      => array()
    );
    $post_type = get_option( IWORKS_UPPREV_PREFIX.'post_type', array( 'post' => 'post' ) );
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
            if ( $display_thumb && has_post_thumbnail( get_the_ID() ) ) {
                $item_class .= ' upprev_thumbnail';
                $image = sprintf(
                    '<a href="%s" title="%s" class="upprev_thumbnail"%s>%s</a>',
                    $permalink,
                    wptexturize(get_the_title()),
                    $url_new_window? ' onclick="window.open(this.href);return false;" onkeypress="this.onclick"':'',
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
                '<h5><a href="%s"%s>%s</a></h5>',
                $permalink,
                $url_new_window? ' onclick="window.open(this.href);return false;" onkeypress="this.onclick"':'',
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


function iworks_upprev_add_to_admin_bar()
{
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }
    global $wp_admin_bar;
    $wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'id' => 'upprev', 'title' => __('upPrev', 'upprev'), 'href' => admin_url('themes.php?page=upprev/admin/index.php') ) );
}


