jQuery( document ).ready( function( $ ) {
    /**
     * configuration
     */
    $( 'input[name="iworks_upprev_configuration"]' ).change( function() {
        $('#iworks_upprev_admin_index').submit();
    });
    /**
     * color
     */
    $('.wpColorPicker').wpColorPicker();
    /**
     * paypal
     */
    $('#iworks_upprev_paypal input[name=submit]').bind('click', function() {
        $('#iworks_upprev_paypal form').submit();
        return false;
    });
});
