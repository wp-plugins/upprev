<?php

function iworks_upprev_options()
{
    $iworks_upprev_options = array();

    /**
     * main settings
     */
    $iworks_upprev_options['index'] = array(
        'options' => array(
            array(
                'type'              => 'heading',
                'label'             => __('Apperance', 'upprev' )
            ),
            array(
                'name'              => 'animation',
                'type'              => 'radio',
                'th'                => __('Animation style', 'upprev' ),
                'default'           => 'flyout',
                'radio'             => array(
                    'flyout' => __('flyout', 'upprev'),
                    'fade'   => __('fade in/out', 'upprev'),
                ),
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'position',
                'type'              => 'radio',
                'th'                => __('Position', 'upprev' ),
                'default'           => 'right',
                'radio'             => array(
                    'right' => __('right', 'upprev'),
                    'left'  => __('left', 'upprev'),
                ),
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'css_bottom',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Margin bottom', 'upprev' ),
                'label'             => __('px', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'css_side',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Margin side', 'upprev' ),
                'label'             => __('px', 'upprev' ),
                'description'       => __('Left or right depending on position.', 'upprev' ),
                'default'           => 0,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'css_width',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Box width', 'upprev' ),
                'label'             => __('px', 'upprev' ),
                'default'           => 360,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'offset_percent',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Offset', 'upprev' ),
                'label'             => __('%', 'upprev' ),
                'description'       => __('Percentage of the page required to be scrolled to display a box.', 'upprev' ),
                'default'           => 75,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'offset_element',
                'type'              => 'text',
                'class'             => 'regular-text',
                'label'             => __('Before HTML element.', 'upprev' ),
                'description'       => __('If empty, all page length is taken for calculation. If not empty, make sure to use the ID or class of an existing element. Put # "hash" before the ID, or . "dot" before a class name.', 'upprev' ),
                'default'           => '#comments',
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'header_show',
                'type'              => 'checkbox',
                'th'                => __('Box header', 'upprev' ),
                'label'             => __('Show boks header.', 'upprev'),
                'checked'           => get_option('header_show', 1) == 1,
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'close_button_show',
                'type'              => 'checkbox',
                'th'                => __('Close button', 'upprev' ),
                'label'             => __('Show close button.', 'upprev'),
                'checked'           => get_option('close_button_show', 1) == 1,
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            /**
             * content
             */
            array(
                'type'              => 'heading',
                'label'             => __('Content', 'upprev' )
            ),
            array(
                'name'              => 'number_of_posts',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Number of posts to show ', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'compare',
                'type'              => 'radio',
                'th'                => __('Next post choose method', 'upprev' ),
                'default'           => 'simple',
                'radio'             => array(
                    'simple'   => __( 'Just next.',        'upprev' ),
                    'category' => __( 'Next in category.', 'upprev' ),
                    'tag'      => __( 'Next in tag.',      'upprev' ),
                    'random'   => __( 'Random.',           'upprev' )
                ),
                'sanitize_callback' => 'esc_html'
            ),
            array
            (
                'name'              => 'taxonomy_limit',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Taxonomy limit', 'upprev' ),
                'label'             => __('Number of taxonomies (tags or categories) to show.', 'upprev' ),
                'description'       => __('Default value: 0 (no limit).', 'upprev'),
                'default'           => 0,
                'sanitize_callback' => 'absint',
            ),
            array
            (
                'name'              => 'post_type',
                'type'              => 'radio',
                'th'                => __('Select post types', 'upprev' ),
                'label'             => __('Show posts.', 'upprev' ),
                'description'       => __('If not any, then default value is "post".', 'upprev'),
                'default'           => 'post',
                'radio'             => array(
                    'post' => __( 'Only posts.',                                'upprev' ),
                    'page' => __( 'Only pages.',                                'upprev' ),
                    'any'  => __( 'Any post type (include custom post types).', 'upprev' ),
                ),
                'extra_options'    => 'iworks_upprev_get_post_types'
            ),
            /**
             * excerpt
             */
            array(
                'name'              => 'excerpt_show',
                'type'              => 'checkbox',
                'th'                => __('Excerpt', 'upprev' ),
                'label'             => __('Show excerpt.', 'upprev'),
                'checked'           => get_option('excerpt_show', 1) == 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'excerpt_length',
                'type'              => 'text',
                'class'             => 'small-text',
                'default'           => 20,
                'label'             => __('Number of words to show.', 'upprev' ),
                'sanitize_callback' => 'absint'
            ),
            /**
             * Featured image
             */
            array
            (
                'name'              => 'show_thumb',
                'type'              => 'checkbox',
                'th'                => __('Featured image', 'upprev' ),
                'label'             => __('Show featured image.', 'upprev'),
                'checked'           => get_option('show_thumb', 1) == 1,
                'sanitize_callback' => 'absint',
                'check_supports'    => array( 'post-thumbnails' )
            ),
            array
            (
                'name'              => 'thumb_width',
                'type'              => 'text',
                'class'             => 'small-text',
                'label'             => __('Featured image width.', 'upprev'),
                'default'           => 48,
                'sanitize_callback' => 'absint',
                'check_supports'    => array( 'post-thumbnails' )
            ),
            /**
             * cache
             */
            array(
                'type'              => 'heading',
                'label'             => __('Transient Cache', 'upprev' )
            ),
            array
            (
                'name'              => 'use_cache',
                'type'              => 'checkbox',
                'th'                => __('Cache', 'upprev'),
                'label'             => __('Use Transient Cache.', 'upprev'),
                'checked'           => get_option('use_cache', 1) == 1,
                'sanitize_callback' => 'absint'
            ),
            array
            (
                'name'              => 'cache_lifetime',
                'type'              => 'text',
                'label'             => __('Transients Cache Lifetime.', 'upprev' ),
                'description'       => __('In seconds, default one hour (360s).', 'upprev'),
                'default'           => 360,
                'sanitize_callback' => 'absint'
            ),
        ),
    );
    return $iworks_upprev_options;
}
