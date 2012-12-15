<?php

function iworks_upprev_options()
{
    $iworks_upprev_options = array();

    /**
     * main settings
     */
    $iworks_upprev_options['index'] = array(
        'use_tabs' => true,
        'version'  => '0.0',
        'options'  => array(
            array(
                'name'              => 'last_used_tab',
                'type'              => 'hidden',
                'dynamic'           => true,
                'autoload'          => false,
                'default'           => 0
            ),
            array(
                'name'              => 'configuration',
                'type'              => 'special',
                'default'           => 'simple',
                'sanitize_callback' => 'iworks_upprev_sanitize_callback_configuration'
            ),
            /**
             * Appearance: simple
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Appearance', 'upprev' ),
                'configuration'     => 'simple'
            ),
            array(
                'name'              => 'layout',
                'type'              => 'serialize',
                'th'                => __( 'Layout', 'upprev' ),
                'default'           => 'simple',
                'callback'          => 'iworks_upprev_callback_layout',
            ),
            /**
             * Position: simple
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Position', 'upprev' ),
                'configuration'     => 'simple'
            ),
            array(
                'name'              => 'position',
                'type'              => 'radio',
                'th'                => __( 'Position', 'upprev' ),
                'default'           => 'right',
                'radio'             => array(
                    'right'         => array( 'label' => __( 'bottom right', 'upprev' ) ),
                    'left'          => array( 'label' => __( 'bottom left',  'upprev' ) ),
                    'bottom'        => array( 'label' => __( 'bottom',       'upprev' ), 'need_pro' => true ),
                    'right-top'     => array( 'label' => __( 'top right',    'upprev' ), 'need_pro' => true ),
                    'top'           => array( 'label' => __( 'top',          'upprev' ), 'need_pro' => true ),
                    'left-top'      => array( 'label' => __( 'top left',     'upprev' ), 'need_pro' => true ),
                    'right-middle'  => array( 'label' => __( 'middle right', 'upprev' ), 'need_pro' => true ),
                    'left-middle'   => array( 'label' => __( 'middle left',  'upprev' ), 'need_pro' => true )
                ),
                'configuration'     => 'both',
                'sanitize_callback' => 'esc_html'
            ),
            /**
             * Appearance: advance
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Appearance', 'upprev' ),
                'configuration'     => 'advance'
            ),
            array(
                'name'              => 'animation',
                'type'              => 'radio',
                'th'                => __( 'Animation style', 'upprev' ),
                'default'           => 'flyout',
                'radio'             => array(
                    'flyout' => array( 'label' => __( 'flyout', 'upprev' ) ),
                    'fade'   => array( 'label' => __( 'fade in/out', 'upprev' ) ),
                ),
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'position',
                'type'              => 'radio',
                'th'                => __( 'Position', 'upprev' ),
                'default'           => 'right',
                'radio'             => array(
                    'right'         => array( 'label' => __( 'bottom right', 'upprev' ) ),
                    'left'          => array( 'label' => __( 'bottom left',  'upprev' ) ),
                    'bottom'        => array( 'label' => __( 'bottom',       'upprev' ), 'need_pro' => true ),
                    'right-top'     => array( 'label' => __( 'top right',    'upprev' ), 'need_pro' => true ),
                    'top'           => array( 'label' => __( 'top',          'upprev' ), 'need_pro' => true ),
                    'left-top'      => array( 'label' => __( 'top left',     'upprev' ), 'need_pro' => true ),
                    'right-middle'  => array( 'label' => __( 'middle right', 'upprev' ), 'need_pro' => true ),
                    'left-middle'   => array( 'label' => __( 'middle left',  'upprev' ), 'need_pro' => true )
                ),
                'configuration'     => 'both',
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'css_bottom',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __( 'Margin bottom', 'upprev' ),
                'label'             => __( 'px', 'upprev' ),
                'default'           => 5,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'css_side',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __( 'Margin side', 'upprev' ),
                'label'             => __( 'px', 'upprev' ),
                'description'       => __( 'Left or right depending on position.', 'upprev' ),
                'default'           => 5,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'css_width',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __( 'Box width', 'upprev' ),
                'label'             => __( 'px', 'upprev' ),
                'default'           => 360,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'offset_percent',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __( 'Offset', 'upprev' ),
                'label'             => __( '%', 'upprev' ),
                'description'       => __( 'Percentage of the page required to be scrolled to display a box.', 'upprev' ),
                'default'           => 75,
                'sanitize_callback' => 'iworks_upprev_sanitize_callback_offset_percent'
            ),
            array(
                'name'              => 'offset_element',
                'type'              => 'text',
                'class'             => 'regular-text',
                'label'             => __( 'Before HTML element.', 'upprev' ),
                'description'       => __( 'If empty, all page length is taken for calculation. If not empty, make sure to use the ID or class of an existing element. Put # "hash" before the ID, or . "dot" before a class name.', 'upprev' ),
                'default'           => '#comments',
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'header_show',
                'type'              => 'checkbox',
                'th'                => __( 'Box header', 'upprev' ),
                'label'             => __( 'Show box header.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'header_text',
                'type'              => 'text',
                'class'             => 'regular-text',
                'label'             => __( 'Header text.', 'upprev' ),
                'description'       => __( 'Leave blank to allow plugin set the heading text.', 'upprev' ),
                'default'           => false,
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'close_button_show',
                'type'              => 'checkbox',
                'th'                => __( 'Close button', 'upprev' ),
                'label'             => __( 'Show close button.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            /**
             * Colors: both
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Colors', 'upprev' ),
                'configuration'     => 'both',
            ),
            array(
                'name'              => 'color_set',
                'type'              => 'checkbox',
                'th'                => __( 'Set custom colors', 'upprev' ),
                'label'             => __( 'Turn on custom colors.', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint',
                'need_pro'          => true,
            ),
            array(
                'name'              => 'color',
                'type'              => 'wpColorPicker',
                'class'             => 'short-text',
                'th'                => __( 'Text', 'upprev' ),
                'default'           => '#000',
                'sanitize_callback' => 'esc_html',
                'use_name_as_id'    => true,
                'need_pro'          => true,
            ),
            array(
                'name'              => 'color_background',
                'type'              => 'wpColorPicker',
                'class'             => 'short-text',
                'th'                => __( 'Background', 'upprev' ),
                'default'           => '#fff',
                'sanitize_callback' => 'esc_html',
                'use_name_as_id'    => true,
                'need_pro'          => true,
            ),
            array(
                'name'              => 'color_link',
                'type'              => 'wpColorPicker',
                'class'             => 'short-text',
                'th'                => __( 'Links', 'upprev' ),
                'sanitize_callback' => 'esc_html',
                'default'           => '#000',
                'use_name_as_id'    => true,
                'need_pro'          => true,
            ),
            array(
                'name'              => 'color_border',
                'type'              => 'wpColorPicker',
                'class'             => 'short-text',
                'th'                => __( 'Border', 'upprev' ),
                'sanitize_callback' => 'esc_html',
                'default'           => '#000',
                'use_name_as_id'    => true,
                'need_pro'          => true,
            ),
            /**
             * Content: advance
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Content', 'upprev' ),
                'configuration'     => 'advance'
            ),
            array(
                'name'              => 'number_of_posts',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __( 'Number of posts to show', 'upprev' ),
                'description'       => __( 'Not affected if using YARPP as choose method.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'remove_all_filters',
                'type'              => 'checkbox',
                'th'                => __( 'Content filters', 'upprev' ),
                'label'             => __( 'Remove all filters.', 'upprev' ),
                'description'       =>  __( 'Untick this if you have some strange things in upPrev box, but unticked have a lot of chances breaks your layout.' , 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            array(
                'name'              => 'compare',
                'type'              => 'radio',
                'th'                => __( 'Previous entry choose method', 'upprev' ),
                'default'           => 'simple',
                'radio'             => array(
                    'simple'   => array( 'label' => __( 'Just previous.',        'upprev' ) ),
                    'category' => array( 'label' => __( 'Previous in category.', 'upprev' ) ),
                    'tag'      => array( 'label' => __( 'Previous in tag.',      'upprev' ) ),
                    'random'   => array( 'label' => __( 'Random entry.',         'upprev' ) )
                ),
                'sanitize_callback' => 'esc_html',
                'extra_options'    => 'iworks_upprev_get_compare_option'
            ),
            array(
                'name'              => 'taxonomy_limit',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __( 'Taxonomy limit', 'upprev' ),
                'label'             => __( 'Number of taxonomies (tags or categories) to show.', 'upprev' ),
                'description'       => __( 'Default value: 0 (no limit).', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint',
            ),
            array(
                'name'              => 'match_post_type',
                'type'              => 'checkbox',
                'th'                => __( 'Match post type', 'upprev' ),
                'label'             => __( 'Display only for selected post types.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            array(
                'name'              => 'post_type',
                'type'              => 'checkbox_group',
                'th'                => __( 'Select post types', 'upprev' ),
                'label'             => __( 'Show posts.', 'upprev' ),
                'description'       => __( 'If not any, then default value is "post".', 'upprev' ),
                'default'           => array( 'post' => 'post' ),
                'options'           => array(
                    'post' => __( 'Posts.',                                'upprev' ),
                    'page' => __( 'Pages.',                                'upprev' ),
                    'any'  => __( 'Any post type (include custom post types).', 'upprev' ),
                ),
                'extra_options'    => 'iworks_upprev_get_post_types'
            ),
            /**
             * ignore sticky posts to avoid two post loop
             */
            array(
                'name'              => 'ignore_sticky_posts',
                'type'              => 'checkbox',
                'th'                => __( 'Sticky posts', 'upprev' ),
                'label'             => __( 'Ignore sticky posts.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            /**
             * excerpt
             */
            array(
                'name'              => 'excerpt_show',
                'type'              => 'checkbox',
                'th'                => __( 'Excerpt', 'upprev' ),
                'label'             => __( 'Show excerpt.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'excerpt_length',
                'type'              => 'text',
                'class'             => 'small-text',
                'default'           => 20,
                'label'             => __( 'Number of words to show.', 'upprev' ),
                'sanitize_callback' => 'absint'
            ),
            /**
             * Featured image
             */
            array(
                'name'              => 'show_thumb',
                'type'              => 'checkbox',
                'th'                => __( 'Featured image', 'upprev' ),
                'label'             => __( 'Show featured image.', 'upprev' ),
                'sanitize_callback' => 'absint',
                'default'           => 1,
                'check_supports'    => array( 'post-thumbnails' )
            ),
            array(
                'name'              => 'thumb_width',
                'type'              => 'text',
                'class'             => 'small-text',
                'label'             => __( 'Featured image width.', 'upprev' ),
                'default'           => 96,
                'sanitize_callback' => 'absint',
                'check_supports'    => array( 'post-thumbnails' )
            ),
            /**
             * Links: advance
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Links', 'upprev' ),
                'configuration'     => 'advance'
            ),
            array(
                'name'              => 'url_prefix',
                'type'              => 'text',
                'th'                => __( 'URL prefix', 'upprev' ),
                'class'             => 'regular-text',
                'description'       => __( 'Will be added before link.', 'upprev' ),
                'default'           => '',
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'url_suffix',
                'type'              => 'text',
                'th'                => __( 'URL suffix', 'upprev' ),
                'class'             => 'regular-text',
                'description'       => __( 'Will be added after link.', 'upprev' ),
                'default'           => '',
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'url_new_window',
                'type'              => 'checkbox',
                'th'                => __( 'Open link', 'upprev' ),
                'label'             => __( 'Open link in new window.', 'upprev' ),
                'description'       => __( 'Not recommended!', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'ga_status',
                'type'              => 'checkbox',
                'th'                => __( 'Google Analytics', 'upprev' ),
                'label'             => __( 'I don\'t have GA tracking on site.', 'upprev' ),
                'description'       => __( 'Turn it on if you don\'t use any other GA tracking plugin.', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'ga_account',
                'type'              => 'text',
                'label'             => __( 'Google Analytics Account', 'upprev' ),
                'description'       => __( 'Replace UA-XXXXX-X with your web property ID.', 'upprev' ),
                'class'             => 'regular-text',
                'default'           => 'UA-XXXXX-X',
                'sanitize_callback' => 'iworks_upprev_sanitize_callback_ga_account',
                'related_to'        => 'ga_status'
            ),
            array(
                'name'              => 'ga_track_views',
                'type'              => 'checkbox',
                'label'             => __( 'Track views', 'upprev' ),
                'description'       => __( 'Track showing of upPrev box.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'ga_track_clicks',
                'type'              => 'checkbox',
                'label'             => __( 'Track clicks', 'upprev' ),
                'description'       => __( 'Turn it on if you don\'t use any other GA tracking plugin.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'ga_opt_noninteraction',
                'type'              => 'checkbox',
                'label'             => __( 'Prevent bounce-rate', 'upprev' ),
                'description'       => __( 'Turn it on to indicate that the event hit will not be used in bounce-rate calculation.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            /**
             * Advance: css, cache, mobile devices
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Other', 'upprev' ),
                'configuration'     => 'advance'
            ),
            /**
             * Advance: mobile devices
             */
            array(
                'type'              => 'subheading',
                'label'             => __( 'Mobile devices', 'upprev' ),
            ),
            array(
                'name'              => 'mobile_hide',
                'type'              => 'checkbox',
                'th'                => __( 'Mobile devices', 'upprev' ),
                'label'             => __( 'Hide for mobile devices.', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'mobile_tablets',
                'type'              => 'checkbox',
                'th'                => __( 'Tablets', 'upprev' ),
                'label'             => __( 'Hide for tablets too.', 'upprev' ),
                'description'       => __( 'Works only when hiding for mobile devices is turn on.', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint'
            ),
            /**
             * Advance: css
             */
            array(
                'type'              => 'subheading',
                'label'             => __( 'Custom CSS', 'upprev' ),
            ),
            array(
                'name'              => 'css',
                'type'              => 'textarea',
                'class'             => 'large-text code',
                'th'                => __( 'Custom CSS', 'upprev' ),
                'sanitize_callback' => 'esc_html',
                'rows'              => 10,
            /**
             * Advance: cache
             */
            ),
            array(
                'type'              => 'subheading',
                'label'             => __( 'Cache', 'upprev' ),
            ),
            array(
                'name'              => 'use_cache',
                'type'              => 'checkbox',
                'th'                => __( 'Cache', 'upprev' ),
                'label'             => __( 'Use Transient Cache.', 'upprev' ),
                'description'       => __( 'Using on large site (more than 1000 posts) may crash website.', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'iworks_upprev_sanitize_callback_use_cache'
            ),
            array(
                'name'              => 'cache_lifetime',
                'type'              => 'text',
                'label'             => __( 'Transients Cache Lifetime.', 'upprev' ),
                'description'       => __( 'In seconds, default one hour (3600s).', 'upprev' ),
                'default'           => 3600,
                'sanitize_callback' => 'absint'
            ),
            /**
             * promotion
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Promote', 'upprev' ),
                'callback_to_show'  => 'iworks_upprev_callback_is_pro'
            ),
            array(
                'name'              => 'promote',
                'type'              => 'checkbox',
                'th'                => __( 'Promote', 'upprev' ),
                'label'             => __( 'Help promote upPrev plugin?', 'upprev' ),
                'description'       => __( 'This option will add the code <code>Previous posts box brought to you by &lt;a href=\'http://iworks.pl/produkty/wordpress/wtyczki/upprev/en/\'&gt;upPrev plugin&lt;/a&gt;.</code>. Try turning it on, updating your options, and see the code in the code example to the right. These links and donations are greatly appreciated.</span>', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint'
            ),
            array(
                'type'              => 'info',
                'th'                => __( 'Donate', 'upprev' ),
                'value'             => __( 'You can buy me some special coffees if you appreciate my work, thank you! <a href="http://iworks.pl/donate/upprev.php">Donate to this plugin.</a>', 'upprev' ),
                'callback_to_show'  => 'iworks_upprev_callback_is_pro'
            ),
            /**
             * Excludes
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Excludes', 'upprev' ),
                'configuration'     => 'both'
            ),
            array(
                'name'              => 'exclude_categories',
                'type'              => 'serialize',
                'th'                => __( 'Categories', 'upprev' ),
                'sanitize_callback' => 'iworks_upprev_exclude_categories_sanitize_callback',
                'callback'          => 'iworks_upprev_exclude_categories',
            ),
            array(
                'name'              => 'exclude_tags',
                'type'              => 'serialize',
                'th'                => __( 'Tags', 'upprev' ),
                'sanitize_callback' => 'iworks_upprev_exclude_tags_sanitize_callback',
                'callback'          => 'iworks_upprev_exclude_tags',
            ),
            /**
             * buy pro version
             */
            array(
                'type'              => 'heading',
                'label'             => __( 'Buy PRO!', 'upprev' ),
                'configuration'     => 'special',
                'class'             => 'error-message',
                'filter'            => 'iworks_upprev_buy_pro',
                'callback_to_show'  => 'iworks_upprev_callback_is_pro'
            ),
        ),
    );
    return $iworks_upprev_options;
}

function iworks_upprev_get_post_types()
{
    $data = array();
    $post_types = get_post_types( null, 'objects' );
    foreach ( $post_types as $post_type_key => $post_type ) {
        if ( preg_match( '/^(post|page|attachment|revision|nav_menu_item)$/', $post_type_key ) ) {
            continue;
        }
        $data[$post_type_key]  = __( 'Custom post type: ', 'upprev' );
        $data[$post_type_key] .= isset($post_type->labels->name)? $post_type->labels->name:$post_type_key;
        $data[$post_type_key] .= '.';
    }
    return $data;
}

function iworks_upprev_get_compare_option()
{
    $data = array();
    if ( is_plugin_active(plugin_basename( 'yet-another-related-posts-plugin/yarpp.php' ) ) ) {
        $data['yarpp']['label'] = __( 'Related Posts (YARPP)', 'yarpp' );
        $data['yarpp']['label'] .= __( '. Works only with post and/or pages.', 'upprev' );
    } else {
        $data['yarpp-disabled']['label'] = __( 'Related Posts (YARPP)', 'upprev' );
    }
    return $data;
}

/**
 * sanitize offset value
 */
function iworks_upprev_sanitize_callback_offset_percent( $value = null )
{
    if ( is_null( $value ) ) {
        return 100;
    }
    if ( !is_numeric( $value ) || $value < 0 || $value > 100 ) {
        return 75;
    }
    return $value;
}

/**
 * sanitize GA account
 */
function iworks_upprev_sanitize_callback_ga_account( $value = 'UA-XXXXX-X' )
{
    if ( preg_match( '/^UA\-\d{5}\-\d$/i', $value ) ) {
        return strtoupper( $value );
    }
    return null;
}

/**
 * sanitize use_cache
 */
function iworks_upprev_sanitize_callback_use_cache( $value = 0 )
{
    if ( !preg_match( '/^(0|1)$/', $value ) ) {
        $value = 0;
    }
    if ( empty( $value ) ) {
        global $wpdb;
        $query = 'DELETE FROM '.$wpdb->options.' WHERE option_name LIKE \'_site_transient%iworks_upprev_%\'';
        $wpdb->query( $query );
    }
    return $value;
}

/**
 * buy pro page
 */
function iworks_upprev_buy_pro()
{
    global $iworks_upprev;
    return $iworks_upprev->buy_pro_page();
}

/**
 * exclude_categories
 */
function iworks_upprev_exclude_categories( $values = array() )
{
    global $iworks_upprev;
    return $iworks_upprev->build_exclude_categories( $values );
}
function iworks_upprev_exclude_categories_sanitize_callback( $values )
{
    if ( is_array( $values ) ) {
        $ids = array();
        foreach( $values as $id => $value ) {
            $ids[] = $id;
        }
        return $ids;
    }
    return null;
}

/**
 * exclude_tags
 */
function iworks_upprev_exclude_tags( $values = array() )
{
    global $iworks_upprev;
    return $iworks_upprev->build_exclude_tags( $values );
}
function iworks_upprev_exclude_tags_sanitize_callback( $values )
{
    if ( is_array( $values ) ) {
        $ids = array();
        foreach( $values as $id => $value ) {
            $ids[] = $id;
        }
        return $ids;
    }
    return null;
}

/**
 * sanitize_callback: configuration
 */
function iworks_upprev_sanitize_callback_configuration( $option_value )
{
    if ( preg_match( '/^(simple|advance)$/', $option_value ) ) {
        return $option_value;
    }
    return 'simple';
}
/**
 * callback: layout
 */
function iworks_upprev_callback_layout( $value )
{
    global $iworks_upprev;
    return $iworks_upprev->build_layout_chooser( $value );
}
/**
 * callback: donate
 */
function iworks_upprev_callback_is_pro()
{
    global $iworks_upprev;
    return !$iworks_upprev->is_pro();
}

