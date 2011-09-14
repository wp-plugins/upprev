function getScrollY() {
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
    var upprev_closed = false;
    var upprev_hidden = true;
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
        if (lastScreen && !upprev_closed) {
            if (iworks_upprev.animation == "fade") {
                $("#upprev_box").fadeIn("slow");
            } else if ( iworks_upprev.position == 'left' ) {
                $("#upprev_box").stop().animate({left:iworks_upprev.css_side+"px"});
            } else {
                $("#upprev_box").stop().animate({right:iworks_upprev.css_side+"px"});
            }
            upprev_hidden = false;
        }
        else if (upprev_closed && getScrollY() == 0) {
            upprev_closed = false;
        }
        else if (!upprev_hidden) {
            upprev_hidden = true;
            if (iworks_upprev.animation == "fade") {
                $("#upprev_box").fadeOut("slow");
            } else if ( iworks_upprev.position == 'left' ) {
                $("#upprev_box").stop().animate({left:"-" + ( iworks_upprev.css_width + iworks_upprev.css_side + 50 ) + "px"});
            } else {
                $("#upprev_box").stop().animate({right:"-" + ( iworks_upprev.css_width + iworks_upprev.css_side + 50 ) + "px"});
            }
        }
    }
    $(window).bind('scroll', function() {
        upprev_show_box();
    });
    if ($(window).height() == $(document).height()) {
        upprev_show_box();
    }
    $("#upprev_close").click(function() {
        if (iworks_upprev.animation == "fade") {
            $("#upprev_box").fadeOut("slow");
        } else if ( iworks_upprev.position == 'left' ) {
            $("#upprev_box").stop().animate({left:"-" + ( iworks_upprev.css_width + 50 ) + "px"});
        } else {
            $("#upprev_box").stop().animate({right:"-" + ( iworks_upprev.css_width + 50 ) + "px"});
        }
        upprev_closed = true;
        upprev_hidden = true;
        return false;
    });
    $('#upprev_box').addClass( iworks_upprev.compare );
    if( iworks_upprev.url_new_window == 1 ) {
        $('#upprev_box a').click(function() {
            window.open($(this).href);
            return false;
        });
    }
});

