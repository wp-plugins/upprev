jQuery(function(){iworks_upprev_tabulator_init();});
/**
 * Tabulator Bootup
 */
function iworks_upprev_tabulator_init()
{
    if (!jQuery("#hasadmintabs").length) {
        return;
    }
    jQuery('#hasadmintabs').prepend("<ul><\/ul>");
    jQuery('#hasadmintabs > fieldset').each(function(i){
        id      = jQuery(this).attr('id');
        rel     = jQuery(this).attr('rel');
        caption = jQuery(this).find('h3').text();
        if ( rel ) {
            rel = ' class="'+rel+'"';
        }
        jQuery('#hasadmintabs > ul').append('<li><a href="#'+id+'"><span'+rel+'>'+caption+"<\/span><\/a><\/li>");
        jQuery(this).find('h3').hide();
    });
    index = 0;
    jQuery('#hasadmintabs h3').each(function(i){
        if ( jQuery(this).hasClass( 'selected' ) ) {
            index = i;
        }
    });
    if ( index < 0 ) index = 0;
    jQuery("#hasadmintabs").tabs({ selected: index });
    jQuery('#hasadmintabs ul a').click(function(i){
        jQuery('#hasadmintabs input[name=iworks_upprev_last_used_tab]').val(jQuery(this).parent().index());
    });
    jQuery( 'input[name="iworks_upprev_configuration"]' ).change( function() {
        jQuery(this).parents( 'form' ).submit();
    });
}
/**
 * farbtastic
 */
jQuery(document).ready(function($) {
    $('.color-picker-container .picker').hide();

    /**
     * color
     */
    $('#iworks_upprev_color_picker').farbtastic('#iworks_upprev_color');
    $('#iworks_upprev_color').click(function() {
        $('#iworks_upprev_color_picker').fadeIn();
    });
    /**
     * color_background
     */
    $('#iworks_upprev_color_background_picker').farbtastic('#iworks_upprev_color_background');
    $('#iworks_upprev_color_background').click(function() {
        $('#iworks_upprev_color_background_picker').fadeIn();
    });
    /**
     * color_link
     */
    $('#iworks_upprev_color_link_picker').farbtastic('#iworks_upprev_color_link');
    $('#iworks_upprev_color_link').click(function() {
        $('#iworks_upprev_color_link_picker').fadeIn();
    });
    /**
     * color controlls
     */
    $('#iworks_upprev_color_set').bind( 'click', function() {
        iworks_upprev_admin_color_picker_setter( $(this) );
    });
    $(document).mousedown(function() {
        $('.color-picker-container .picker').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
            $(this).fadeOut();
        });
    });

    iworks_upprev_admin_color_picker_setter( $('#iworks_upprev_color_set' ) );

    function iworks_upprev_admin_color_picker_setter( el ) {
        if( el.attr('checked') ) {
            $('table tr[id^=tr_color] input[type=text]').each( function() {
                $(this).removeAttr( 'disabled' ).removeClass( 'disabled' );
                $(this).parent().parent().parent().removeClass( 'disabled' );
            });
        } else {
            $('table tr[id^=tr_color] input[type=text]').each( function() {
                $(this).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
                $(this).parent().parent().parent().addClass( 'disabled' );
            });
        }
    }
});

