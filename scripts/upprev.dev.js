console.log('init');
jQuery( function($) {
    if ( 'undefined' == typeof( iworks_upprev ) ) {
        return;
    }
    var upprev_closed                = false;
    var upprev_hidden                = true;
    var upprev_ga_track_view         = true;
    var upprev_ga                    = typeof(_gaq ) != 'undefined';
    var upprev_ga_opt_noninteraction = iworks_upprev.ga_opt_noninteraction == 1;
    var upprev_fade_duration         = 1000;
    var upprev_animate_duration      = 600;

    function upprev_show_box() {
        var upprev_last_screen = false;
        var iworks_upprev_offset_element = '#upprev-trigger';
console.log(1);
        if ( iworks_upprev_offset_element && $(iworks_upprev_offset_element) ) {
            if ( $(iworks_upprev_offset_element).length > 0 ) {
                upprev_last_screen = $(window).scrollTop() + $(window).height() > $(iworks_upprev_offset_element).offset().top;
            } else {
                upprev_last_screen = $(window).scrollTop() + $(window).height() >= $(document).height() * iworks_upprev.offset_percent / 100;
            }
        } else {
            return;
        }
        box = $('#upprev_box');
        if ( upprev_last_screen && !upprev_closed ) {
            if ( 'fade' == iworks_upprev.animation ) {
                box.fadeIn( upprev_fade_duration );
            } else {
                box.css({ display: 'block' });
                upprev_horizontal = iworks_upprev.css_side + 'px';
                upprev_vertical   = iworks_upprev.css_bottom + 'px';
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
                box.stop().animate( iworks_upprev_setup_position( upprev_horizontal, upprev_vertical ), upprev_animate_duration );
            }
            upprev_hidden = false;
            if ( upprev_ga && upprev_ga_track_view && iworks_upprev.ga_track_views == 1 ) {
                _gaq.push( [ '_trackEvent', 'upPrev', iworks_upprev.title, null, 0, upprev_ga_opt_noninteraction ] );
                upprev_ga_track_view = false;
            }
        }
        else if (upprev_closed && $(window).scrollTop() == 0) {
            upprev_closed = false;
        }
        else if (!upprev_hidden) {
            upprev_hidden = true;
            if ( iworks_upprev.animation == 'fade' ) {
                box.fadeOut( upprev_fade_duration );
            } else {
                upprev_horizontal = iworks_upprev_get_horizontal( box );
                upprev_vertical   = iworks_upprev_get_vertical( box );
                box.stop().animate( iworks_upprev_setup_position( upprev_horizontal, upprev_vertical ), upprev_animate_duration );
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
                $(this).fadeOut( upprev_fade_duration, function() {
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
                    $('#upprev_rise').css({ bottom: 0, right: 0 }).fadeIn( upprev_fade_duration );
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
            box.css( { width: iworks_upprev.css_width, borderWidth: iworks_upprev.css_border_width } );
            /**
             * apply custom colors
             */
            if ( iworks_upprev.color_set ) {
                iworks_upprev_add_style( box, 'background-color: ' + iworks_upprev.color_background + ' !important;color: ' + iworks_upprev.color + ' !important;border-color:' + iworks_upprev.color_border + ' !important;' );
                $( '#upprev_box a' ).each( function() {
                    iworks_upprev_add_style( $(this), 'color:' + iworks_upprev.color_link + ' !important' );
                });
            }
            /**
             * default
             */
            upprev_horizontal = iworks_upprev_get_horizontal( box );
            upprev_vertical = iworks_upprev_get_vertical( box );
            /**
             * out, is fade
             */
            if ( 'fade' == iworks_upprev.animation ) {
                upprev_vertical   = iworks_upprev.css_side;
                upprev_horizontal = iworks_upprev.css_bottom;
                box.css( { display: 'none' } );
            }
            box.css( iworks_upprev_setup_position( upprev_horizontal, upprev_vertical ) );
            /**
             * maybe show?
             */
            upprev_show_box();
        });
    });

    function iworks_upprev_get_horizontal( box ) {
        return '-' + (
                box.width()
                + parseInt( box.css( 'padding-top'    ).replace( /px$/, '' ) )
                + parseInt( box.css( 'padding-bottom' ).replace( /px$/, '' ) )
                ) + 'px';
    }

    function iworks_upprev_get_vertical( box ) {
        return '-' + (
                box.width()
                + parseInt( box.css( 'padding-left'  ).replace( /px$/, '' ) )
                + parseInt( box.css( 'padding-right' ).replace( /px$/, '' ) )
                ) + 'px';
    }

    function iworks_upprev_setup_position( upprev_vertical, upprev_horizontal ) {
        upprev_properites = {};
        switch ( iworks_upprev.position.all ) {
            case 'left':
                upprev_properites.left   = upprev_horizontal;
                upprev_properites.bottom = upprev_vertical;
                break;
            case 'bottom':
                upprev_properites.bottom = upprev_vertical;
                break;
            case 'top':
                upprev_properites.top    = upprev_vertical;
                break;
            case 'left-top':
                upprev_properites.left   = upprev_horizontal;
                upprev_properites.top    = upprev_vertical;
                break;
            case 'left-middle':
                upprev_properites.left   = upprev_horizontal;
                break;
            case 'right':
                upprev_properites.right  = upprev_horizontal;
                upprev_properites.bottom = upprev_vertical;
                break;
            case 'right-middle':
                upprev_properites.right  = upprev_horizontal;
                break;
            case 'right-top':
                upprev_properites.right  = upprev_horizontal;
                upprev_properites.top    = upprev_vertical;
                break;
            default:
                alert( iworks_upprev.position );
                break;
        }
        return upprev_properites;
    }

    function iworks_upprev_add_style( e, s ) {
        e.attr( 'test', 'value' );
        style = e.attr( 'style' );
        if ( "undefined" != style ) {
            style += ';';
        }
        e.attr( 'style', style + s );
    }

});

