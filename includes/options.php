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
                'name'              => IWORKS_UPPREV_PREFIX.'animation',
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
                'name'              => IWORKS_UPPREV_PREFIX.'position',
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
                'name'              => IWORKS_UPPREV_PREFIX.'width',
                'type'              => 'radio',
                'th'                => __('Length', 'upprev' ),
                'default'           => 400,
                'radio'             => array(
                    200 => __('200px', 'upprev'),
                    300 => __('300px', 'upprev'),
                    400 => __('400px', 'upprev'),
                    500 => __('500px', 'upprev'),
                    600 => __('600px', 'upprev'),
                ),
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => IWORKS_UPPREV_PREFIX.'offset_percent',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Offset', 'upprev' ),
                'label'             => __('% Percentage of the page required to be scrolled to display a box.', 'upprev' ),
                'default'           => 75,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => IWORKS_UPPREV_PREFIX.'offset_element',
                'type'              => 'text',
                'class'             => 'regular-text',
                'label'             => __('Before HTML element. If empty, all page length is taken for calculation. If not empty, make sure to use the ID or class of an existing element. Put # "hash" before the ID, or . "dot" before a class name.', 'upprev' ),
                'default'           => '#comments',
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => IWORKS_UPPREV_PREFIX.'header_show',
                'type'              => 'checkbox',
                'th'                => __('Box header', 'upprev' ),
                'label'             => __('Show boks header.', 'upprev'),
                'checked'           => get_option(IWORKS_UPPREV_PREFIX.'header_show', 1) == 1,
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
                'name'              => IWORKS_UPPREV_PREFIX.'number_of_posts',
                'type'              => 'text',
                'class'             => 'small-text',
                'th'                => __('Number of posts to show ', 'upprev' ),
                'default'           => 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => IWORKS_UPPREV_PREFIX.'compare',
                'type'              => 'radio',
                'th'                => __('Next post choose method', 'upprev' ),
                'default'           => 'simple',
                'radio'             => array(
                    'simple'   => __('Just next.', 'upprev'),
                    'category' => __('Next in category.', 'upprev'),
                    'tag'      => __('Next in tag.', 'upprev')
                ),
                'sanitize_callback' => 'esc_html'
            ),
            /**
             * excerpt
             */
            array(
                'name'              => IWORKS_UPPREV_PREFIX.'excerpt_show',
                'type'              => 'checkbox',
                'th'                => __('Excerpt', 'upprev' ),
                'label'             => __('Show excerpt.', 'upprev'),
                'checked'           => get_option(IWORKS_UPPREV_PREFIX.'excerpt_show', 1) == 1,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => IWORKS_UPPREV_PREFIX.'excerpt_length',
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
                'name'              => IWORKS_UPPREV_PREFIX.'show_thumb',
                'type'              => 'checkbox',
                'th'                => __('Featured image', 'upprev' ),
                'label'             => __('Show featured image.', 'upprev'),
                'checked'           => get_option(IWORKS_UPPREV_PREFIX.'show_thumb', 1) == 1,
                'sanitize_callback' => 'absint',
                'check_supports'    => array( 'post-thumbnails' )
            ),
            array
            (
                'name'              => IWORKS_UPPREV_PREFIX.'thumb_width',
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
                'name'              => IWORKS_UPPREV_PREFIX.'use_cache',
                'type'              => 'checkbox',
                'th'                => __('Cache', 'upprev'),
                'label'             => __('Use Transient Cache.', 'upprev'),
                'checked'           => get_option(IWORKS_UPPREV_PREFIX.'use_cache', 1) == 1,
                'sanitize_callback' => 'absint'
            ),
            array
            (
                'name'              => IWORKS_UPPREV_PREFIX.'cache_lifetime',
                'type'              => 'text',
                'label'             => __('Transients Cache Lifetime (in seconds, default one hour).', 'upprev'),
                'default'           => 360,
                'sanitize_callback' => 'absint'
            ),
        ),
    );
    return $iworks_upprev_options;
}
