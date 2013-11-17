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

if ( !defined( 'WPINC' ) ) {
    die;
}

if ( class_exists( 'IworksUpprev' ) ) {
    return;
}

class IworksUpprev
{
    private $base;
    private $capability;
    private $dev;
    private $dir;
    private $is_pro;
    private $options;
    private $version;
    private $working_mode;

    public function __construct()
    {
        /**
         * static settings
         */
        $this->version           = '2.0';
        $this->base              = dirname( dirname( __FILE__ ) );
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
                'name'     => __( 'Default simple layout', 'upprev' ),
                'defaults' => array(
                    'class'            => 'simple',
                    'compare'          => 'simple_or_yarpp',
                    'css_border_width' => '2px 0 0 0',
                    'css_bottom'       => 10,
                    'css_side'         => 10,
                    'number_of_posts'  => 1,
                )
            ),
            'vertical 3' => array(
                'name'     => __( 'Vertical Three', 'upprev' ),
                'defaults' => array(
                    'class'            => 'vertical-3',
                    'compare'          => 'simple_or_yarpp',
                    'css_border_width' => '2px 0 0 0',
                    'css_bottom'       => 10,
                    'css_side'         => 10,
                    'excerpt_show'     => false,
                    'make_break'       => false,
                    'number_of_posts'  => 3,
                    'show_thumb'       => true,
                    'thumb_height'     => 96,
                    'thumb_width'      => 96,
                ),
                'need_pro' => true
            ),
            'bloginity' => array(
                'name'     => __( '"Bloginity" style', 'upprev' ),
                'defaults' => array(
                    'class'             => 'bloginity',
                    'compare'           => 'simple_or_yarpp',
                    'css_bottom'        => 0,
                    'css_side'          => 0,
                    'css_width'         => 376,
                    'excerpt_show'      => false,
                    'header_show'       => false,
                    'make_break'        => false,
                    'number_of_posts'   => 4,
                    'show_close_button' => false,
                    'show_thumb'        => true,
                    'thumb_height'      => 84,
                    'thumb_width'       => 84,
                ),
                'need_pro' => true
            )
        );
        /**
         * generate
         */
        add_action( 'init',                  array( &$this, 'init' ) );
        add_action( 'after_setup_theme',     array( &$this, 'after_setup_theme'  ) );
        add_filter( 'iworks_upprev_buy_pro', array( &$this, 'buy_pro_page' ) );
        /**
         * global option object
         */
        global $iworks_upprev_options;
        $this->options = $iworks_upprev_options;
    }

    /**
     * return false to display
     * return true to hide
     */
    private function iworks_upprev_check()
    {
        /**
         * check base and exclude streams
         */
        if ( !is_singular() && 'page' != get_option( 'show_on_front' ) ) {
            return true;
        }
        /**
         * check mobile devices
         */
        if ( 1 == $this->options->get_option( 'mobile_hide' ) || 1 == $this->options->get_option( 'mobile_tablets' ) ) {
            require_once $this->base.'/mobile/detect.php';
            $detect = new Mobile_Detect;
            if ( $detect->isMobile() && !$detect->isTablet() && 1 == $this->options->get_option( 'mobile_hide' ) ) {
                return true;
            }
            if ( $detect->isTablet() && 1 == $this->options->get_option( 'mobile_tablets' ) ) {
                return true;
            }
        }
        /**
         * get allowed post types
         */
        $post_types = $this->options->get_option( 'post_type' );
        /**
         * check front page
         */
        if ( is_front_page() && $this->options->get_option( 'match_post_type' ) && is_array( $post_types ) ) {
            return !in_array( 'post', $post_types );
        }
        /**
         * check post types
         */
        if ( $this->options->get_option( 'match_post_type' ) && is_array( $post_types ) ) {
            return !is_singular( $post_types );
        }
        return !is_single();
    }

    public function is_pro()
    {
        return false;
    }

    public function get_version($file = null)
    {
        if ( defined( 'IWORKS_DEV_MODE' ) && IWORKS_DEV_MODE ) {
            if ( null != $file ) {
                $file = dirname( dirname ( __FILE__ ) ) . $file;
                return md5_file( $file );
            }
            return rand( 0, 99999 );
        }
        return $this->version;
    }

    public function init()
    {
        add_action( 'admin_enqueue_scripts',      array( &$this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_init',                 array( &$this, 'admin_init' ) );
        add_action( 'admin_init',                 'iworks_upprev_options_init' );
        add_action( 'admin_menu',                 array( &$this, 'admin_menu' ) );
        add_action( 'wp_before_admin_bar_render', array( &$this, 'admin_bar' ) );
        add_action( 'wp_enqueue_scripts',         array( &$this, 'wp_enqueue_scripts' ) );
        add_action( 'wp_head',                    array( &$this, 'print_custom_style'), PHP_INT_MAX );
        /**
         * filters
         */
        add_filter( 'index_iworks_upprev_color_background', array( &$this, 'index_iworks_upprev_colors'    ) );
        add_filter( 'index_iworks_upprev_color_border',     array( &$this, 'index_iworks_upprev_colors'    ) );
        add_filter( 'index_iworks_upprev_color_link',       array( &$this, 'index_iworks_upprev_colors'    ) );
        add_filter( 'index_iworks_upprev_color_set',        array( &$this, 'index_iworks_upprev_color_set' ) );
        add_filter( 'index_iworks_upprev_color',            array( &$this, 'index_iworks_upprev_colors'    ) );
        add_filter( 'index_iworks_upprev_position_content', array( &$this, 'index_iworks_upprev_position_content' ), 10, 5 );
        add_filter( 'index_iworks_upprev_position_data',    array( &$this, 'index_iworks_upprev_position_data'    )        );
    }

    public function after_setup_theme()
    {
        if ( 'simple' == $this->sanitize_layout( $this->options->get_option( 'layout' ) ) ) {
            foreach( $this->available_layouts as $key => $layout ) {
                if( isset( $layout['defaults']['thumb_width'] ) and isset( $layout['defaults']['thumb_height'] ) ) {
                    add_image_size(
                        'iworks-upprev-'.$layout['defaults']['class'],
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
            $help = '<p>' .  __( '<p>upPrev settings allows you to set the proprieties of user notification showed when reader scroll down the page.</p>', 'upprev' ) . '</p>';
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
            $scripts = array( 'jquery-ui-tabs' );
            if ( $this->is_pro ) {
                wp_enqueue_style( 'farbtastic' );
                $scripts[] = 'farbtastic';
            }
            wp_enqueue_script( 'upprev-admin-js',  plugins_url( '/scripts/upprev-admin.js', $this->base ), $scripts, $this->get_version() );
            $this->enqueue_style( 'upprev-admin' );
            $this->enqueue_style( 'upprev' );
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
                'title'  => __( 'upPrev', 'upprev' ),
                'href'   => admin_url( 'themes.php?page=' . $this->dir . '/admin/index.php' )
            )
        );
    }

    public function wp_enqueue_scripts()
    {
        if ( $this->iworks_upprev_check() ) {
            return;
        }
        $file = '/scripts/upprev'.$this->dev.'.js';
        wp_enqueue_script( 'upprev-js',  plugins_url( $file, $this->base ), array( 'jquery' ), $this->get_version( $file ) );
        wp_localize_script( 'upprev-js', 'iworks_upprev', $this->get_config_javascript() );
        $this->enqueue_style( 'upprev' );
    }

    /**
     * Add page to theme menu
     */
    public function admin_menu()
    {
        add_theme_page( __( 'upPrev', 'upprev' ), __( 'upPrev', 'upprev' ), $this->capability, $this->dir.'/admin/index.php' );
    }

    public function admin_init()
    {
        add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
    }

    public function plugin_row_meta($links, $file)
    {
        if ( $this->dir.'/upprev.php' == $file ) {
            if ( !is_multisite() && current_user_can( $this->capability ) ) {
                $links[] = '<a href="themes.php?page='.$this->dir.'/admin/index.php">' . __( 'Settings' ) . '</a>';
            }
            if ( !$this->is_pro ) {
                $links[] = '<a href="http://iworks.pl/donate/upprev.php">' . __( 'Donate' ) . '</a>';
            }
        }
        return $links;
    }

    public function index_iworks_upprev_position_data($data)
    {
        if ( $this->is_pro ) {
            return $data;
        }
        foreach( array_keys( $data ) as $key ) {
            if ( isset( $data[ $key ]['need_pro'] ) && $data[ $key ]['need_pro'] ) {
                $data[ $key ]['disabled'] = true;
            }
        }
        return $data;
    }

    private function get_config_javascript()
    {
        $params = array(
            'animation',
            'color_set',
            'compare',
            'css_border_width',
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
        if ( $this->is_pro && $this->options->get_option( 'color_set' ) ) {
            $params = array_merge( $params, array( 'color', 'color_background', 'color_link', 'color_border' ) );
        }
        $defaults = $this->get_default_params();
        foreach ( $params as $key ) {
            $value = isset( $defaults[ $key ] )? $defaults[ $key ] : $this->options->get_option( $key );
            $data[$key] = $value;
        }
        $position = $this->sanitize_position( $this->options->get_option( 'position' ) );
        foreach( array( 'top', 'left', 'center', 'middle' ) as $key ) {
            $re = sprintf( '/%s/', $key );
            $data['position'][$key] = preg_match( $re, $position );
        }
        $data['position']['all'] = $position;
        $data['title'] = esc_attr( get_the_title() );
        $data['url'] = add_query_arg( 'p', get_the_ID(), plugins_url( 'box.php', dirname( __FILE__ ) ) );
        return $data;
    }

    private function get_box($layout = false)
    {
        if ( 'site' == $this->working_mode ) {
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
        $box_classes       = array( 'default' );
        $header_show       = $this->options->get_option( 'header_show' );
        $make_break        = true;
        $show_close_button = $this->options->get_option( 'close_button_show' );
        $thumb_height      = 9999;
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
            'url_suffix'
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
            extract( $this->get_default_params( $layout ) );
            $box_classes[] = $class;
        }
        /**
         * select compare method
         */
        $compare = $this->sanitize_compare( apply_filters( 'iworks_upprev_compare', $compare ) );
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
            'posts_per_page'      => $number_of_posts,
            'post_status'         => 'publish',
            'post_type'           => array(),
        );
        /**
         * exclude one id if singular
         */
        if( isset( $_GET['p'] ) && preg_match( '/^\d+$/', $_GET['p'] ) ) {
            $args['post__not_in'] = array( $_GET['p'] );
        }
        /**
         * check & set post type
         */
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
         * exclude tags
         */
        if( $this->is_pro ) {
            $exclude_tags = $this->options->get_option( 'exclude_tags' );
            if ( is_array( $exclude_tags ) && !empty( $exclude_tags ) ) {
                $args[ 'tag__not_in' ] = $exclude_tags;
            }
        }
        /**
         * comparation method
         */
        switch ( $compare ) {
        /**
         * category
         */
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
            $args['cat'] = implode( ',',$ids );
            break;
        /**
         * tag
         */
        case 'tag':
            $tags = get_the_tags();
            if ( !$tags ) {
                break;
            }
            $max = count( $tags );
            if ( $max < 1 ) {
                break;
            }
            if ( $taxonomy_limit > 0 && $taxonomy_limit < $max ) {
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
        /**
         * random
         */
        case 'random':
            $args['orderby'] = 'rand';
            unset($args['order']);
        /**
         * YARPP
         */
        case 'yarpp':
            if ( !yarpp_related_exist( $args ) ) {
                return;
            }
            $args['limit'] = $number_of_posts;
            $a = yarpp_get_related( $args );
            $yarpp_posts = array();
            foreach( $a as $b ) {
                if ( $b->ID == $post->ID ) {
                    continue;
                }
                $yarpp_posts[] = $b->ID;
            }
            break;
        default:
            $show_taxonomy = false;
        }
        $value = sprintf( '<div id="upprev_box" class="%s">', implode( ' ', $box_classes ) ) ;
        if ( !preg_match( '/^(yarpp|random)$/', $compare ) ) {
            add_filter( 'posts_where', array( &$this, 'posts_where' ), 72, 1 );
        }
        /**
         * YARPP
         */
        if ( 'yarpp' == $compare ) {
            $args = array( 'post__in' => $yarpp_posts, 'ignore_sticky_posts' => 1 );
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
        if ( $header_show ) {
            $header_text = $this->options->get_option( 'header_text' );
            if ( !empty( $header_text ) ) {
                $title .= $header_text;
            } elseif ( count( $siblings ) ) {
                $title .= sprintf ( '%s ', __( 'More in', 'upprev' ) );
                $a = array();
                foreach ( $siblings as $url => $name ) {
                    $a[] = sprintf( '<a href="%s" rel="%s">%s</a>', $url, $current_post_title, $name );
                }
                $title .= implode( ', ', $a);
            } elseif ( preg_match( '/^(random|yarpp)$/', $compare ) or 'vertical 3' == $layout ) {
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
                if ( $i < $number_of_posts ) {
                    $item_class[] = 'upprev_space';
                }
            }
            $image = '';
            $permalink = sprintf(
                '%s%s%s',
                $url_prefix,
                get_permalink(),
                $url_suffix
            );
            if ( current_theme_supports( 'post-thumbnails' ) && $show_thumb && has_post_thumbnail( get_the_ID() ) ) {
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
                            apply_filters(
                                'iworks_upprev_thumbnail_size',
                                array( $thumb_width, $thumb_height )
                            ),
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
                $item .= sprintf( '<p>%s</p>', wp_trim_words( get_the_excerpt(), $this->options->get_option( 'excerpt_length' ), '...' ) );
            } elseif ( $image && $make_break ) {
                $item .= '<br />';
            }
            $item .= '</div>';
            $value .= apply_filters( 'iworks_upprev_box_item', $item );
            $i++;
        }
        if ( $show_close_button ) {
            $value .= sprintf( '<a id="upprev_close" href="#" rel="close">%s</a>', __( 'Close', 'upprev' ) );
        }
        if ( $this->options->get_option( 'promote' ) ) {
            $value .= '<p class="promote"><small>'.__( 'Previous posts box brought to you by <a href="http://iworks.pl/produkty/wordpress/wtyczki/upprev/en/">upPrev plugin</a>.', 'upprev' ).'</small></p>';
        }
        $value .= '<br />';
        ob_start();
        do_action( 'iworks_upprev_box_after' );
        $value .= ob_get_flush();
        $value .= '</div>';
        wp_reset_postdata();
        remove_filter( 'posts_where', array( &$this, 'posts_where' ), 72, 1 );
        /**
         * cache
         */
        if ( 'site' == $this->working_mode && $use_cache && $compare != 'random' ) {
            set_site_transient( $cache_key, $value, $this->options->get_option( 'cache_lifetime' ) );
        }
        return apply_filters( 'iworks_upprev_box', $value );
    }

    public function posts_where($where = '')
    {
        if ( !is_singular() ) {
            return $where;
        }
        global $post;
        if ( $post->post_date ) {
            $where .= " AND post_date < '" . $post->post_date . "'";
        }
        return $where;
    }
    public function the_box()
    {
        echo "\n";
        printf( '<!-- upPrev: %s/%s -->', IWORKS_UPPREV_VERSION, $this->version );
        echo "\n";
        echo $this->get_box();
        $layout = $this->sanitize_layout( $this->options->get_option( 'layout' ) );
        extract( $this->get_default_params( $layout ) );
        if ( !isset( $show_close_button ) || $show_close_button ) {
            echo '<a id="upprev_rise">&clubs;</a>';
        }
    }

    private function sanitize_layout($layout)
    {
        if ( array_key_exists( $layout, $this->available_layouts ) ) {
            if ( $this->is_pro ) {
                return $layout;
            }
            if ( isset( $this->available_layouts[ $layout ][ 'need_pro' ] ) && $this->available_layouts[ $layout ][ 'need_pro' ] ) {
                return 'simple';
            }
            return $layout;
        }
        return 'simple';
    }

    /**
     * callback: layout
     */
    public function build_layout_chooser($layout)
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
        $content = '<ul>';
        foreach( $options as $key => $one ) {
            $id = 'iworks_upprev_'.crc32( $key );
            $content .= sprintf(
                '<li><input type="radio" name="iworks_upprev_layout" value="%s"%s%s id="%s"><label for="%s"> %s</label>',
                $key,
                $one['checked']? ' checked="checked"':'',
                $one['disabled']? ' disabled="disabled"':'',
                $id,
                $id,
                $one['name']
            );
            $content .= $one['value'];
            $content .= '</li>';
        }
        $content .= '</ul>';
        return $content;
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

    private function position_one_radio($value, $input, $html_element_name, $option_name, $option_value)
    {
        $option_value = $this->sanitize_position( $option_value );
        $id = $option_name.'-'.$value;
        $disabled = '';
        if ( isset( $input['disabled'] ) && $input['disabled'] ) {
            $disabled = 'disabled="disabled"';
        }
        return sprintf (
            '<td class="%s%s"><label for="%s" class="imgedit-group"><input type="radio" name="%s" value="%s"%s id="%s" %s/> <span>%s</span></label></td>',
            sanitize_title( $value ),
            $disabled? ' disabled':'',
            $id,
            $html_element_name,
            $value,
            ($option_value == $value or ( empty($option_value) and isset($option['default']) and $value == $option['default'] ) )? ' checked="checked"':'',
            $id,
            $disabled,
            $input['label']
        );
    }

    public function index_iworks_upprev_position_content($content, $data, $html_element_name, $option_name, $option_value)
    {
        $content = '';
        if ( !$this->is_pro ) {
            $content .= '<p class="error-message">'.__( 'All positions are available in PRO version!', 'upprev' ).'</p>';
        }
        $content .= sprintf( '<table id="%s"><tbody><tr>', $html_element_name );
        foreach( array( 'left-top', 'top', 'right-top' ) as $key ) {
            $content .= $this->position_one_radio( $key, $data[$key], $html_element_name, $option_name, $option_value );
        }
        $content .= '</tr><tr>';
        $key = 'left-middle';
        $content .= $this->position_one_radio( $key, $data[$key], $html_element_name, $option_name, $option_value );
        $content .= '<td>&nbsp;</td>';
        $key = 'right-middle';
        $content .= $this->position_one_radio( $key, $data[$key], $html_element_name, $option_name, $option_value );
        $content .= '</tr><tr>';
        foreach( array( 'left', 'bottom', 'right' ) as $key ) {
            $content .= $this->position_one_radio( $key, $data[$key], $html_element_name, $option_name, $option_value );
        }
        $content .= '</tr><tr>';
        $content .= '</tr></tbody></table>';
        return $content;
    }

    public function index_iworks_upprev_colors($content)
    {
        if ( !$this->is_pro ) {
            return $content;
        }
        return preg_replace( '/ disabled="disabled"/', '', $content );
    }

    public function index_iworks_upprev_color_set($content)
    {
        if ( !$this->is_pro ) {
            return '<p class="error-message">'.__( 'Colors setup is available in PRO version!', 'upprev' ).'</p>'.$content;
        }
        return preg_replace( '/ disabled="disabled"/', '', $content );
    }

    public function print_custom_style()
    {
        if ( $this->iworks_upprev_check() ) {
            return;
        }
        $content = '<style type="text/css">'.PHP_EOL;
        $content .= preg_replace( '/\s\s+/s', ' ', preg_replace( '/#[^\{]+ \{ \}/', '', preg_replace( '@/\*[^\*]+\*/@', '', $this->options->get_option( 'css' ) ) ) );
        $content .= '</style>'.PHP_EOL;
        echo $content;
    }

    private function get_default_params($layout = null)
    {
        if ( null == $layout ) {
            $layout = $this->sanitize_layout( $this->options->get_option( 'layout' ) );
        }
        if ( isset( $this->available_layouts[ $layout ] ) && isset( $this->available_layouts[ $layout ]['defaults'] ) && is_array( $this->available_layouts[ $layout ]['defaults'] ) ) {
            return $this->available_layouts[ $layout ]['defaults'];
        }
        return array();
    }

    private function enqueue_style($name, $deps = null)
    {
        $file = '/styles/'.$name.$this->dev.'.css';
        wp_enqueue_style ( $name, plugins_url( $file, $this->base ), $deps, $this->get_version( $file ) );
    }

    private function sanitize_position($position)
    {
        $positions = $this->options->get_values( 'position' );
        if ( $this->is_pro && array_key_exists( $position, $positions ) ) {
            return $position;
        }
        if ( isset( $positions[ $position ] ) && ( !isset( $positions[ $position ][ 'need_pro' ] ) || isset( $positions[ $position ][ 'need_pro' ] ) && !$positions[ $position ][ 'need_pro' ] ) ) {
            return $position;
        }
        return 'right';
    }

    /**
     * sanitize_compare
     */
    private function sanitize_compare($compare)
    {
        if ( !preg_match( '/^(simple|category|tag|random|yarpp|simple_or_yarpp)$/', $compare ) ) {
            return 'simple';
        }
        if ( preg_match( '/^(simple_or_yarpp|yarpp)$/', $compare ) ) {
            if ( defined( 'YARPP_VERSION' ) && version_compare( YARPP_VERSION, '3.5' ) > -1 ) {
                return 'yarpp';
            }
            return 'simple';
        }
        return $compare;
    }

    /**
     * exclude categories
     */
    public function build_exclude_categories($values)
    {
        $args = array(
            'hide_empty'   => false,
            'hierarchical' => false,
        );
        $content = '';
        if ( !$this->is_pro ) {
            $args['number'] = 3;
            $content .= '<li class="error-message">'.__( 'Exclude categories available in PRO version!', 'upprev' ).'</li>';
        }
        $categories = get_categories( $args );
        foreach ( $categories as $category ) {
            $id = sprintf( 'category_%04d', $category->term_id );
            $content .= sprintf(
                '<li><input type="checkbox" name="iworks_upprev_exclude_categories[%d]" id="%s"%s%s /><label for="%s"> %s <small>(%d)</small></label></li>',
                $category->term_id,
                $id,
                is_array( $values ) && in_array( $category->term_id, $values )? ' checked="checked"':'',
                $this->is_pro? '':' disabled="disabled"',
                $id,
                $category->name,
                $category->count
            );
        }
        if ( !$this->is_pro ) {
            $content .= '<li>...</li>';
        }
        return '<ul>'.$content.'</li>';
    }

    /**
     * exclude tags
     */
    public function build_exclude_tags($values)
    {
        $args = array(
            'hide_empty'   => false,
            'hierarchical' => false,
        );
        $content = '';
        if ( !$this->is_pro ) {
            $args['number'] = 3;
            $content .= '<li class="error-message">'.__( 'Exclude tags available in PRO version!', 'upprev' ).'</li>';
        }
        $tags = get_tags( $args );
        foreach ( $tags as $category ) {
            $id = sprintf( 'category_%04d', $category->term_id );
            $content .= sprintf(
                '<li><input type="checkbox" name="iworks_upprev_exclude_tags[%d]" id="%s"%s%s /><label for="%s"> %s <small>(%d)</small></label></li>',
                $category->term_id,
                $id,
                is_array( $values ) && in_array( $category->term_id, $values )? ' checked="checked"':'',
                $this->is_pro? '':' disabled="disabled"',
                $id,
                $category->name,
                $category->count
            );
        }
        if ( !$this->is_pro ) {
            $content .= '<li>...</li>';
        }
        return '<ul>'.$content.'</li>';
    }

    /**
     * Buy PRO page
     */
    public function buy_pro_page($content = '')
    {
        if ( $this->is_pro ) {
            return;
        }
        return include dirname( $this->base ).'/admin/buy_pro.php';
    }

    /**
     * Buy PRO link
     */
    public function link_buy($type = 'this-site')
    {
        $params = array();
        $link = 'http://upprev.com/buy/';
        if ( defined( 'WPLANG' ) && WPLANG ) {
            $params['lang'] = WPLANG;
        }
        $params['admin_email'] = get_option( 'admin_email' );
        $params['home_url']    = home_url();
        $params['language']    = get_option( 'language' );
        $params['type']        = $type;
        $params['version']     = get_option( 'version' );
        echo add_query_arg( 'iworks_upprev', urlencode( base64_encode( gzcompress( serialize( $params ) ) ) ), $link );
    }
}
