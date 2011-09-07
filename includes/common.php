<?php
function iworks_upprev_build_options( $option_group = 'index', $echo = true )
{
    $options = array();
    $iworks_upprev_options = iworks_upprev_options();
    if ( isset( $iworks_upprev_options[ $option_group ] ) ) {
        $options = $iworks_upprev_options[ $option_group ];
    }
    /**
     * check options exists?
     */
    if(!is_array($options['options'])) {
        echo '<div class="below-h2 error"><p><strong>'.__('An error occurred while getting the configuration.', 'solr4wp').'</strong></p></div>';
        return;
    }
    $content = '';
    $hidden  = '';
    /**
     * produce options
     */
    $i = 0;
    foreach ($options['options'] as $option) {
        if (isset($option['capability'])) {
            if(!current_user_can($option['capability'])) {
                continue;
            }
        }
        $show_option = true;
        if ( isset( $option['check_supports'] ) && is_array( $option['check_supports'] ) && count( $option['check_supports'] ) ) {
            foreach ( $option['check_supports'] as $support_to_check ) {
                if ( !current_theme_supports( $support_to_check ) ) {
                    $show_option = false;
                }
            }
        }
        if ( !$show_option ) {
            continue;
        }
        if ( $option['type'] == 'heading' ) {
            $content .= '<tr><td colspan="2">';
        } else {
            $content .= sprintf( '<tr valign="top" class="%s">', $i++%2? 'alternate':'' );
            $content .= sprintf( '<th scope="row">%s</th>', isset($option['th']) && $option['th']? $option['th']:'&nbsp;' );
            $content .= '<td>';
        }
        $html_element_name = isset($option['name']) && $option['name']? IWORKS_UPPREV_PREFIX.$option['name']:'';
        switch ($option['type']) {
        case 'hidden':
            $hidden .= sprintf
                (
                    '<input type="hidden" name="%s" value="%s" />',
                    $html_element_name,
                    $option['value']
                );
            break;
        case 'text':
            $content .= sprintf
                (
                    '<input type="text" name="%s" value="%s" class="%s" /> %s',
                    $html_element_name,
                    iworks_upprev_get_option( $option['name'], $option_group ),
                    isset($option['class']) && $option['class']? $option['class']:'',
                    isset($option['label'])?  $option['label']:''
                );
            break;
        case 'checkbox':
            $content .= sprintf
                (
                    '<label for="%s"><input type="checkbox" name="%s" id="%s" value="1"%s%s /> %s</label>',
                    $html_element_name,
                    $html_element_name,
                    $html_element_name,
                    $option['checked']? ' checked="checked"':'',
                    isset($option['disabled']) && $option['disabled']? ' disabled="disabled"':'',
                    $option['label']
                );
            break;
        case 'radio':
            $option_value = iworks_upprev_get_option($option['name'], $option_group );
            $content .= '<ul>';
            $i = 0;
            if ( isset( $option['extra_options'] ) && is_callable( $option['extra_options'] ) ) {
                $option['radio'] = array_merge( $option['radio'], $option['extra_options']());
            }
            foreach ($option['radio'] as $value => $label) {
                $id = $option['name'].$i++;
                $content .= sprintf
                    (
                        '<li><label for="%s"><input type="radio" name="%s" value="%s"%s id="%s"/> %s</label></li>',
                        $id,
                        $html_element_name,
                        $value,
                        ($option_value == $value or ( empty($option_value) and isset($option['default']) and $value == $option['default'] ) )? ' checked="checked"':'',
                        $id,
                        $label
                    );
            }
            $content .= '</ul>';
            break;
        case 'textarea':
            $value = iworks_upprev_get_option($option['name'], $option_group);
            $content .= sprintf
                (
                    '<textarea name="%s" class="%s" rows="%d">%s</textarea>',
                    $html_element_name,
                    $option['class'],
                    isset($option['rows'])? $option['rows']:3,
                   (!$value && isset($option['default']))? $option['default']:$value
                );
            break;
        case 'heading':
            if ( isset( $option['label'] ) && $option['label'] ) {
                $content .= sprintf( '<h3>%s</h3>', $option['label'] );
                $i = 0;
            }
            break;
        default:
            $content .= sprintf('not implemented type: %s', $option['type']);
        }
        if ( isset ( $option['description'] ) && $option['description'] ) {
            if ( isset ( $option['label'] ) && $option['label'] ) {
                $content .= '<br />';
            }
            $content .= sprintf('<span class="description">%s</span>', $option['description']);
        }
        $content .= '</td>';
        $content .= '</tr>';
    }
    if ($content) {
        $top = '';
        if ( isset ( $options['label'] ) && $options['label'] ) {
            $top .= sprintf('<h3>%s</h3>', $options['label']);
        }
        $top .= $hidden;
        $top .= sprintf( '<table class="form-table%s" style="%s">', isset($options['widefat'])? ' widefat':'', isset($options['style'])? $options['style']:'' );
        if ( isset( $options['thead'] ) ) {
            $top .= '<thead><tr>';
            foreach( $options['thead'] as $text => $colspan ) {
                $top .= sprintf
                    (
                        '<th%s>%s</th>',
                        $colspan > 1? ' colspan="'.$colspan.'"':'',
                        $text
                    );
            }
            $top .= '</tr></thead>';
        }
        $top .= '<tbody>';
        $content = $top.$content;
        $content .= '</tbody></table>';
    }
    $content .= sprintf
        (
            '<p class="submit"><input type="submit" class="button-primary" value="%s" /></p>',
            __('Save Changes', 'upprev')
        );
    /* print ? */
    if ( $echo ) {
        echo $content;
        return;
    }
    return $content;
}

