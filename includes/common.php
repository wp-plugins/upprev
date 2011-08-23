<?php
function iworks_upprev_build_options($option_name, $echo = true)
{
    $options = array();
    global $iworks_upprev_options;
    if ( isset( $iworks_upprev_options[ $option_name ] ) ) {
        $options = $iworks_upprev_options[ $option_name ];
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
        $content .= sprintf( '<tr valign="top" class="%s">', $i++%2? 'alternate':'' );
        $content .= sprintf( '<th scope="row">%s</th>', $option['th'] );
        $content .= '<td>';
        switch ($option['type']) {
        case 'hidden':
            $hidden .= sprintf
                (
                    '<input type="hidden" name="%s" value="%s" />',
                    $option['name'],
                    $option['value']
                );
            break;
        case 'text':
            $value = get_option($option['name']);
            $content .= sprintf
                (
                    '<input type="text" name="%s" value="%s" class="%s" /> %s',
                    $option['name'],
                   (!$value && isset($option['default']))? $option['default']:$value,
                    $option['class'],
                    isset($option['label'])?  $option['label']:''
                );
            break;
        case 'checkbox':
            $content .= sprintf
                (
                    '<label for="%s"><input type="checkbox" name="%s" value="1"%s id="%s"%s /> %s</label>',
                    $option['name'],
                    $option['name'],
                    $option['checked']? ' checked="checked"':'',
                    $option['name'],
                    $option['disabled']? ' disabled="disabled"':'',
                    $option['label']
                );
            break;
        case 'radio':
            $option_value = get_option($option['name']);
            $content .= '<ul>';
            $i = 0;
            foreach ($option['radio'] as $value => $label) {
                $id = $option['name'].$i++;
                $content .= sprintf
                    (
                        '<li><label for="%s"><input type="radio" name="%s" value="%s"%s id="%s"/> %s</label></li>',
                        $id,
                        $option['name'],
                        $value,
                        ($option_value == $value or ( empty($option_value) and isset($option['default']) and $value == $option['default'] ) )? ' checked="checked"':'',
                        $id,
                        $label
                    );
            }
            $content .= '</ul>';
            break;
        case 'textarea':
            $value = get_option($option['name']);
            $content .= sprintf
                (
                    '<textarea name="%s" class="%s" rows="%d">%s</textarea>',
                    $option['name'],
                    $option['class'],
                    isset($option['rows'])? $option['rows']:3,
                   (!$value && isset($option['default']))? $option['default']:$value
                );
            break;
        default:
            $content .= sprintf('not implemented type: %s', $option['type']);
        }
        $content .= '</td>';
        $content .= '</tr>';
    }
    if ($content) {
        $top .= sprintf('<h3>%s</h3>', $options['label']);
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
            __('Save Changes', 'iworks_upprev')
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
    global $iworks_upprev_options;
    foreach( $iworks_upprev_options as $key => $data ) {
        if ( isset ( $data['options'] ) && is_array( $data['options'] ) ) {
            $option_group = IWORKS_UPPREV_PREFIX.$key;
            foreach ( $data['options'] as $option ) {
                register_setting
                    (
                        $option_group,
                        $option['name'],
                        isset($option['sanitize_callback'])? $option['sanitize_callback']:null
                    );
            }
        }
    }
}

/**
 * admin
 */
function iworks_upprev_admin_notices()
{
    $notices = array();
    if ( current_user_can('manage_options') && get_option('iworks_upprev_on', 0) == 0 ) {
        $notices[] = sprintf(__('Blomedia HUBS plugin is turned on, but not activated. <a href="%s">Activate HUBs</a>.', 'iworks_upprev'), get_admin_url(null, 'admin.php?page=blomedia-hubs/admin/index.php' ) );
    }
    /**
     * Show messages
     */
    foreach ($notices as $notice ) {
        printf('<div class="error"><p>%s</p></div>', $notice);
    }
}
