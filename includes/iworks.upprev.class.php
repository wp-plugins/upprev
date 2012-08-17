<?php

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

    public function __construct()
    {
        /**
         * static settings
         */
        $this->version    = '1.0';
        $this->base       = dirname( __FILE__ );
        $this->dir        = basename( dirname( $this->base ) );
        $this->capability = apply_filters( 'iworks_upprev_capability', 'manage_options' );
        $this->is_pro     = $this->is_pro();
        /**
         * generate
         */
        add_action( 'init', array( &$this, 'init' ) );
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
         * global option object
         */
        global $iworks_upprev_options;
        /**
         * check post types
         */
        $post_types = $iworks_upprev_options->get_option( 'post_type' );
        if ( $iworks_upprev_options->get_option( 'match_post_type' ) && is_array( $post_types ) ) {
            return !is_singular( $post_types );
        }
        return !is_single();
    }

    private function is_pro()
    {
        return true;
    }

    public function get_version()
    {
        return $this->version;
    }

    public function init()
    {
        add_action( 'admin_enqueue_scripts',      array( &$this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_init',                 'iworks_upprev_options_init' );
        add_action( 'admin_init',                 array( &$this, 'admin_init'         ) );
        add_action( 'admin_menu',                 array( &$this, 'admin_menu'         ) );
        add_action( 'wp_before_admin_bar_render', array( &$this, 'admin_bar'          ) );
        add_action( 'wp_enqueue_scripts',         array( &$this, 'wp_enqueue_scripts' ) );
        add_action( 'wp_footer',                  array( &$this, 'wp_footer' ), PHP_INT_MAX, 0 );
        add_action( 'wp_print_scripts',           array( &$this, 'wp_print_scripts' ) );
        add_action( 'wp_print_styles',            array( &$this, 'wp_print_styles' ) );
        /**
         * filters
         */
        add_filter( 'index_iworks_upprev_position_data', array( &$this, 'index_iworks_upprev_position_data' ) );
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
            wp_enqueue_script( 'upprev-admin-js',  plugins_url( '/scripts/upprev-admin.js', $this->base ), array('jquery-ui-tabs'), IWORKS_UPPREV_VERSION );
            wp_enqueue_style ( 'upprev-admin-css', plugins_url( '/styles/upprev-admin.css', $this->base ), array(),                 IWORKS_UPPREV_VERSION );
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
        $dev = ( defined( 'IWORKS_DEV_MODE' ) && IWORKS_DEV_MODE )? '.dev':'';
        wp_enqueue_script( 'upprev-js',  plugins_url( '/scripts/upprev'.$dev.'.js', $this->base ), array( 'jquery' ), IWORKS_UPPREV_VERSION );
        wp_enqueue_style ( 'upprev-css', plugins_url( '/styles/upprev'.$dev.'.css', $this->base ), array(),           IWORKS_UPPREV_VERSION );
    }

    /**
     * Add page to theme menu
     */
    public function admin_menu()
    {
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
        global $iworks_upprev_options;
        $use_cache = $iworks_upprev_options->get_option( 'use_cache' );
        if ( $use_cache ) {
            $cache_key = IWORKS_UPPREV_PREFIX.'scripts_'.get_the_ID().'_'.$iworks_upprev_options->get_option('cache_stamp');
            if ( true === ( $content = get_site_transient( $cache_key ) ) ) {
                print $content;
                return;
            }
        }
        $data = '';
        foreach ( array( 'animation', 'offset_percent', 'offset_element', 'css_side', 'css_bottom', 'compare', 'url_new_window', 'ga_track_views', 'ga_track_clicks', 'ga_opt_noninteraction' ) as $key ) {
            $value = $iworks_upprev_options->get_option( $key );
            $data .= sprintf(
                '%s: %s, ',
                $key,
                is_numeric($value)? $value:(sprintf("'%s'", $value))
            );
        }
        $postition = $iworks_upprev_options->get_option( 'position' );
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
        $content .= ' };'."\n";
        /**
         * Google Analitics tracking code
         */
        $ga_account = $iworks_upprev_options->get_option( 'ga_account' );
        if ( $ga_account && $iworks_upprev_options->get_option( 'ga_status' )) {
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
            set_site_transient( $cache_key, $content, $iworks_upprev_options->get_option( 'cache_lifetime' ) );
        }
        echo $content;
    }

    public function wp_print_styles()
    {
        if ( $this->iworks_upprev_check() ) {
            return;
        }
        global $iworks_upprev_options;
        $use_cache = $iworks_upprev_options->get_option( 'use_cache' );
        if ( $use_cache ) {
            $cache_key = IWORKS_UPPREV_PREFIX.'style_'.get_the_ID().'_'.$iworks_upprev_options->get_option( 'cache_stamp' );
            if ( true === ( $content = get_site_transient( $cache_key ) ) ) {
                print $content;
                return;
            }
        }
        $content = '<style type="text/css">'."\n";
        $content .= '#upprev_box{';
        $values = array();
        foreach ( array( 'position', 'animation' ) as $key ) {
            $values[$key] = $iworks_upprev_options->get_option( $key );
        }
        foreach ( array( 'bottom', 'width', 'side' ) as $key ) {
            $values[$key] = $iworks_upprev_options->get_option( 'css_'.$key );
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
        $content .= preg_replace( '/\s\s+/s', ' ', preg_replace( '/#[^\{]+ \{ \}/', '', preg_replace( '@/\*[^\*]+\*/@', '', $iworks_upprev_options->get_option( 'css' ) ) ) );
        $content .= '</style>'."\n";
        if ( $use_cache ) {
            set_site_transient( $cache_key, $content, $iworks_upprev_options->get_option( 'cache_lifetime' ) );
        }
        echo $content;
    }

    public function wp_footer()
    {
        if ( $this->iworks_upprev_check() ) {
            return;
        }
        global $post, $iworks_upprev_options;
        $use_cache = $iworks_upprev_options->get_option( 'use_cache' );
        if ( $use_cache ) {
            $cache_key = IWORKS_UPPREV_PREFIX.'box_'.get_the_ID().'_'.$iworks_upprev_options->get_option( 'cache_stamp' );
            if ( true === ( $value = get_site_transient( $cache_key ) ) ) {
                print $value;
                return;
            }
        }

        /**
         * get current post title and convert special characters to HTML entities
         */
        $current_post_title = esc_attr( get_the_title() );

        /**
         * get used params
         */
        foreach( array(
            'compare',
            'excerpt_length',
            'excerpt_show',
            'ignore_sticky_posts',
            'number_of_posts',
            'show_thumb',
            'taxonomy_limit',
            'url_prefix',
            'url_sufix'
        ) as $key ) {
            $$key = $iworks_upprev_options->get_option( $key );
        }

        $show_taxonomy   = true;
        $siblings        = array();

        $args = array(
            'ignore_sticky_posts' => $ignore_sticky_posts,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'post__not_in'        => array( $post->ID ),
            'posts_per_page'      => $number_of_posts,
            'post_type'           => array()
        );
        $post_type = $iworks_upprev_options->get_option( 'post_type' );
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
                    $yarpp_posts = related_entries( $a, $post->ID, false );
                } else if ( array_key_exists( 'post', $post_type ) ) {
                    $yarpp_posts = related_posts( $a, $post->ID, false );
                } else if ( array_key_exists( 'page', $post_type ) ) {
                    $yarpp_posts = related_pages( $a, $post->ID, false );
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
        $value = '<div id="upprev_box">';
        if ( $compare != 'yarpp' ) {
            if ( $compare != 'random' ) {
                add_filter( 'posts_where', array( &$this, 'posts_where' ), 72, 1 );
            }
            add_filter( 'excerpt_more', array( &$this, 'excerpt_more' ), 72, 1 );

            if ( $excerpt_length > 0 ) {
                add_filter( 'excerpt_length', array( &$this, 'excerpt_length' ), 72, 1 );
            }
            $upprev_query = new WP_Query( $args );
            if (!$upprev_query->have_posts()) {
                return;
            }
            /**
             * remove any filter if needed
             */
            if ( $iworks_upprev_options->get_option( 'remove_all_filters' ) ) {
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
            if ( $iworks_upprev_options->get_option( 'header_show' ) ) {
                $header_text = $iworks_upprev_options->get_option( 'header_text' );
                if ( !empty( $header_text ) ) {
                    $title .= $header_text;
                } else if ( count( $siblings ) ) {
                    $title .= sprintf ( '%s ', __('More in', 'upprev' ) );
                    $a = array();
                    foreach ( $siblings as $url => $name ) {
                        $a[] = sprintf( '<a href="%s" rel="%s">%s</a>', $url, $current_post_title, $name );
                    }
                    $title .= implode( ', ', $a);
                } else if ( $compare == 'random' ) {
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
                if ( current_theme_supports('post-thumbnails') && $show_thumb && has_post_thumbnail( get_the_ID() ) ) {
                    $item_class .= ' upprev_thumbnail';
                    $image = sprintf(
                        '<a href="%s" title="%s" class="upprev_thumbnail"%s rel="%s">%s</a>',
                        $permalink,
                        wptexturize(get_the_title()),
                        $ga_click_track,
                        $current_post_title,
                        apply_filters(
                            'iworks_upprev_get_the_post_thumbnail', get_the_post_thumbnail(
                                get_the_ID(),
                                array(
                                    $iworks_upprev_options->get_option( 'thumb_width' ),
                                    9999
                                ),
                                array(
                                    'title'=>get_the_title(),
                                    'class'=>'iworks_upprev_thumb'
                                )
                            )
                        )
                    );
                } else {
                    ob_start();
                    do_action( 'iworks_upprev_image' );
                    $image = ob_get_flush();
                }
                $item .= sprintf( '<div class="%s">%s', $item_class, $image );
                $item .= sprintf(
                    '<h5><a href="%s"%s rel="%s">%s</a></h5>',
                    $permalink,
                    $ga_click_track,
                    $current_post_title,
                    get_the_title()
                );
                if ( $excerpt_show != 0 && $excerpt_length > 0 ) {
                    $item .= sprintf( '<p>%s</p>', get_the_excerpt() );
                } else if ( $image ) {
                    $item .= '<br />';
                }
                $item .= '</div>';
                $value .= apply_filters( 'iworks_upprev_box_item', $item );
            }
        } else {
            $value .= $yarpp_posts;
        }
        if ( $iworks_upprev_options->get_option( 'close_button_show' ) ) {
            $value .= sprintf( '<a id="upprev_close" href="#" rel="close">%s</a>', __('Close', 'upprev') );
        }
        if ( $iworks_upprev_options->get_option( 'promote' ) ) {
            $value .= '<p class="promote"><small>'.__('Previous posts box brought to you by <a href="http://iworks.pl/produkty/wordpress/wtyczki/upprev/en/">upPrev plugin</a>.', 'upprev').'</small></p>';
        }
        ob_start();
        do_action( 'iworks_upprev_box_after' );
        $value .= ob_get_flush();
        $value .= '</div>';
        if ( !$compare != 'yarpp' ) {
            wp_reset_postdata();
            remove_filter( 'posts_where', array( &$this, 'posts_where' ), 72, 1 );
            remove_filter( 'excerpt_more', array( &$this, 'excerpt_more' ), 72, 1 );
            if ( $excerpt_length > 0 ) {
                remove_filter( 'excerpt_length', array( &$this, 'excerpt_length' ), 72, 1 );
            }
        }
        if ( $use_cache && $compare != 'random' ) {
            set_site_transient( $cache_key, $value, $iworks_upprev_options->get_option( 'cache_lifetime' ) );
        }
        echo apply_filters( 'iworks_upprev_box', $value );
    }

    public function posts_where( $where = '' )
    {
        global $post;
        if ($post->post_date) {
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
        global $iworks_upprev_options;
        return $iworks_upprev_options->get_option( 'excerpt_length' );
    }
}

