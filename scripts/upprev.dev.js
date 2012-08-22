function iworks_upprev_get_horizontal( box, side_offset ) {
    return '-' + (
            box.width()
            + side_offset
            + parseInt( box.css( 'padding-top'    ).replace( /px$/, '' ) )
            + parseInt( box.css( 'padding-bottom' ).replace( /px$/, '' ) )
            ) + 'px';
}

function iworks_upprev_get_vertical( box, side_offset ) {
    return '-' + (
            box.width()
            + side_offset
            + parseInt( box.css( 'padding-left'  ).replace( /px$/, '' ) )
            + parseInt( box.css( 'padding-right' ).replace( /px$/, '' ) )
            ) + 'px';
}

function iworks_upprev_get_html_offset( h ) {
    return parseInt( h.css( 'margin-top' ).replace( /px$/, '' ) ) - h.offset().top;
}

jQuery(function($){
    if ( 'undefined' == typeof( iworks_upprev ) ) {
        return;
    }
    var upprev_closed                = false;
    var upprev_hidden                = true;
    var upprev_ga_track_view         = true;
    var upprev_ga                    = typeof(_gaq ) != 'undefined';
    var upprev_ga_opt_noninteraction = iworks_upprev.ga_opt_noninteraction == 1;
    var side_offset = 5;

    function upprev_show_box() {
        var lastScreen = false;
        if (iworks_upprev.offset_element && $(iworks_upprev.offset_element) ) {
            if ($(iworks_upprev.offset_element).length > 0) {
                lastScreen = iworks_upprev_get_html_offset( $('html') ) + $(window).height() > $(iworks_upprev.offset_element).offset().top;
            } else {
                lastScreen = iworks_upprev_get_html_offset( $('html') ) + $(window).height() >= $(document).height() * iworks_upprev.offset_percent / 100;
            }
        } else {
            lastScreen = ( iworks_upprev_get_html_offset( $('html') ) + $(window).height() >= $(document).height() * iworks_upprev.offset_percent / 100 );
        }
        box = $('#upprev_box');
        if (lastScreen && !upprev_closed) {
            box.css({ display: 'block' });
            if (iworks_upprev.animation == "fade") {
                box.fadeIn("slow");
            } else {
                horizontal = iworks_upprev.css_side + 'px';
                vertical   = iworks_upprev.css_bottom + 'px';
                switch ( iworks_upprev.position.all ) {
                    case 'bottom':
                    case 'top':
                        box.css( 'left', ( ( $(window).width() - box.width() - parseInt( box.css( 'padding-left'  ).replace( /px$/, '' ) ) - parseInt( box.css( 'padding-right' ).replace( /px$/, '' ) ) ) / 2 ) + 'px' )
                        break;
                    case 'left-middle':
                    case 'right-middle':
                        box.css( 'top', ( ( $(window).height() - box.height() - parseInt( box.css( 'padding-top'  ).replace( /px$/, '' ) ) - parseInt( box.css( 'padding-bottom' ).replace( /px$/, '' ) ) ) / 2 ) + 'px' )
                        break;
                }
                switch ( iworks_upprev.position.all ) {
                    case 'left':
                        box.stop().animate({ left: horizontal, bottom: vertical });
                        break;
                    case 'bottom':
                        box.stop().animate({ bottom: vertical });
                        break;
                    case 'top':
                        box.stop().animate({ top: vertical });
                        break;
                    case 'left-top':
                        box.stop().animate({ left: horizontal, top: vertical });
                        break;
                    case 'left-middle':
                        box.stop().animate( { left: horizontal });
                        break;
                    case 'right':
                        box.stop().animate({ right: horizontal, bottom: vertical });
                        break;
                    case 'right-middle':
                        box.stop().animate( { right: horizontal });
                        break;
                    case 'right-top':
                        box.stop().animate({right: horizontal, top: vertical });
                        break;
                    default:
                        alert( iworks_upprev.position );
                        break;
                }
            }
            upprev_hidden = false;
            if ( upprev_ga && upprev_ga_track_view && iworks_upprev.ga_track_views == 1 ) {
                _gaq.push( [ '_trackEvent', 'upPrev', iworks_upprev.title, null, 0, upprev_ga_opt_noninteraction ] );
                upprev_ga_track_view = false;
            }
        }
        else if (upprev_closed && iworks_upprev_get_html_offset( $('html') ) == 0) {
            upprev_closed = false;
        }
        else if (!upprev_hidden) {
            upprev_hidden = true;
            if ( iworks_upprev.animation == 'fade' ) {
                box.fadeOut( 'slow' );
            } else {
                horizontal = iworks_upprev_get_horizontal( box, side_offset );
                vertical = iworks_upprev_get_vertical( box, side_offset );
                /**
                 * hide!
                 */
                switch ( iworks_upprev.position.all ) {
                    case 'left':
                        box.stop().animate( { left: vertical, bottom: horizontal } );
                        break;
                    case 'bottom':
                        box.stop().animate( { bottom: horizontal } );
                        break;
                    case 'top':
                        box.stop().animate( { top: horizontal } );
                        break;
                    case 'left-top':
                        box.stop().animate( { left: vertical, top: horizontal } );
                        break;
                    case 'left-middle':
                        box.stop().animate( { left: vertical } );
                        break;
                    case 'right-top':
                        box.stop().animate( { right: vertical, top: horizontal } );
                        break;
                    case 'right-middle':
                        box.stop().animate( { right: vertical} );
                        break;
                    case 'right':
                        box.stop().animate( { right: vertical, bottom: horizontal } );
                        break;
                    default:
                        alert( iworks_upprev.position );
                        break;
                }
            }
        }
    }
    $(document).ready(function() {
        $.get( iworks_upprev.url, function( data ) {
            /**
             * append data
             */
            $('body').append( data );
            /**
             * bind scroll
             */
            $( window ).bind( 'scroll', function() {
                upprev_show_box();
            });
            $( '#upprev_rise' ).click( function() {
                $(this).fadeOut('slow', function() {
                    upprev_show_box();
                    $( window ).bind( 'scroll', function() {
                        upprev_show_box();
                    });
                });
            });
            /**
             * bind close function
             */
            $("#upprev_close").click(function() {
                $('#upprev_box').fadeOut("slow", function(){
                    $(window).unbind('scroll');
                    $('#upprev_rise').css({ bottom: 0, right: 0 }).fadeIn( 'slow' );
                });
                return false;
            });
            /**
             * force links to open in new window if needed
             */
            if( iworks_upprev.url_new_window == 1 || iworks_upprev.ga_track_clicks == 1 ) {
                $('#upprev_box a[id!=upprev_close]').click(function() {
                    $(this).attr('style','bacground-color:lime');
                    if ( iworks_upprev.url_new_window == 1) {
                        window.open($(this).attr('href'));
                    }
                    if ( upprev_ga && iworks_upprev.ga_track_clicks == 1 ) {
                        _gaq.push( [ '_trackEvent', 'upPrev', iworks_upprev.title, $(this).html(), 1, upprev_ga_opt_noninteraction ] );
                    }
                    if ( iworks_upprev.url_new_window == 1) {
                        return false;
                    }
                });
            }
            /**
             * setup width
             */
            box = $('#upprev_box');
            box.css( { width: iworks_upprev.css_width } );
            /**
             * out, is fade
             */
            if ( iworks_upprev.animation == 'flyout' ) {
                /**
                * setup init animation
                */
                horizontal = iworks_upprev_get_horizontal( box, side_offset );
                vertical = iworks_upprev_get_vertical( box, side_offset );
                /**
                * move!
                */
                switch ( iworks_upprev.position.all ) {
                    case 'left':
                        box.css( { left: vertical, bottom: horizontal } );
                        break;
                    case 'bottom':
                        box.css( { bottom: horizontal } );
                        break;
                    case 'top':
                        box.css( { top: horizontal } );
                        break;
                    case 'left-top':
                        box.css( { left: vertical, top: horizontal } );
                        break;
                    case 'left-middle':
                        box.css( { left: vertical } );
                    case 'right-top':
                        box.css( { right: vertical, top: horizontal } );
                        break;
                    case 'right-middle':
                        box.css( { right: vertical } );
                        break;
                    case 'right':
                        box.css( { right: vertical, bottom: horizontal } );
                        break;
                    default:
                        alert( iworks_upprev.position );
                        break;
                }
            }
            /**
             * maybe show?
             */
            upprev_show_box();
        });
    });
});

