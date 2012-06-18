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

    private function is_pro()
    {
        return false;
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
        add_action( 'wp_footer',                  'iworks_upprev_box', PHP_INT_MAX, 0 );
        add_action( 'wp_print_scripts',           'iworks_upprev_print_scripts' );
        add_action( 'wp_print_styles',            'iworks_upprev_print_styles' );
        /**
         * filters
         */
        add_filter( 'index_iworks_upprev_position_data', array( &$this, 'index_iworks_upprev_position_data' ) );
    }

    public function admin_enqueue_scripts()
    {
        $screen = get_current_screen();
        if ( isset( $screen->id ) && $screen->id == $this->dir.'/admin/index' ) {
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
        if ( iworks_upprev_check() ) {
            return;
        }
        wp_enqueue_script( 'upprev-js',  plugins_url( '/scripts/upprev.js', $this->base ), array( 'jquery' ), IWORKS_UPPREV_VERSION );
        wp_enqueue_style ( 'upprev-css', plugins_url( '/styles/upprev.css', $this->base ), array(),           IWORKS_UPPREV_VERSION );
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
        if ( $file == $this->dir.'/upprev.php' ) {
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
                $data[ $a . '_' . $b ]['disabled'] = false;
            }
        }
        return $data;
    }
}

