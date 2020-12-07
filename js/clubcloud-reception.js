var $ = jQuery.noConflict();
jQuery(document).ready(function ($) {

    if ($('#clubcloud_shortcode').length) {
        alert(wpApiSettings.enevelope)


    } else {
        alert('The clubcloud video shortcode is required to use the Call Queueing code')
    }
});

function get_q_data(roomname) {
    jQuery.ajax({
        url: wpApiSettings.ajax_url,
        type: 'post',
        data: {
            action: 'get_call_queue',
            table_id: room,
            auth: cc_auth,
            envelope: wpApiSettings.envelope,
            temptoken: cc_token,
            currentjid: connection_id_var,
        },
        success: function (response) {
            //alert(response)
            startvideo(room, subject, response[0])
        }
    });


}
