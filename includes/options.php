<?php

$iworks_upprev_options = array();

/**
 * main settings
 */
$iworks_upprev_options['index'] = array(
    'options' => array(
        array(
            'type'              => 'heading',
            'label'             => __('Apperance', 'iworks_upprev' )
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'animation',
            'type'              => 'radio',
            'th'                => __('Animation style', 'upprev' ),
            'default'           => 'flyout',
            'radio'             => array(
                'flyout' => __('flyout', 'iworks_upprev'),
                'fade'   => __('fade in/out', 'iworks_upprev'),
            ),
            'sanitize_callback' => 'esc_html'
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'position',
            'type'              => 'radio',
            'th'                => __('Position', 'upprev' ),
            'default'           => 'right',
            'radio'             => array(
                'right' => __('right', 'iworks_upprev'),
                'left'  => __('left', 'iworks_upprev'),
            ),
            'sanitize_callback' => 'esc_html'
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'offset_percent',
            'type'              => 'text',
            'class'             => 'small-text',
            'th'                => __('Offset', 'iworks_upprev' ),
            'label'             => __('% Percentage of the page required to be scrolled to display a box.', 'iworks_upprev' ),
            'default'           => 100,
            'sanitize_callback' => 'absint'
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'offset_element',
            'type'              => 'text',
            'class'             => 'regular-text',
            'label'             => __('Before HTML element. If empty, all page length is taken for calculation. If not empty, make sure to use the ID or class of an existing element. Put # "hash" before the ID, or . "dot" before a class name.', 'iworks_upprev' ),
            'default'           => '#comments',
            'sanitize_callback' => 'esc_html'
        ),
        /**
         * content
         */
        array(
            'type'              => 'heading',
            'label'             => __('Content', 'iworks_upprev' )
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'number_of_posts',
            'type'              => 'text',
            'class'             => 'small-text',
            'th'                => __('Number of posts to show ', 'iworks_upprev' ),
            'default'           => 1,
            'sanitize_callback' => 'absint'
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'compare',
            'type'              => 'radio',
            'th'                => __('Next post choose method', 'upprev' ),
            'default'           => 'simple',
            'radio'             => array(
                'simple'   => __('Just next.', 'iworks_upprev'),
                'category' => __('Next in category.', 'iworks_upprev'),
                'tag'      => __('Next in tag.', 'iworks_upprev')
            ),
            'sanitize_callback' => 'esc_html'
        ),
        /**
         * excerpt
         */
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'excerpt_show',
            'type'              => 'checkbox',
            'th'                => __('Excerpt', 'iworks_upprev' ),
            'label'             => __('Show excerpt.', 'iworks_upprev'),
            'checked'           => get_option(IWORKS_UPPREV_PREFIX.'excerpt_show', 1) == 1,
            'sanitize_callback' => 'absint'
        ),
        array(
            'name'              => IWORKS_UPPREV_PREFIX.'excerpt_length',
            'type'              => 'text',
            'class'             => 'small-text',
            'default'           => 20,
            'label'             => __('Number of words to show.', 'iworks_upprev' ),
            'sanitize_callback' => 'absint'
        ),
        /**
         * Featured image
         */
        array
        (
            'name'              => IWORKS_UPPREV_PREFIX.'show_thumb',
            'type'              => 'checkbox',
            'th'                => __('Featured image', 'iworks_upprev' ),
            'label'             => __('Show featured image.', 'iworks_upprev'),
            'checked'           => get_option(IWORKS_UPPREV_PREFIX.'show_thumb', 1) == 1,
            'sanitize_callback' => 'absint',
            'check_supports'    => array( 'post-thumbnails' )
        ),
        array
        (
            'name'              => IWORKS_UPPREV_PREFIX.'thumb_width',
            'type'              => 'text',
            'class'             => 'small-text',
            'label'             => __('Featured image width.', 'iworks_upprev'),
            'default'           => 48,
            'sanitize_callback' => 'absint',
            'check_supports'    => array( 'post-thumbnails' )
        ),
        /**
         * cache
         */
        array(
            'type'              => 'heading',
            'label'             => __('Transient Cache', 'iworks_upprev' )
        ),
        array
        (
            'name'              => IWORKS_UPPREV_PREFIX.'use_cache',
            'type'              => 'checkbox',
            'th'                => __('Cache', 'iworks_upprev'),
            'label'             => __('Use Transient Cache.', 'iworks_upprev'),
            'checked'           => get_option(IWORKS_UPPREV_PREFIX.'use_cache', 1) == 1,
            'sanitize_callback' => 'absint'
        ),
        array
        (
            'name'              => IWORKS_UPPREV_PREFIX.'cache_lifetime',
            'type'              => 'text',
            'label'             => __('Transients Cache Lifetime (in seconds, default one hour).', 'iworks_upprev'),
            'default'           => 360,
            'sanitize_callback' => 'absint'
        ),
    ),
);

