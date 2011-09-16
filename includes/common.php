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
        echo '<div class="below-h2 error"><p><strong>'.__('An error occurred while getting the configuration.', 'iworks_upprev').'</strong></p></div>';
        return;
    }
    $content  = '';
    $hidden   = '';
    $top      = '';
    $use_tabs = isset( $options['use_tabs'] ) && $options['use_tabs'];
    /**
     * produce options
     */
    if ( $use_tabs ) {
        $top .= '<div id="hasadmintabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">';
    }
    $i = 0;
    $last_tab = null;
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
            if ( $use_tabs ) {
                if ( $last_tab != $option['label'] ) {
                    $last_tab = $option['label'];
                    $content .= '</tbody></table>';
                    $content .= '</fieldset>';
                }
                $content .= sprintf(
                    '<fieldset id="%s" class="ui-tabs-panel ui-widget-content ui-corner-bottom">',
                    sanitize_title_with_dashes(remove_accents($option['label']))
                );
                if ( !$use_tabs ) {
                    $content .= sprintf( '<h3>%s</h3>', $option['label'] );
                }
                $content .= sprintf(
                    '<table class="form-table%s" style="%s">',
                    isset($options['widefat'])? ' widefat':'',
                    isset($options['style'])? $options['style']:''
                );
                $content .= '<tbody>';
            }
            $content .= '<tr><td colspan="2">';
        } else if ( $option['type'] != 'hidden' ) {
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
                    isset($option['dynamic']) && $option['dynamic']? iworks_upprev_get_option( $option['name'], $option_group ):$option['value']
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
                    iworks_upprev_get_option( $option['name'], $option_group )? ' checked="checked"':'',
                    isset($option['disabled']) && $option['disabled']? ' disabled="disabled"':'',
                    $option['label']
                );
            break;
        case 'checkbox_group':
            $option_value = iworks_upprev_get_option($option['name'], $option_group );
            if ( empty( $option_value ) && isset( $option['defaults'] ) ) {
                foreach( $option['defaults'] as $default ) {
                    $option_value[ $default ] = $default;
                }
            }
            $content .= '<ul>';
            $i = 0;
            if ( isset( $option['extra_options'] ) && is_callable( $option['extra_options'] ) ) {
                $option['options'] = array_merge( $option['options'], $option['extra_options']());
            }
            foreach ($option['options'] as $value => $label) {
                $checked = false;
                if ( is_array( $option_value ) && array_key_exists( $value, $option_value ) ) {
                    $checked = true;
                }
                $id = $option['name'].$i++;
                $content .= sprintf
                    (
                        '<li><label for="%s"><input type="checkbox" name="%s[%s]" value="%s"%s id="%s"/> %s</label></li>',
                        $id,
                        $html_element_name,
                        $value,
                        $value,
                        $checked? ' checked="checked"':'',
                        $id,
                        $label
                    );
            }
            $content .= '</ul>';
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
                        '<li><label for="%s"><input type="radio" name="%s" value="%s"%s id="%s" %s/> %s</label></li>',
                        $id,
                        $html_element_name,
                        $value,
                        ($option_value == $value or ( empty($option_value) and isset($option['default']) and $value == $option['default'] ) )? ' checked="checked"':'',
                        $id,
                        preg_match( '/\-disabled$/', $value )? 'disabled="disabled"':'',
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
                $content .= sprintf(
                    '<h3 id="upprev-%s">%s</h3>',
                    sanitize_title_with_dashes(remove_accents($option['label'])),
                    $option['label']
                );
                $i = 0;
            }
            break;
        case 'info':
            $content .= $option['value'];
            break;
        default:
            $content .= sprintf('not implemented type: %s', $option['type']);
        }
        if ( $option['type'] != 'hidden' ) {
            if ( isset ( $option['description'] ) && $option['description'] ) {
                if ( isset ( $option['label'] ) && $option['label'] ) {
                    $content .= '<br />';
                }
                $content .= sprintf('<span class="description">%s</span>', $option['description']);
            }
            $content .= '</td>';
            $content .= '</tr>';
        }
    }
    if ($content) {
        if ( isset ( $options['label'] ) && $options['label'] && !$use_tabs ) {
            $top .= sprintf('<h3>%s</h3>', $options['label']);
        }
        $top .= $hidden;
        if ( $use_tabs ) {
            $content .= '</tbody></table>';
            $content .= '</fieldset>';
            $content = $top.$content;
        } else {
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
    }
    if ( $use_tabs ) {
        $content .= '</div>';
    }
    $content .= sprintf(
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
                if ( $option['type'] == 'heading' || !isset($option['name']) ) {
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
    $text = __("<p>upPrev settings allows you to set the proprites of user notification showed when reader scroll down the page.</p>");
    add_contextual_help( 'upprev/admin/index', $text );
    //Enqueue ui-tabs
    wp_enqueue_script('jquery-ui-tabs');
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
            delete_option( IWORKS_UPPREV_PREFIX.$option['name'] );
        }
    }
}

function iworks_upprev_get_option( $option_name, $option_group = 'index' )
{
    $option_value = get_option( IWORKS_UPPREV_PREFIX.$option_name, null );
    if ( $option_value === null ) {
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
        if ( isset( $option['name'] ) && $option['name'] == $option_name ) {
            return isset($option['default'])? $option['default']:null;
        }
    }
    return null;
}

