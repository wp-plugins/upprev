<?php

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

if ( class_exists( 'IworksUpprev' ) ) {
    return;
}

class IworksUpprev
{
    private static $version;
    private static $dir;
    private static $base;
    private static $capability;
    private static $is_pro;
    private $options;
    private $working_mode;
    private $dev;

    public function __construct()
    {
        /**
         * static settings
         */
        $this->version           = '2.0';
        $this->base              = dirname( __FILE__ );
        $this->dir               = basename( dirname( $this->base ) );
        $this->capability        = apply_filters( 'iworks_upprev_capability', 'manage_options' );
        $this->is_pro            = $this->is_pro();
        $this->working_mode      = 'site';
        $this->dev               = ( defined( 'IWORKS_DEV_MODE' ) && IWORKS_DEV_MODE )? '.dev':'';
        /**
         * layouts settings
         */
        $this->available_layouts = array(
            'simple' => array(
                'name'     => __( 'Default simple layout', 'iworks_upprev' ),
                'defaults' => array(
                    'class'           => 'simple',
                    'number_of_posts' => 1
                )
            ),
            'vertical 3' => array(
                'name'     => __( 'Vertical Three', 'iworks_upprev' ),
                'defaults' => array(
                    'class'           => 'vertical-3',
                    'number_of_posts' => 3,
                    'show_thumb'      => true,
                    'excerpt_show'    => false,
                    'thumb_width'     => 96,
                    'thumb_height'    => 96,
                ),
                'need_pro' => true
            )
        );
        /**
         * generate
         */
        add_action( 'init', array( &$this, 'init' ) );
        add_action( 'after_setup_theme',          array( &$this, 'after_setup_theme'  ) );
        /**
         * global option object
         */
        global $iworks_upprev_options;
        $this->options = $iworks_upprev_options;
    }

    private function iworks_upprev_check()
    {
        if ( !is_singular() ) {
            return true;
        }
        /**
         * check mobile devices
         */
        if ( iworks_upprev_check_mobile_device() ) {
            return true;
        }
        /**
         * check post types
         */
        $post_types = $this->options->get_option( 'post_type' );
        if ( $this->options->get_option( 'match_post_type' ) && is_array( $post_types ) ) {
            return !is_singular( $post_types );
        }
        return !is_single();
    }

    public function is_pro()
    {
//        return false;
        return true;
    }

    public function get_version()
    {
        return ( defined( 'IWORKS_DEV_MODE' ) && IWORKS_DEV_MODE )? rand( 0, 99999 ):$this->version;
    }

