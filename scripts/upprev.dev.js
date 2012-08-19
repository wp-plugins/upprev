function getScrollY()
{
    scrOfY = 0;
    if( typeof( window.pageYOffset ) == "number" ) {
        scrOfY = window.pageYOffset;
    } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
        scrOfY = document.body.scrollTop;
    } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
        scrOfY = document.documentElement.scrollTop;
    }
    return scrOfY;
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
    var side_offset = 0;

    function upprev_show_box() {
        var lastScreen = false;
        if (iworks_upprev.offset_element && $(iworks_upprev.offset_element) ) {
            if ($(iworks_upprev.offset_element).length > 0) {
                lastScreen = getScrollY() + $(window).height() > $(iworks_upprev.offset_element).offset().top;
            } else {
                lastScreen = getScrollY() + $(window).height() >= $(document).height() * iworks_upprev.offset_percent / 100;
            }
        } else {
            lastScreen = ( getScrollY() + $(window).height() >= $(document).height() * iworks_upprev.offset_percent / 100 );
        }
        box = $('#upprev_box');
        if (lastScreen && !upprev_closed) {
            if (iworks_upprev.animation == "fade") {
                box.fadeIn("slow");
            } else {
                horizontal = iworks_upprev.css_side + 'px';
                switch ( iworks_upprev.position.all ) {
                    case 'left':
                        box.stop().animate({left: horizontal, bottom: iworks_upprev.css_bottom + 'px' });
                        break;
                    case 'left-top':
                        box.stop().animate({left: horizontal, top: iworks_upprev.css_bottom + 'px' });
                        break;
                    case 'right':
                        box.stop().animate({right: horizontal, bottom: iworks_upprev.css_bottom + 'px' });
                        break;
                    case 'right-middle':
                        box.css( 'top', ( ( $(window).height() + box.height() ) / 2 ) + 'px' );
                        box.stop().animate( { right: horizontal });
                        break;
                    case 'right-top':
                        box.stop().animate({right: horizontal, top: iworks_upprev.css_bottom + 'px' });
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
        else if (upprev_closed && getScrollY() == 0) {
            upprev_closed = false;
        }
        else if (!upprev_hidden) {
            upprev_hidden = true;
            if ( iworks_upprev.animation == 'fade' ) {
                box.fadeOut( 'slow' );
            } else {
                /**
                 * horizontal
                 */
                horizontal = box.height() + side_offset;
                horizontal = '-' + horizontal + 'px';
                /**
                 * vertical
                 */
                padding_side  = '0px';
                position_side = '0px';
                if ( iworks_upprev.position_left ) {
                    padding_side  = box.css( 'padding-left' );
                    position_side = box.css( 'left' );
                } else {
                    padding_side  = box.css( 'padding-right' );
                    position_side = box.css( 'right' );
                }
                padding_side = parseInt( padding_side.replace( /px$/, '' ) );
                position_side = parseInt( position_side.replace( /px$/, '' ) );
                vertical = '-' + ( box.width()  + side_offset + padding_side + position_side ) + 'px';
                /**
                 * hide!
                 */
                switch ( iworks_upprev.position.all ) {
                    case 'left':
                        box.stop().animate( { left: vertical, bottom: horizontal } );
                        break;
                    case 'left-top':
                        box.stop().animate( { left: vertical, top: horizontal } );
                        break;
                    case 'right-top':
                        box.stop().animate( { right: vertical, top: horizontal } );
                        break;
                    case 'right-middle':
                        box.stop().animate( { right: horizontal } );
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
    $( window ).bind( 'scroll', function() {
        upprev_show_box();
    });
    if ($(window).height() == $(document).height()) {
        upprev_show_box();
    }
    $("#upprev_close").click(function() {
        $('#upprev_box').fadeOut("slow");
        $(window).unbind('scroll');
        return false;
    });
    $('#upprev_box').addClass( iworks_upprev.compare );
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
    $(document).ready(function() {
        if ( iworks_upprev.animation == 'fade' ) {
            return;
        }
        box = $('#upprev_box');
        /**
         * vertical
         */
        padding_side  = '0px';
        if ( iworks_upprev.position_left ) {
            padding_side  = box.css( 'padding-left' );
        } else {
            padding_side  = box.css( 'padding-right' );
        }
        padding_side = parseInt( padding_side.replace( /px$/, '' ) );
        vertical = '-' + ( box.width()  + side_offset + padding_side ) + 'px';

        horizontal = 0;

        console.log( 'css: right: ' +  box.css('right') ) ;
        console.log( 'vertical: ' + vertical );
        console.log( 'css: bottom: ' +  box.css('bottom') ) ;
        console.log( 'horizontal: ' + horizontal );
        /**
         * move!
         */
        switch ( iworks_upprev.position.all ) {
            case 'left':
                box.css( { left: vertical, bottom: horizontal } );
                break;
            case 'left-top':
                box.css( { left: vertical, top: horizontal } );
                break;
            case 'right-top':
                box.css( { right: vertical, top: horizontal } );
                break;
            case 'right-middle':
                box.css( { right: horizontal } );
                break;
            case 'right':
                box.css( { right: vertical, bottom: horizontal } );
                break;
            default:
                alert( iworks_upprev.position );
                break;
        }

    });
});

