/**
 * Ajax control for Admin pages.
 *
 * @package ElementalPlugin\Admin\js\AdminAjax.js
 */
window.addEventListener(
    "load",
    function() {
        jQuery(
            function($) {

                /**
                 * Initialise Functions on Load
                 */
                function init() {
                    // Buttons for Core. (settings, default room view)
                    $('.myvideoroom-sitevideo-settings').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            var room_id = $(this).data('roomId'),
                                input_type = $(this).data('inputType');
                            moduleCore(room_id, input_type);
                        }
                    );

                    // For Cancel Buttons.
                    $('.mvr-confirmation-cancel').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            $('.mvr-security-room-host').empty();
                            $('.myvideoroom-sitevideo-hide-button').hide();
                        }
                    );
                    // Toggle for Settings UI Slider.
                    $('.mvideoroom-information-menu-toggle-selector').click(
                        function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                            action = $(this).attr('data-target');
                            $('.mvideoroom-information-menu-toggle-target-' + action).slideToggle();
                            $('.mvideoroom-settings-menu-toggle-target-' + action).slideToggle();

                            $('.' + action).slideToggle();
                            e.stopImmediatePropagation();

                        }
                    );
                    // Toggle for Info - Conf Center.
                    $('.mvr-admin-ajax').click(
                        function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                            let action = $(this).attr('data-action'),
                                module = $(this).attr('data-module');
                            moduleAction(action, module);
                        }
                    );
                    // For Changing Page Slugs.
                    $('.myvideoroom-edit-page-trigger').click(
                        function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                            let id = $(this).attr('data-id'),
                                offset = $(this).attr('data-offset');
                            $('#urlinput_' + id + '-' + offset).toggle();
                            $('#button_' + id + '-' + offset).toggle();
                        }
                    );
                    // For Room Checking Slug Suitability during Room Edits
                    $(".myvideoroom-input-url-trigger").keyup(
                        function(e) {
                            e.stopPropagation();
                            let id = $(this).attr('data-id'),
                                offset = $(this).attr('data-offset'),
                                core_url = 'https://' + document.domain + '/';
                            $('#urlchange_' + id + '-' + offset).html(core_url + this.value.toLowerCase());
                            let targetid = '#button_' + id + '-' + offset;
                            checkSlug(targetid, this.value.toLowerCase());
                        }
                    );
                    // For Changing Page Names.
                    $('.myvideoroom-edit-title-trigger').click(
                        function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                            let id = $(this).attr('data-id'),
                                offset = $(this).attr('data-offset');
                            $('#nameinput_' + id + '-' + offset).toggle();
                            $('#namebutton_' + id + '-' + offset).toggle();
                        }
                    );
                    // For Checking Name Rules
                    $(".myvideoroom-input-name-trigger").keyup(
                        function(e) {
                            e.stopPropagation();
                            let id = $(this).attr('data-id'),
                                offset = $(this).attr('data-offset');
                            $('#namechange_' + id + '-' + offset).html(this.value);
                            let targetid = '#namebutton_' + id + '-' + offset;
                            checkName(targetid, this.value);
                        }
                    );

                    // For Update Slug.
                    $('.myvideoroom-roomslug-submit-form').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            let id = $(this).attr('data-id'),
                                offset = $(this).attr('data-offset');
                            updateSlug(id, offset);
                        }
                    );

                    // For Update Name.
                    $('.myvideoroom-roomname-submit-form').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            let id = $(this).attr('data-id'),
                                offset = $(this).attr('data-offset');
                            updateName(id, offset);
                        }
                    );

                    // For Adding New Room.
                    $('#button_add_new').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            addRoom();
                            console.log('addnew');
                        }
                    );

                    // For Confirmation Buttons.
                    $('.mvr-confirmation-button').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            let room = parseInt($(this).attr('data-room-id')),
                                nonce = $(this).attr('data-nonce'),
                                input_type = $(this).attr('data-input-type');
                            // Button type handlers
                            if (input_type === 'delete-approved') {
                                deleteRoom(room, nonce, input_type);
                            }
                            console.log('cancel');
                        }
                    );
                    // For Delete Room Button.
                    $('.myvideoroom-sitevideo-delete').click(
                        function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            e.preventDefault();
                            let room = parseInt($(this).attr('data-room-id')),
                                nonce = $(this).attr('data-nonce'),
                                room_name = $(this).attr('data-room-name');
                            console.log('delete');
                            deleteRoom(room, nonce, room_name);
                        }
                    );

                    $('#user-profile-input').on('keyup', checkform);
                    $('#group-profile-input').on('keyup', checkgroupform);

                    $('#save-user-tab').on('click', updateUsertab);
                    $('#save-group-tab').on('click', updateGrouptab);

                }

                /**
                 * Room Manager Ajax Functions
                 * Used by Room Manager Ajax pages to update room URL(slugs)
                 */

                /**
                 * Handles Module Activation and De-activation Button
                 */
                var moduleAction = function(action, module) {
                        if (!action || !module) {
                            return false;
                        }
                        var form_data = new FormData(),
                            frame = $('#module' + module);
                        form_data.append('action', 'myvideoroom_admin_ajax');
                        form_data.append('action_taken', 'update_module');
                        form_data.append('state', action);
                        form_data.append('module', module);
                        form_data.append('security', myvideoroom_admin_ajax.security);
                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_admin_ajax.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);
                                if (state_response.button) {
                                    frame_parent = frame.parent().attr('id');
                                    parent_element = $('#' + frame_parent);
                                    frame.remove();
                                    frame.parent().empty();
                                    parent_element.parent().html(state_response.button);
                                    refreshTables();
                                    init();
                                }

                            },
                            error: function(response) {
                                console.log('Error Uploading');
                            }
                        });
                    }
                    /**
                     * Handles Core Room View, Settings Click, and Default Room Appearance.
                     */
                var moduleCore = function(room_id, input_type) {

                        var container = $('.mvr-security-room-host');
                        var loading_text = container.data('loadingText');
                        $('.myvideoroom-sitevideo-hide-button').show();
                        if (input_type === 'close') {
                            container.empty();
                            $('#mvr-close_' + room_id).hide();
                            return false;
                        }
                        container.html('<h1 style = "padding:20px" > ' + loading_text + '</h1>');
                        var form_data = new FormData();
                        form_data.append('action', 'myvideoroom_admin_ajax');
                        form_data.append('action_taken', 'core');
                        form_data.append('roomId', room_id);
                        form_data.append('inputType', input_type);
                        form_data.append('security', myvideoroom_admin_ajax.security);

                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_admin_ajax.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);

                                window.myvideoroom_tabbed_init;
                                container.html(state_response.mainvideo);

                                if (window.myvideoroom_tabbed_init) {
                                    window.myvideoroom_tabbed_init(container);
                                }

                                if (window.myvideoroom_app_init) {
                                    window.myvideoroom_app_init(container[0]);
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
                     * Check if Room Length is sufficient, and then checks availability of room name.
                     */
                function checkSlug(targetid, input) {
                    var length = input.length;
                    if (length < 3) {
                        $(targetid).prop('value', 'Too Short');
                        $(targetid).prop('disabled', true);
                    } else {
                        $(targetid).prop('value', 'Checking Availability');
                        verifySlug(targetid, input);
                    }
                    return false;

                }
                /**
                 * Check if Room Length is sufficient, and then checks availability of room name.
                 */
                function checkName(targetid, input) {
                    var length = input.length;
                    if (length < 3) {
                        $(targetid).prop('value', 'Too Short');
                        $(targetid).prop('disabled', true);
                    } else {
                        $(targetid).prop('disabled', false);
                        $(targetid).prop('value', 'Save');
                        //updateName(targetid, input);
                    }
                    return false;

                }
                /**
                 * Check Slug Exists or Is available (used for changing page slug for rooms)
                 */
                var verifySlug = function(targetid, input) {
                        var form_data = new FormData();
                        form_data.append('action', 'myvideoroom_admin_ajax');
                        form_data.append('action_taken', 'check_slug');
                        form_data.append('slug', input);
                        form_data.append('security', myvideoroom_admin_ajax.security);
                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_admin_ajax.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);

                                if (state_response.available === true) {
                                    $(targetid).prop('disabled', false);
                                    $(targetid).prop('value', 'Save')
                                } else {
                                    $(targetid).prop('disabled', true);
                                    $(targetid).prop('value', 'Taken')
                                }
                            },
                            error: function(response) {
                                console.log('Error Uploading');
                            }
                        });
                    }
                    /**
                     * Update Slug - used to update page slug from Room Manager Pages
                     */
                var updateSlug = function(id, offset) {
                    var form_data = new FormData();
                    var input = $('#urlinput_' + id + '-' + offset).val(),
                        maintable = $('#mvr-table-basket-frame_main');
                    form_data.append('action', 'myvideoroom_admin_ajax');
                    form_data.append('action_taken', 'update_slug');
                    form_data.append('post_id', id);
                    form_data.append('slug', input);
                    form_data.append('security', myvideoroom_admin_ajax.security);
                    $.ajax({
                        type: 'post',
                        dataType: 'html',
                        url: myvideoroom_admin_ajax.ajax_url,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(response) {
                            var state_response = JSON.parse(response);
                            $('#button_' + id + '-' + offset).prop('value', state_response.feedback);
                            $('#button_' + id + '-' + offset).prop('disabled', true);
                            maintable.empty();
                            if (maintable) {
                                maintable.html(state_response.maintable);
                            }
                            if (state_response.personalmeeting) {
                                let pmm = $('#mvr-table-basket-frame_personal-meeting-module');
                                pmm.html(state_response.personalmeeting);
                            }
                            if (state_response.conference) {
                                let conf = $('#mvr-table-basket-frame_site-conference-room');
                                conf.html(state_response.conference);
                            }

                            init();

                            if (window.myvideoroom_monitor_load) {
                                window.myvideoroom_monitor_load();

                            }
                        },
                        error: function(response) {
                            console.log('Error Updating Slug');
                        }
                    });
                }

                /**
                 * Update Name - used to update page Name from Room Manager Pages
                 */
                var updateName = function(id, offset) {
                    var form_data = new FormData();
                    var input = $('#nameinput_' + id + '-' + offset).val(),
                        maintable = $('#mvr-table-basket-frame_main');
                    form_data.append('action', 'myvideoroom_admin_ajax');
                    form_data.append('action_taken', 'update_name');
                    form_data.append('post_id', id);
                    form_data.append('room_name', input);
                    form_data.append('security', myvideoroom_admin_ajax.security);
                    $.ajax({
                        type: 'post',
                        dataType: 'html',
                        url: myvideoroom_admin_ajax.ajax_url,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(response) {
                            var state_response = JSON.parse(response);
                            $('#namebutton_' + id + '-' + offset).prop('value', state_response.feedback);
                            $('#namebutton_' + id + '-' + offset).prop('disabled', true);
                            maintable.empty();
                            if (maintable) {
                                maintable.html(state_response.maintable);
                            }
                            if (state_response.personalmeeting) {
                                let pmm = $('#mvr-table-basket-frame_personal-meeting-module');
                                pmm.html(state_response.personalmeeting);
                            }
                            if (state_response.conference) {
                                let conf = $('#mvr-table-basket-frame_site-conference-room');
                                conf.html(state_response.conference);
                            }

                            init();

                            if (window.myvideoroom_monitor_load) {
                                window.myvideoroom_monitor_load();

                            }
                        },
                        error: function(response) {
                            console.log('Error Updating Name');
                        }
                    });
                }

                /**
                 * Add New Room - used to add a new room from Site Conference Pages
                 */
                var addRoom = function() {
                        var form_data = new FormData();
                        var input = $('#room-url-link').val().toLowerCase(),
                            display_title = $('#room-display-name').val(),
                            table = $('#mvr-table-basket-frame_site-conference-room'),
                            shortcode = table.attr('data-type');

                        form_data.append('action_taken', 'add_new_room');

                        if (display_title.length < 3 || input.length < 3) {
                            return false;
                        }

                        form_data.append('action', 'myvideoroom_admin_ajax');

                        form_data.append('display_title', display_title);
                        form_data.append('slug', input);
                        form_data.append('type', shortcode);
                        form_data.append('security', myvideoroom_admin_ajax.security);
                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_admin_ajax.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response),
                                    main = $('#mvr-table-basket-frame_main');

                                $('.myvideoroom-roomslug-submit-form').prop('value', state_response.feedback);
                                $('.myvideoroom-roomslug-submit-form').prop('disabled', true);
                                if (state_response.personalmeeting) {
                                    let pmm = $('#mvr-table-basket-frame_personal-meeting-module');
                                    pmm.html(state_response.personalmeeting);
                                }
                                if (state_response.conference) {
                                    let conf = $('#mvr-table-basket-frame_site-conference-room');
                                    conf.html(state_response.conference);
                                }
                                main.empty();
                                if (main) {
                                    main.html(state_response.maintable);
                                }

                                if (window.myvideoroom_monitor_load) {
                                    window.myvideoroom_monitor_load();
                                }
                                init();
                            },
                            error: function(response) {
                                console.log('Error Uploading');
                            }
                        });
                    }
                    /**
                     * Handles Deletion of Room
                     */
                var deleteRoom = function(room_id, nonce, room_name) {

                        var container = $('.mvr-security-room-host');
                        var loading_text = container.data('loadingText');
                        $('.myvideoroom-sitevideo-hide-button').show();

                        var form_data = new FormData();
                        form_data.append('action', 'myvideoroom_admin_ajax');

                        if (room_name === 'delete-approved') {
                            form_data.append('action_taken', 'delete_approved');
                            container.html('<h1 style = "padding:20px" > Delete in Progress.... </h1>');
                        } else {
                            container.html('<h1 style = "padding:20px" > ' + loading_text + '</h1>');
                            form_data.append('action_taken', 'delete_room');

                        }

                        form_data.append('roomId', room_id);
                        form_data.append('roomName', room_name);
                        form_data.append('nonce', nonce);
                        form_data.append('security', myvideoroom_admin_ajax.security);

                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_admin_ajax.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);
                                if (room_name !== 'delete-approved') {
                                    container.html(state_response.mainvideo);

                                    if (window.myvideoroom_tabbed_init) {
                                        window.myvideoroom_tabbed_init(container);
                                    }
                                } else {
                                    sctable = $('#mvr-table-basket-frame_site-conference-room');
                                    $('.mvr-security-room-host').empty();

                                    $('.myvideoroom-sitevideo-hide-button').hide();

                                    var main = $('#mvr-table-basket-frame_main');

                                    if (state_response.personalmeeting) {
                                        let pmm = $('#mvr-table-basket-frame_personal-meeting-module');
                                        pmm.html(state_response.personalmeeting);
                                    }
                                    if (state_response.conference) {
                                        let conf = $('#mvr-table-basket-frame_site-conference-room');
                                        conf.html(state_response.conference);
                                    }
                                    main.empty();
                                    if (main) {
                                        main.html(state_response.maintable);
                                    }

                                    if (window.myvideoroom_monitor_load) {
                                        window.myvideoroom_monitor_load();
                                    }
                                    init();
                                }

                                init();
                            }
                        });

                    }
                    /**
                     * Refresh Tables - used to refresh room page tables (used post module activation)
                     */
                var refreshTables = function() {
                    var form_data = new FormData(),
                        maintable = $('#mvr-table-basket-frame_main');
                    form_data.append('action', 'myvideoroom_admin_ajax');
                    form_data.append('action_taken', 'refresh_tables');
                    form_data.append('security', myvideoroom_admin_ajax.security);
                    $.ajax({
                        type: 'post',
                        dataType: 'html',
                        url: myvideoroom_admin_ajax.ajax_url,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(response) {
                            var state_response = JSON.parse(response);
                            maintable.empty();
                            if (maintable) {
                                maintable.html(state_response.maintable);
                            }
                            if (state_response.personalmeeting) {
                                let pmm = $('#mvr-table-basket-frame_personal-meeting-module');
                                pmm.html(state_response.personalmeeting);
                            }
                            if (state_response.conference) {
                                let conf = $('#mvr-table-basket-frame_site-conference-room');
                                conf.html(state_response.conference);
                            }

                            init();
                            if (window.myvideoroom_monitor_load) {
                                window.myvideoroom_monitor_load();

                            }

                        },
                        error: function() {
                            console.log('Error Refreshing');
                        }
                    });
                }

                /**
                 * BuddyPress User and Group Ajax Tab Functions
                 * Used to update Group Tab Names, and User Video Tab Names from BuddyPress module.
                 */

                /**
                 * Update User Display Name Tab in BuddyPress
                 */
                var updateUsertab = function() {
                    var form_data = new FormData();
                    tab_user_profile = $('#user-profile-input').val();
                    form_data.append('action', 'myvideoroom_admin_ajax');
                    form_data.append('action_taken', 'update_user_tab_name');
                    form_data.append('user_tab_name', tab_user_profile);
                    form_data.append('security', myvideoroom_admin_ajax.security);
                    $.ajax({
                        type: 'post',
                        dataType: 'html',
                        url: myvideoroom_admin_ajax.ajax_url,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function(response) {
                            var state_response = JSON.parse(response);
                            $('#save-user-tab').prop('value', state_response.feedback);

                        },
                        error: function(response) {
                            console.log('Error Uploading');
                        }
                    });
                }

                /**
                 * Update Group Display Tab in BuddyPress
                 */
                var updateGrouptab = function() {
                        var form_data = new FormData();
                        tab_user_profile = $('#group-profile-input').val();
                        form_data.append('action', 'myvideoroom_admin_ajax');
                        form_data.append('action_taken', 'update_group_tab_name');
                        form_data.append('group_tab_name', tab_user_profile);
                        form_data.append('security', myvideoroom_admin_ajax.security);
                        $.ajax({
                            type: 'post',
                            dataType: 'html',
                            url: myvideoroom_admin_ajax.ajax_url,
                            contentType: false,
                            processData: false,
                            data: form_data,
                            success: function(response) {
                                var state_response = JSON.parse(response);
                                $('#save-group-tab').prop('value', state_response.feedback);

                            },
                            error: function(response) {
                                console.log('Error Uploading');
                            }
                        });
                    }
                    /**
                     * Check if Length Conditions Met for Submit Users
                     */
                function checkform() {
                    var input_check = $('#user-profile-input').val().length;
                    $('#save-user-tab').prop('value', 'Save');
                    if (input_check >= 5) {
                        $('#save-user-tab').show();
                        $('#save-user-tab').prop('disabled', false);
                    } else {
                        $('#save-user-tab').hide();
                    }
                    if (input_check < 5) {
                        return false;
                    }
                }
                /**
                 * Check if Length Conditions Met for Submit Groups
                 */
                function checkgroupform() {
                    var input_check = $('#group-profile-input').val().length;
                    $('#save-group-tab').prop('value', 'Save');
                    if (input_check >= 5) {
                        $('#save-group-tab').show();
                        $('#save-group-tab').prop('disabled', false);
                    } else {
                        $('#save-group-tab').hide();
                    }
                    if (input_check < 5) {
                        return false;
                    }
                }

                init();
            }
        );
    }
);