    public function init()
    {
        add_action( 'admin_enqueue_scripts',      array( &$this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_init',                 array( &$this, 'admin_init'         ) );
        add_action( 'admin_init',                 'iworks_upprev_options_init' );
        add_action( 'admin_menu',                 array( &$this, 'admin_menu'         ) );
        add_action( 'wp_before_admin_bar_render', array( &$this, 'admin_bar'          ) );
        add_action( 'wp_enqueue_scripts',         array( &$this, 'wp_enqueue_scripts' ) );
        add_action( 'wp_print_scripts',           array( &$this, 'wp_print_scripts'   ) );
        /**
         * filters
         */
        add_filter( 'index_iworks_upprev_position_data', array( &$this, 'index_iworks_upprev_position_data' ) );
    }

    public function after_setup_theme()
    {
        if ( 'simple' == $this->sanitize_layout( $this->options->get_option( 'layout' ) ) ) {
            foreach( $this->available_layouts as $key => $layout ) {
                if( isset( $layout['defaults']['thumb_width'] ) and isset( $layout['defaults']['thumb_height'] ) ) {
                    add_image_size(
                        'iworks-upprev-'.$layout['class'],
                        $layout['defaults']['thumb_width'],
                        $layout['defaults']['thumb_height'],
                        true
                    );
                }
            }
        }
    }

    public function admin_enqueue_scripts()
    {
        $screen = get_current_screen();
        if ( isset( $screen->id ) && $this->dir.'/admin/index' == $screen->id ) {
            /**
             * make help
             */
            $help = '<p>' .  __( '<p>upPrev settings allows you to set the proprites of user notification showed when reader scroll down the page.</p>', 'upprev' ) . '</p>';
            $screen->add_help_tab( array(
                'id'      => 'overview',
                'title'   => __( 'Overview', 'upprev' ),
                'content' => $help,
            ) );
            unset( $help );

            /**
             * make sidebar help
             */
            $screen->set_help_sidebar(
                '<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
                '<p>' . __( '<a href="http://wordpress.org/extend/plugins/upprev/" target="_blank">Plugin Homepage</a>', 'upprev' ) . '</p>' .
                '<p>' . __( '<a href="http://wordpress.org/tags/upprev" target="_blank">Support Forums</a>', 'upprev' ) . '</p>' .
                '<p>' . __( '<a href="http://iworks.pl/en/" target="_blank">break the web</a>', 'upprev' ) . '</p>'
            );

            /**
             * enqueue resources
             */
            wp_enqueue_script( 'upprev-admin-js',  plugins_url( '/scripts/upprev-admin.js', $this->base ), array('jquery-ui-tabs'), $this->get_version() );
            wp_enqueue_style ( 'upprev-admin-css', plugins_url( '/styles/upprev-admin.css', $this->base ), array(),                 $this->get_version() );
            wp_enqueue_style ( 'upprev-css',       plugins_url( '/styles/upprev'.$this->dev.'.css', $this->base ), array(),         $this->get_version() );
        }
    }

    public function admin_bar()
    {
        if ( !current_user_can( $this->capability ) ) {
            return;
        }
        global $wp_admin_bar;
        $wp_admin_bar->add_menu(
            array(
                'parent' => 'appearance',
                'id'     => 'upprev',
                'title'  => __('upPrev', 'upprev'),
                'href'   => admin_url( 'themes.php?page=' . $this->dir . '/admin/index.php' )
            )
        );
    }

    public function wp_enqueue_scripts()
    {
        if ( $this->iworks_upprev_check() ) {
            return;
        }
        wp_enqueue_script( 'upprev-js',  plugins_url( '/scripts/upprev'.$this->dev.'.js', $this->base ), array( 'jquery' ), $this->get_version() );
        wp_enqueue_style ( 'upprev-css', plugins_url( '/styles/upprev'.$this->dev.'.css', $this->base ), array(),           $this->get_version() );
    }

    /**
     * Add page to theme menu
     */
    public function admin_menu()
    {
        if ( preg_match( '/^(vertical 3)$/', $layout ) ) {
            $value .= '<br />';
        }
        add_theme_page( __( 'upPrev', 'upprev' ), __( 'upPrev', 'upprev' ), $this->capability, $this->dir.'/admin/index.php');
    }

    public function admin_init()
    {
        add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
    }

    public function plugin_row_meta( $links, $file )
    {
        if ( $this->dir.'/upprev.php' == $file ) {
            if ( !is_multisite() && current_user_can( $this->capability ) ) {
                $links[] = '<a href="themes.php?page='.$this->dir.'/admin/index.php">' . __('Settings') . '</a>';
            }
            $links[] = '<a href="http://iworks.pl/donate/upprev.php">' . __('Donate') . '</a>';
        }
        return $links;
    }

    public function index_iworks_upprev_position_data( $data )
    {
        if ( !$this->is_pro ) {
            return $data;
        }
        foreach( array( 'right', 'left' ) as $a ) {
            foreach( array( 'top', 'middle' ) as $b ) {
                $data[ $a . '-' . $b ]['disabled'] = false;
            }
        }
        return $data;
    }

    public function wp_print_scripts()
    {
        if ( $this->iworks_upprev_check() ) {
            return;
        }
        $use_cache = $this->options->get_option( 'use_cache' );
        if ( $use_cache ) {
            $cache_key = IWORKS_UPPREV_PREFIX.'scripts_'.get_the_ID().'_'.$this->options->get_option('cache_stamp');
            if ( true === ( $content = get_site_transient( $cache_key ) ) ) {
                print $content;
                return;
            }
        }
        $data = '';
        $params = array(
            'animation',
            'compare',
            'css_bottom',
            'css_side',
            'css_width',
            'ga_opt_noninteraction',
            'ga_track_clicks',
            'ga_track_views',
            'offset_element',
            'offset_percent',
            'url_new_window',
        );
        foreach ( $params as $key ) {
            $value = $this->options->get_option( $key );
            $data .= sprintf(
                '%s: %s, ',
                $key,
                is_numeric($value)? $value:(sprintf("'%s'", $value))
            );
        }
        $postition = $this->options->get_option( 'position' );
        $data .= ' position: { ';
        foreach( array( 'top', 'left', 'center', 'middle' ) as $key ) {
            $re = sprintf( '/%s/', $key );
            $data .= sprintf( '%s: %d, ', $key, preg_match( $re, $postition ) );
        }
        $data .= sprintf( "all: '%s' }, ", $postition );
        /**
         * print
         */
        $content  = '<script type="text/javascript">'."\n";
        $content .= 'var iworks_upprev = { ';
        $content .= $data;
        $content .= 'title: \''.esc_attr( get_the_title() ).'\'';
        $content .= ', url: \''. plugins_url( 'box.php', dirname( __FILE__ ) ).'?p='.get_the_ID().'\'';
        $content .= ' };'."\n";
        /**
         * Google Analitics tracking code
         */
        $ga_account = $this->options->get_option( 'ga_account' );
        if ( $ga_account && $this->options->get_option( 'ga_status' )) {
            $content.= 'var _gaq = _gaq || [];'."\n";
            $content.= '_gaq.push([\'_setAccount\', \''.$ga_account.'\']);'."\n";
            $content.= '(function() {'."\n";
            $content.= '    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;'."\n";
            $content.= '    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';'."\n";
            $content.= '    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);'."\n";
            $content.= '})();'."\n";
        }
        $content .= '</script>'."\n";
        if ( $use_cache ) {
            set_site_transient( $cache_key, $content, $this->options->get_option( 'cache_lifetime' ) );
        }
        echo $content;
    }

    private function get_box( $layout = false )
    {
        if ( 'site' == $this->working_mode ) {
            if ( $this->iworks_upprev_check() ) {
                return;
            }
            $use_cache = $this->options->get_option( 'use_cache' );
            if ( $use_cache ) {
                $cache_key = IWORKS_UPPREV_PREFIX.'box_'.get_the_ID().'_'.$this->options->get_option( 'cache_stamp' );
                if ( true === ( $value = get_site_transient( $cache_key ) ) ) {
                    print $value;
                    return;
                }
            }
        }
        /**
         * get current post title and convert special characters to HTML entities
         */
        $current_post_title = esc_attr( get_the_title() );
        /**
         * set defaults
         */
        $thumb_height = 9999;
        $box_classes = array( 'default' );
        /**
         * get used params
         */
        foreach( array(
            'animation',
            'compare',
            'configuration',
            'excerpt_length',
            'excerpt_show',
            'ignore_sticky_posts',
            'number_of_posts',
            'show_thumb',
            'taxonomy_limit',
            'thumb_width',
            'url_prefix',
            'url_sufix'
        ) as $key ) {
            $$key = $this->options->get_option( $key );
        }
        /**
         * if simple or admin mode setup defaults
         */
        if ( 'simple' == $configuration or 'admin' == $this->working_mode) {
            if ( 'admin' == $this->working_mode ) {
                $compare = 'simple';
            } else {
                $layout = $this->sanitize_layout( $this->options->get_option( 'layout' ) );
            }
            foreach( $this->available_layouts[ $layout ]['defaults'] as $key => $value ) {
                $$key = $value;
            }
            $box_classes[] = $class;
        }
        /**
         * upprev_box class
         */
        $box_classes[] = 'compare-'.$compare;
        $box_classes[] = 'animation-'.$animation;
        /**
         * admin mode?
         */

        $show_taxonomy   = true;
        $siblings        = array();

        $args = array(
            'ignore_sticky_posts' => $ignore_sticky_posts,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'post__not_in'        => array( $post->ID ),
            'posts_per_page'      => $number_of_posts,
            'post_status'         => 'publish',
            'post_type'           => array(),
        );
        $post_type = $this->options->get_option( 'post_type' );
        if ( !empty( $post_type ) ) {
            if ( array_key_exists( 'any', $post_type ) ) {
                $args['post_type'] = 'any';
            } else {
                foreach( $post_type as $type ) {
                    $args['post_type'][] = $type;
                }
            }
        }
        /**
         * exclude categories
         */
        if( $this->is_pro ) {
            $exclude_categories = $this->options->get_option( 'exclude_categories' );
            if ( is_array( $exclude_categories ) && !empty( $exclude_categories ) ) {
                $args[ 'category__not_in' ] = $exclude_categories;
            }
        }
        /**
         * YARPP
         */
        if ( 'yarpp' == $compare ) {
            if ( defined( 'YARPP_VERSION' ) && version_compare( YARPP_VERSION, '3.5' ) > -1 ) {
                if ( !yarpp_related_exist( $args ) ) {
                    return;
                }
                $args['fields'] = 'ids';
                $a = yarpp_get_related( $args );
                $yarpp_posts = array();
                foreach( $a as $b ) {
                    $yarpp_posts[] = $b->ID;
                }
            } else {
                $compare = 'simple';
            }
        }
        /**
         * comparation method
         */
        switch ( $compare ) {
        case 'category':
            $categories = get_the_category();
            if ( !$categories ) {
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
            if ( !$tags ) {
                break;
            }
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
            $show_taxonomy = false;
        }
        $value = sprintf( '<div id="upprev_box" class="%s">', implode( ' ', $box_classes ) ) ;
        if ( 'random' != $compare ) {
            add_filter( 'posts_where', array( &$this, 'posts_where' ), 72, 1 );
        }
        add_filter( 'excerpt_more', array( &$this, 'excerpt_more' ), 72, 1 );
        if ( $excerpt_length > 0 ) {
            add_filter( 'excerpt_length', array( &$this, 'excerpt_length' ), 72, 1 );
        }
        /**
         * YARPP
         */
        if ( 'yarpp' == $compare ) {
            $args = array( 'post__in' => $yarpp_posts );
        }
        $upprev_query = new WP_Query( $args );
        if ( !$upprev_query->have_posts() ) {
            return;
        }
        /**
         * remove any filter if needed
         */
        if ( $this->options->get_option( 'remove_all_filters' ) ) {
            remove_all_filters( 'the_content' );
        }
        /**
         * catch elements
         */
        ob_start();
        do_action( 'iworks_upprev_box_before' );
        $value .= ob_get_flush();
        /**
         * box title
         */
        $title = '';
        if ( $this->options->get_option( 'header_show' ) ) {
            $header_text = $this->options->get_option( 'header_text' );
            if ( !empty( $header_text ) ) {
                $title .= $header_text;
            } else if ( count( $siblings ) ) {
                $title .= sprintf ( '%s ', __('More in', 'upprev' ) );
                $a = array();
                foreach ( $siblings as $url => $name ) {
                    $a[] = sprintf( '<a href="%s" rel="%s">%s</a>', $url, $current_post_title, $name );
                }
                $title .= implode( ', ', $a);
            } else if ( preg_match( '/^(random|yarpp)$/', $compare ) or 'vertical 3' == $layout ) {
                $title .= __( 'Read more:', 'upprev' );
            } else {
                $title .= __( 'Read previous post:', 'upprev' );
            }
        }
        $title = apply_filters( 'iworks_upprev_box_title', $title );
        if ( $title ) {
            $value .= sprintf( '<h6>%s</h6>', $title );
        }
        /**
         *
         */
        $i = 1;
        $ga_click_track = '';
        while ( $upprev_query->have_posts() ) {
            $item = '';
            $upprev_query->the_post();
            $item_class = array();
            if ( $excerpt_show ) {
                $item_class[] = 'upprev_excerpt';
            }
            if ( $i > $number_of_posts ) {
                break;
            }
            if ( !preg_match( '/^(vertical 3)$/', $layout ) ) {
                if ( $i++ < $number_of_posts ) {
                    $item_class[] = 'upprev_space';
                }
            }
            $image = '';
            $permalink = sprintf(
                '%s%s%s',
                $url_prefix,
                get_permalink(),
                $url_sufix
            );
            if ( current_theme_supports('post-thumbnails') && $show_thumb && has_post_thumbnail( get_the_ID() ) ) {
                $a_class = '';
                    if ( !preg_match( '/^(vertical 3)$/', $layout ) ) {
                        $item_class[] = 'upprev_thumbnail';
                        $a_class = 'upprev_thumbnail';
                    }
                    $image = sprintf(
                        '<a href="%s" title="%s" class="%s"%s rel="%s">%s</a>',
                        $permalink,
                        wptexturize(get_the_title()),
                        $a_class,
                        $ga_click_track,
                        $current_post_title,
                        apply_filters(
                            'iworks_upprev_get_the_post_thumbnail', get_the_post_thumbnail(
                                get_the_ID(),
                                array( $thumb_width, $thumb_height ),
                                array(
                                    'title' => get_the_title(),
                                    'class' => 'iworks_upprev_thumb'
                                )
                            )
                        )
                    );
                } else {
                    ob_start();
                    do_action( 'iworks_upprev_image' );
                    $image = ob_get_flush();
                }
                if( empty( $image ) ) {
                    $item_class[] = 'no-image';
                }
                $item .= '<div';
                if ( count( $item ) ) {
                    $item .= sprintf( ' class="%s"', implode( ' ', $item_class ) );
                }
                $item .= '>';
                $item .= $image;
                $item .= sprintf(
                    '<h5><a href="%s"%s rel="%s">%s</a></h5>',
                    $permalink,
                    $ga_click_track,
                    $current_post_title,
                    get_the_title()
                );
                if ( $excerpt_show != 0 && $excerpt_length > 0 ) {
                    $item .= sprintf( '<p>%s</p>', get_the_excerpt() );
                } else if ( $image && !preg_match( '/^(vertical 3)$/', $layout ) ) {
                    $item .= '<br />';
                }
                $item .= '</div>';
                $value .= apply_filters( 'iworks_upprev_box_item', $item );
            }
        if ( $this->options->get_option( 'close_button_show' ) ) {
            $value .= sprintf( '<a id="upprev_close" href="#" rel="close">%s</a>', __('Close', 'upprev') );
        }
        if ( $this->options->get_option( 'promote' ) ) {
            $value .= '<p class="promote"><small>'.__('Previous posts box brought to you by <a href="http://iworks.pl/produkty/wordpress/wtyczki/upprev/en/">upPrev plugin</a>.', 'upprev').'</small></p>';
        }
        $value .= '<br />';
        ob_start();
        do_action( 'iworks_upprev_box_after' );
        $value .= ob_get_flush();
        $value .= '</div>';
        wp_reset_postdata();
        remove_filter( 'posts_where', array( &$this, 'posts_where' ), 72, 1 );
        remove_filter( 'excerpt_more', array( &$this, 'excerpt_more' ), 72, 1 );
        if ( $excerpt_length > 0 ) {
            remove_filter( 'excerpt_length', array( &$this, 'excerpt_length' ), 72, 1 );
        }
        /**
         * cache
         */
        if ( 'site' == $this->working_mode && $use_cache && $compare != 'random' ) {
            set_site_transient( $cache_key, $value, $this->options->get_option( 'cache_lifetime' ) );
        }
        return apply_filters( 'iworks_upprev_box', $value );
    }

    public function posts_where( $where = '' )
    {
        global $post;
        if ( $post->post_date ) {
            $where .= " AND post_date < '" . $post->post_date . "'";
        }
        return $where;
    }

    public function excerpt_more( $more )
    {
        return '...';
    }

    public function excerpt_length( $length )
    {
        return $this->options->get_option( 'excerpt_length' );
    }

    public function the_box()
    {
        echo $this->get_box();
    }

    private function sanitize_layout( $layout )
    {
        if ( array_key_exists( $layout, $this->available_layouts ) ) {
            return $layout;
        }
        return 'simple';
    }

    /**
     * callback: layout
     */
    public function build_layout_chooser( $layout )
    {
        $this->working_mode = 'admin';
        $options = array();
        $set_simple_as_default = false;
        foreach( $this->available_layouts as $key => $one ) {
            $data = array(
                'name'     => $one[ 'name' ],
                'value'    => preg_replace( '/id="upprev_box" class="/', 'class="upprev_box ', $this->get_box( $key ) ),
                'checked'  => $key == $this->sanitize_layout( $layout ),
                'disabled' => false,
            );
            if ( !$this->is_pro && isset( $one['need_pro'] ) && $one['need_pro'] ) {
                $data['disabled'] = true;
                if ( $data['checked'] ) {
                    $data['checked'] = false;
                    $set_simple_as_default = true;
                }
            }
            $options[ $key ] = $data;
        }
        if ( $set_simple_as_default ) {
            $options['simple']['checked'] = true;
        }
        return $options;
    }

    public function update()
    {
        $version = $this->options->get_option( 'version' );
        if ( version_compare( $this->version, $version, '>' ) ) {
            if ( version_compare( $version, '2.0', '<' ) ) {
                $this->options->add_option( 'salt', wp_generate_password( 256, false, false ), false );
            }
            $this->options->update_option( 'version', $this->version );
        }
    }

}

