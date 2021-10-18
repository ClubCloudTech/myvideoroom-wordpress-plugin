/**
 * Iframe Handling
 *
 * @package MyVideoRoomPlugin\JS\iframe-manage.js
 */

(function($) {
    var handleLogin = function(e) {
        var iframevar = document.getElementById('iframe-login'),
            loginframe = document.getElementById('mvr-login-form'),
            form_data = new FormData();
        form_data.append('action', 'myvideoroom_base_ajax');

        var room_name = $('#roominfo').data('roomName'),
            logged_in = $('#roominfo').data('loggedIn');
        form_data.append('room_name', room_name);
        form_data.append('action_taken', 'check_login');
        form_data.append('security', myvideoroom_base_ajax.security);
        $.ajax({
            type: 'post',
            dataType: 'html',
            url: myvideoroom_base_ajax.ajax_url,
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {
                var state_response = JSON.parse(response);
                if (state_response.login == logged_in) {
                    console.log('no change' + state_response.login + logged_in);
                } else {
                    iframevar.style.display = "none";
                    loginframe.innerHTML = "Login detected, redirecting";
                    window.location.reload();
                }
            },
            error: function(response) {
                console.log('Error in Login Check Function');
            }
        });
    }

    var init = function() {

    }

    /* Disabling Execution outside of MVR */
    var mvrIsactive = document.getElementsByClassName('mvr-nav-shortcode-outer-wrap');
    var loginIsactive = document.getElementsByClassName('mvr-login-form');

    if (mvrIsactive.length > 0 || loginIsactive.length > 0) {

        // Check Inside Iframe for Login Page
        document.getElementById('iframe-login').onload = function() {

            function inIframe() {
                try {
                    return window.self !== window.top;
                } catch (e) {
                    return true;
                }
            }
            if (inIframe) {
                var iframevar = document.getElementById('iframe-login'),
                    head = iframevar.contentWindow.document.getElementById("site-header"),
                    foot = iframevar.contentWindow.document.getElementById("footer"),
                    adminbar = iframevar.contentWindow.document.getElementById("wpadminbar");

                if (head) {
                    head.style.display = "none";
                }
                if (foot) {
                    foot.style.display = "none";
                }

                if (adminbar) {
                    adminbar.style.display = "none";
                }
                console.log('inside iframe');
                handleLogin();
            }

        };

    }

})(jQuery);