function iworks_upprev_options_init()
{
    add_filter( 'plugin_row_meta', 'iworks_upprev_plugin_links', 10, 2 );
    $iworks_upprev_options = iworks_upprev_options();
    foreach( $iworks_upprev_options as $key => $data ) {
        if ( isset ( $data['options'] ) && is_array( $data['options'] ) ) {
            $option_group = IWORKS_UPPREV_PREFIX.$key;
            foreach ( $data['options'] as $option ) {
                if ( $option['type'] == 'heading' ) {
                    continue;
                }
                register_setting
                    (
                        $option_group,
                        IWORKS_UPPREV_PREFIX.$option['name'],
                        isset($option['sanitize_callback'])? $option['sanitize_callback']:null
                    );
            }
        }
    }
}

function iworks_upprev_activate()
{
    require_once dirname(__FILE__).'/options.php';
    $iworks_upprev_options = iworks_upprev_options();
    foreach( $iworks_upprev_options as $key => $data ) {
    }
    foreach ( $data['options'] as $option ) {
        if ( $option['type'] == 'heading' or !isset( $option['name'] ) or !$option['name'] or !isset( $option['default'] ) ) {
            continue;
        }
        add_option( IWORKS_UPPREV_PREFIX.$option['name'], $option['default'], '', isset($option['autoload'])? $option['autoload']:'yes' );
    }
    add_option( IWORKS_UPPREV_PREFIX.'cache_stamp', date('c') );
}

function iworks_upprev_deactivate()
{
    $iworks_upprev_options = iworks_upprev_options();
    foreach( $iworks_upprev_options as $key => $data ) {
        foreach ( $data['options'] as $option ) {
            if ( $option['type'] == 'heading' or !isset( $option['name'] ) or !$option['name'] ) {
                continue;
            }
            delete_option( $option['name'] );
        }
    }
}

function iworks_upprev_get_option( $option_name, $option_group = 'index' )
{
    $option_value = get_option( IWORKS_UPPREV_PREFIX.$option_name, null );
    if ( $option_group === null ) {
        $option_value = iworks_upprev_get_default_value( $option_name, $option_group );
    }
    return $option_value;
}

function iworks_upprev_get_default_value( $option_name, $option_group = 'index' )
{
    $options = array();
    $iworks_upprev_options = iworks_upprev_options();
    if ( isset( $iworks_upprev_options[ $option_group ] ) ) {
        $options = $iworks_upprev_options[ $option_group ];
    }
    /**
     * check options exists?
     */
    if(!is_array($options['options'])) {
        return null;
    }
    foreach ( $options['options'] as $option ) {
        if ( isset( $option['name'] ) && $option['name'] == IWORKS_UPPREV_PREFIX.$option_name ) {
            return isset($option['default'])? $option['default']:null;
        }
    }
    return null;
}

function iworks_upprev_get_post_types()
{
    $data = array();
    $post_types = get_post_types( null, 'objects' );
    foreach ( $post_types as $post_type_key => $post_type ) {
        if ( preg_match( '/^(post|page|attachment|revision|nav_menu_item)$/', $post_type_key ) ) {
            continue;
        }
        $data[$post_type_key] = __('Only custom post type: ', 'upprev' ).(isset($post_type->labels->name)? $post_type->labels->name:$post_type_key).'.';
    }
    return $data;
}
