/**
 * Handle Ajax requests for Tabbed Frames
 *
 * @package MyVideoRoomPlugin\Module\SiteVideo
 */

/*global myvideoroom_sitevideo_settings*/

window.addEventListener(
    "load",
    function() {
        jQuery(
            function($) {


                /**
                 * Initialise Functions on Load
                 */
                function init() {
                    $('.myvideoroom-sitevideo-settings').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            var room_id = $(this).data('roomId'),
                                input_type = $(this).data('inputType');
                            moduleAction(room_id, input_type);
                        }
                    );
                    $('.myvideoroom-sitevideo-delete').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            let room = parseInt($(this).attr('data-room-id')),
                                nonce = $(this).attr('data-nonce'),
                                room_name = $(this).attr('data-room-name');

                            deleteRoom(room, nonce, room_name);
                        }
                    );

                    $('.mvr-confirmation-cancel').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            $('.mvr-security-room-host').empty();
                            $('.myvideoroom-sitevideo-hide-button').hide();
                        }
                    );

                    $('.mvr-confirmation-button').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            let room = parseInt($(this).attr('data-room-id')),
                                nonce = $(this).attr('data-nonce'),
                                input_type = $(this).attr('data-input-type');
                            console.log(input_type);
                            // Button type handlers
                            if (input_type === 'delete-approved') {
                                deleteRoom(room, nonce, input_type);
                            }
                        }
                    );
                }





                /**
                 * Handles Core Room View, Settings Click, and Default Room Appearance.
                 */
                var moduleAction = function(room_id, input_type) {

                        var $container = $('.mvr-security-room-host');
                        var loading_text = $container.data('loadingText');
                        $('.myvideoroom-sitevideo-hide-button').show();
                        if (input_type === 'close') {
                            $container.empty();
                            $('#mvr-close_' + room_id).hide();
                            return false;
                        }
                        $container.html(loading_text);
                        var form_data = new FormData();
                        form_data.append('action', 'myvideoroom_sitevideo_settings');
                        form_data.append('action_taken', 'core');
                        form_data.append('roomId', room_id);
                        form_data.append('inputType', input_type);
                        form_data.append('security', myvideoroom_sitevideo_settings.security);


                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_sitevideo_settings.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);
                                /*if ('URLSearchParams' in window) {
                                    var searchParams = new URLSearchParams(window.location.search);
                                    searchParams.set('room_id', room_id);

                                    var newRelativePathQuery = window.location.pathname + '?' + searchParams.toString();
                                    history.pushState(null, '', newRelativePathQuery);
                                }*/


                                window.myvideoroom_tabbed_init;
                                $container.html(state_response.mainvideo);

                                if (window.myvideoroom_tabbed_init) {
                                    window.myvideoroom_tabbed_init($container);
                                }

                                if (window.myvideoroom_app_init) {
                                    window.myvideoroom_app_init($container[0]);
                                }

                                if (window.myvideoroom_app_load) {
                                    window.myvideoroom_app_load();
                                }

                                if (window.myvideoroom_shoppingbasket_init) {
                                    window.myvideoroom_shoppingbasket_init();
                                }

                            }
                        });

                    }
                    /**
                     * Handles Core Room View, Settings Click, and Default Room Appearance.
                     */
                var deleteRoom = function(room_id, nonce, room_name) {
                        console.log(room_id + 'RI - NO' + nonce);
                        var $container = $('.mvr-security-room-host');
                        var loading_text = $container.data('loadingText');
                        $('.myvideoroom-sitevideo-hide-button').show();

                        var form_data = new FormData();
                        form_data.append('action', 'myvideoroom_sitevideo_settings');

                        if (room_name === 'delete-approved') {
                            form_data.append('action_taken', 'delete_approved');
                            $container.html('Delete In Progress');
                        } else {
                            $container.html(loading_text);
                            form_data.append('action_taken', 'delete_room');

                        }

                        form_data.append('roomId', room_id);
                        form_data.append('roomName', room_name);
                        form_data.append('nonce', nonce);
                        form_data.append('security', myvideoroom_sitevideo_settings.security);


                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_sitevideo_settings.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);
                                if (room_name !== 'delete-approved') {
                                    window.myvideoroom_tabbed_init;
                                    $container.html(state_response.mainvideo);

                                    if (window.myvideoroom_tabbed_init) {
                                        window.myvideoroom_tabbed_init($container);
                                    }

                                    if (window.myvideoroom_app_init) {
                                        window.myvideoroom_app_init($container[0]);
                                    }

                                    if (window.myvideoroom_app_load) {
                                        window.myvideoroom_app_load();
                                    }

                                    if (window.myvideoroom_shoppingbasket_init) {
                                        window.myvideoroom_shoppingbasket_init();
                                    }
                                } else {
                                    sctable = $('#mvr-table-basket-frame_site-conference-room');
                                    $('.mvr-security-room-host').empty();
                                    sctable.empty();
                                    sctable.html(state_response.mainvideo);
                                    reloadJs('myvideoroom-monitor-js');
                                }

                                init();
                            }
                        });

                    }
                    /**
                     * Reload a Script by ID and re-initialise.
                     */
                function reloadJs(id) {
                    src = $('#' + id).attr('src');
                    src = $('script[src$="' + src + '"]').attr("src");
                    $('script[src$="' + src + '"]').remove();
                    $('<script/>').attr('src', src).appendTo('head');
                }
                init();
            }
        );
    }
);