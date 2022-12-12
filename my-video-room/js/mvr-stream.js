window.addEventListener("load", function() {

    function init() {
        jQuery(function($) {
            //Initialise Reception.
            let item = document.querySelector("#Reset_app__30V6t > form > input[type=submit]");
            if (item) {
                item.click();
            }
            /* Disabling Execution outside of MVR */
            var mvrIsactive = document.getElementsByClassName('mvr-nav-shortcode-outer-wrap');

            if (mvrIsactive.length < 1) {
                return false;
            }
            var loginActive = document.getElementsByClassName('mvr-login-form');
            /* Initialise Camera, and Listen to Buttons */
            $('#vid-picture').click(function(e) {
                document.getElementById("vid-picture").classList.add('mvr-hide');
                document.getElementById("vid-up").classList.add('mvr-hide');
                document.getElementById("myvideoroom-picturedescription").classList.remove('mvr-hide');
                document.getElementById("mvr-text-description-new").classList.remove('mvr-hide');
                document.getElementById("mvr-text-description-current").classList.remove('mvr-hide');
                startcamera();
            });

            $('.mvr-button-login').click(function(e) {
                e.preventDefault();
                document.getElementById("mvr-picture").classList.add('mvr-hide');
                if (loginActive.length > 0) {
                    document.getElementById("mvr-login-form").classList.remove('mvr-hide');
                }
                document.getElementById("myvideoroom-checksound").classList.add('mvr-hide');
                document.getElementById("myvideoroom-meeting-name").classList.add('mvr-hide');
                $('#mvr-login-form').slideToggle();
            });

            $('.mvr-photo-image').click(function(e) {
                e.preventDefault();
                document.getElementById("mvr-picture").classList.remove('mvr-hide');
                document.getElementById("vid-picture").classList.remove('mvr-hide');

                if (loginActive.length > 0) {
                    document.getElementById("mvr-login-form").classList.add('mvr-hide');
                }
                document.getElementById("myvideoroom-checksound").classList.add('mvr-hide');
                document.getElementById("myvideoroom-meeting-name").classList.add('mvr-hide');
                $('#mvr-picture').slideToggle();
            });

            $('.mvr-name-user').click(function(e) {
                e.preventDefault();
                skipwindow();
                $('#myvideoroom-meeting-name').slideToggle();

            });

            $('.mvr-check-sound').click(function(e) {
                e.preventDefault();
                document.getElementById("mvr-picture").classList.add('mvr-hide');
                if (loginActive.length > 0) {
                    document.getElementById("mvr-login-form").classList.add('mvr-hide');
                }
                document.getElementById("myvideoroom-meeting-name").classList.add('mvr-hide');

                $('#myvideoroom-checksound').slideToggle();

                document.getElementById("myvideoroom-checksound").classList.remove('mvr-hide');
            });
            $('#vid-down').click(function(e) {
                startmeeting();
            });

            $('#chk-sound').click(function(e) {
                e.preventDefault();
                document.getElementById("myvideoroom-checksound").classList.remove('myvideoroom-center');
                checksound();
            });

            $('#stop-chk-sound').click(function(e) {
                window.location.reload();
            });
            $('#room-name-update').click(function(e) {
                e.preventDefault();
                updateName();
            });
            $('#vid-name').keydown(function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    updateName();
                }
            });

            $('.mvr-forget-me').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                deleteMe();
            });

            $('.myvideoroom-clipboard-copy').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                textToClipboard = $(this).parent().attr('data-id');
                navigator.clipboard.writeText(textToClipboard);
                alert("Copied: " + textToClipboard + ' to clipboard.');
            });



            var text = document.getElementById('vid-name');
            if (text && Object.keys(text).length === 0 && Object.getPrototypeOf(text)) {
                document.getElementById("vid-down").disabled = false;
                document.getElementById("vid-down").onclick = startmeeting;
            }

            if ($('#myvideoroom-welcome-setup').length) {
                var existsvalue = document.getElementById('myvideoroom-welcome-setup').innerHTML;
                if (existsvalue.length > 1) {
                    $('.mvr-name-user').click();
                    $('.myvideoroom-app').hide();
                    $('.mvr-forget-me').hide();
                    $('#mvr-above-article-notification').html('<br><strong>The Video Room will be available when you complete your welcome</strong>');
                }
            }
            $('#mvr-file-input').on('change', imageUpload);
        });

    }

    function imageUpload(event) {
        event.stopPropagation();
        jQuery(function($) {
            document.getElementById("upload-picture").classList.remove('mvr-hide');
            var file = event.target.files;
            var form_data = new FormData();
            $.each(file, function(key, value) {
                form_data.append("upimage", value);
            });

            form_data.append('action', 'myvideoroom_base_ajax');

            var room_name = $('#roominfo').data('roomName');
            form_data.append('room_name', room_name);
            form_data.append('action_taken', 'update_picture');
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
                    console.log(state_response.message);
                    if (state_response.errormessage) {
                        console.log(state_response.errormessage);
                    }
                    let notify = document.getElementById("mvr-top-notification");
                    if (typeof notify !== null) {
                        notify.innerHTML += '<br><h3>' + state_response.message + '</h3><br>';
                    }
                    $('#vid-up').prop('value', 'Saved !');
                },
                error: function(response) {
                    console.log('Error Uploading');
                }
            });

            setTimeout(() => { refreshWelcome(); }, 2000);
        });

    }

    function skipwindow() {
        document.getElementById("mvr-picture").classList.add('mvr-hide');
        document.getElementById("myvideoroom-meeting-name").classList.remove('mvr-hide');
        document.getElementById("myvideoroom-checksound").classList.add('mvr-hide');
        document.getElementById("myvideoroom-meeting-name").classList.add('myvideoroom-center');
    }


    function startcamera() {

        document.getElementById("vid-take").classList.remove('mvr-hide');
        navigator.mediaDevices.getUserMedia({

            video: {
                width: { min: 213, ideal: 1024, max: 1920 },
                height: { min: 120, ideal: 576, max: 1080 }
            }
        })


        .then(function(stream) {

            var video = document.getElementById("vid-live");
            video.srcObject = stream;
            video.play();

            document.getElementById("vid-take").onclick = vidtake;
            document.getElementById("vid-up").onclick = vidup;
            document.getElementById("vid-retake").onclick = retakevideo;

        })

        // Handle Error.
        .catch(function(err) {
            alert(err + " Please enable access and attach a webcam");
        });
        setTimeout(function() {
            cameratimeout();
        }, 30000);
    }

    function vidtake() {
        /* Create Canvas */
        var video = document.getElementById("vid-live"),
            canvas = document.createElement("canvas"),
            context2D = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context2D.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
        var wrap = document.getElementById("vid-result");
        wrap.innerHTML = "";
        wrap.appendChild(canvas);

        /* Arrange Buttons for Retake, or Accept Image */
        jQuery(function($) {
            $('#vid-up').prop('value', 'Use This');
        });
        document.getElementById("vid-result").classList.remove('mvr-hide');
        document.getElementById("vid-retake").classList.remove('mvr-hide');
        document.getElementById("vid-up").classList.remove('mvr-hide');
        document.getElementById("vid-live").classList.add('mvr-hide');
        document.getElementById("vid-take").classList.add('mvr-hide');

        stopcamera();
    }

    function stopcamera() {
        navigator.mediaDevices.getUserMedia({
                // Resolution
                video: {
                    width: { min: 213, ideal: 1024, max: 1920 },
                    height: { min: 120, ideal: 576, max: 1080 }
                }
            })
            .then(function(stream) {

                var video = document.getElementById("vid-live");
                video.srcObject = stream;

                stream.getTracks().forEach(track => track.stop())
            })

    }

    function cameratimeout() {
        navigator.mediaDevices.getUserMedia({
                // Resolution
                video: {
                    width: { min: 213, ideal: 1024, max: 1920 },
                    height: { min: 120, ideal: 576, max: 1080 }
                }
            })
            .then(function(stream) {

                var video = document.getElementById("vid-live");
                video.srcObject = stream;

                stream.getTracks().forEach(track => track.stop())
            })

        console.log('Stopcamera Command Sent');
        document.getElementById("vid-retake").classList.remove('mvr-hide');
        document.getElementById("vid-take").classList.add('mvr-hide');
        document.getElementById("vid-live").classList.add('mvr-hide');
    }

    function retakevideo() {
        document.getElementById("myvideoroom-picturedescription").classList.remove('mvr-hide');
        stopcamera();
        /* Reset Buttons for Retake */
        document.getElementById("vid-result").innerHTML = "";
        document.getElementById("vid-live").classList.remove('mvr-hide');
        document.getElementById("vid-result").classList.add('mvr-hide');
        document.getElementById("vid-up").classList.add('mvr-hide');
        document.getElementById("vid-take").classList.remove('mvr-hide');
        document.getElementById("vid-retake").classList.add('mvr-hide');
        document.getElementById("vid-up").value = "Use This";

        startcamera();

    }


    function vidup() {

        canvas = document.querySelector('canvas');
        context2D = canvas.getContext("2d");
        canvas.toBlob(function(blob) {

            // Prepare Form.
            var form_data = new FormData();
            form_data.append('upimage', blob);
            form_data.append('action', 'myvideoroom_base_ajax');

            jQuery(function($) {
                var room_name = $('#roominfo').data('roomName');
                form_data.append('room_name', room_name);
                form_data.append('action_taken', 'update_picture');
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
                        if (state_response.errormessage) {
                            console.log(state_response.errormessage);
                        }
                        console.log(state_response.message);
                        document.getElementById("mvr-top-notification").innerHTML += '<br>';
                        $('#vid-up').prop('value', 'Saved !');
                    },
                    error: function(response) {
                        console.log('Error Uploading');
                    }
                });
            });

        });
        setTimeout(() => { refreshWelcome(); }, 2000);
    }

    function updateName() {
        var textvalue = document.getElementById('vid-name').value;

        if (textvalue.length < 1) {
            alert('You can not enter a blank Display Name');
            return false;
        }

        // Prepare Form.
        var form_data = new FormData();
        form_data.append('action', 'myvideoroom_base_ajax');

        jQuery(function($) {
            console.log('Start Name Update');
            notification = $('#mvr-above-article-notification');
            var room_name = $('#roominfo').data('roomName'),
                status_message = $('#mvr-postbutton-notification'),
                display_name = $('#vid-name').val();
            form_data.append('room_name', room_name);
            form_data.append('display_name', display_name);

            form_data.append('action_taken', 'update_display_name');
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
                    notification.empty();
                    if (state_response.feedback) {
                        status_message.html(state_response.feedback);
                        setTimeout(function() {
                            status_message.fadeOut();
                        }, 6000);
                        setTimeout(function() {
                            status_message.empty();
                            $(status_message).removeAttr('style');
                        }, 8000);
                    }
                    refreshWelcome();
                    if (state_response.errormessage) {
                        console.log(state_response.errormessage);
                    }
                    $('.mvr-forget-me').show();;
                },
                error: function(response) {
                    console.log('Error Uploading');
                }
            });
        });

    }

    function deleteMe() {

        // Prepare Form.
        var form_data = new FormData();
        form_data.append('action', 'myvideoroom_base_ajax');

        jQuery(function($) {
            console.log('Picture Delete');
            var room_name = $('#roominfo').data('roomName');
            display_name = $('#vid-name').val(),
                form_data.append('room_name', room_name);
            form_data.append('display_name', display_name);

            form_data.append('action_taken', 'delete_me');
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
                    if (state_response.errormessage) {
                        console.log(state_response.errormessage);
                    }
                    $('.mvr-forget-me').hide();
                    setTimeout(() => { window.location.reload(); }, 1500);;
                },
                error: function(response) {
                    console.log('Error Deleting');
                }
            });
        });

        document.getElementById("mvr-top-notification").innerHTML += '<br><div><strong>Your Records have been deleted</strong></div>';

    }


    function refreshWelcome() {
        // Prepare Form.
        var form_data = new FormData();
        form_data.append('action', 'myvideoroom_base_ajax');

        jQuery(function($) {

            var room_name = $('#roominfo').data('roomName'),
                container = $('#myvideoroom-welcome-page');

            form_data.append('room_name', room_name);
            form_data.append('security', myvideoroom_base_ajax.security);
            form_data.append('action_taken', 'refresh_page');

            $.ajax({
                type: 'post',
                dataType: 'html',
                url: myvideoroom_base_ajax.ajax_url,
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {
                    var state_response = JSON.parse(response);

                    if (state_response.welcome) {
                        container.html(state_response.welcome);
                    }

                    if (state_response.errormessage) {
                        console.log(state_response.errormessage);
                    }
                    resetPanel();
                    init();
                },
                error: function(response) {
                    console.log('Error RefreshWelcome');
                }
            });
        });

    }

    function resetPanel() {
        jQuery(function($) {
            $('.myvideoroom-app').show();
            startmeeting(true);
        });
    }

    function checksound() {
        console.log('Check sound starting');
        document.getElementById("stop-chk-sound").classList.remove('mvr-hide');
        // Prepare Form.
        var form_data = new FormData();
        form_data.append('action', 'myvideoroom_base_ajax');

        jQuery(function($) {
            container = $('.myvideoroom-app');
            notification = $('#mvr-above-article-notification');
            form_data.append('security', myvideoroom_base_ajax.security);
            form_data.append('action_taken', 'check_sound');
            $.ajax({
                type: 'post',
                dataType: 'html',
                url: myvideoroom_base_ajax.ajax_url,
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {

                    var state_response = JSON.parse(response);
                    if (state_response.errormessage) {
                        console.log(state_response.errormessage);
                    }
                    if (state_response.mainvideo) {
                        refreshTarget(container, state_response.mainvideo);
                    }
                    if (state_response.message) {
                        notification.html(state_response.message);
                    }
                    init();

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

                    $('#vid-up').prop('value', 'Saved !');
                },
                error: function(response) {
                    console.log('Error Uploading');
                }
            });
        });
        // Change Focus to Video Tab.
        document.getElementById('mvr-video').click();
    }


    function startmeeting(skip) {
        // Prepare Form.
        var form_data = new FormData();
        form_data.append('action', 'myvideoroom_base_ajax');

        jQuery(function($) {
            var room_name = $('#roominfo').data('roomName'),
                checksum = $('#roominfo').data('checksum'),
                room_type = $('#roominfo').data('roomType'),
                display_name = $('#vid-name').val(),
                original_room = $('.myvideoroom-app').data('roomName'),
                status_message = $('#mvr-postbutton-notification'),
                container = $('.myvideoroom-app');
            console.log(checksum + room_type);
            form_data.append('room_name', room_name);
            form_data.append('security', myvideoroom_base_ajax.security);
            form_data.append('display_name', display_name);
            form_data.append('original_room', original_room);
            form_data.append('action_taken', 'start_meeting');
            form_data.append('checksum', checksum);
            form_data.append('roomType', room_type);

            $.ajax({
                type: 'post',
                dataType: 'html',
                url: myvideoroom_base_ajax.ajax_url,
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {
                    var state_response = JSON.parse(response);

                    if (state_response.mainvideo) {
                        refreshTarget(container, state_response.mainvideo);
                    }

                    if (state_response.feedback) {
                        status_message.html(state_response.feedback);
                        setTimeout(function() {
                            status_message.fadeOut();
                        }, 6000);
                        setTimeout(function() {
                            status_message.empty();
                            $(status_message).removeAttr('style');
                        }, 8000);
                    }

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

                    $('#vid-up').prop('value', 'Saved !');
                },
                error: function(response) {
                    console.log('Error Startmeeting Handler');
                }
            });
        });
        // Change Focus to Video Tab.
        if (typeof skip == 'undefined') {
            document.getElementById('mvr-video').click();
        }

    }

    let vidnamecheck = document.getElementById("vid-name");
    if (vidnamecheck) {
        document.getElementById("vid-name").onkeyup = function() {
            document.getElementById("vid-name").innerHTML = '';
            document.getElementById("vid-down").disabled = false;
        };
    }


    init();


});

function refreshTarget(source_element, ajax_response, video_skip) {
    // Hard Delete of Content in Parent Container to Avoid Duplication in replacement.
    if (source_element.length === 0) {
        console.log('Source Element Empty- Exiting');
        return false
    }
    mainvideo_parent = source_element.parent().attr('id');
    source_element.remove();
    source_element.parent().empty();
    let item = document.getElementById(mainvideo_parent);
    if (item && Object.keys(item).length >= 1) {
        item.innerHTML = ajax_response;
    } else {
        console.log('empty parent');
    }

    if (!video_skip) {
        reloadVideo();
    }
}

function reloadVideo() {
    jQuery(function($) {
        // WordPress may add custom headers to the request, this is likely to trigger CORS issues, so we remove them.
        if ($.ajaxSettings && $.ajaxSettings.headers) {
            delete $.ajaxSettings.headers;
        }

        $.ajax({
            url: myVideoRoomAppEndpoint + '/asset-manifest.json',
            dataType: 'json'
        }).then(
            function(data) {
                Object.values(data.files).map(
                    function(file) {
                        var url = myVideoRoomAppEndpoint + '/' + file;

                        if (file.endsWith('.js')) {
                            $.ajax({
                                beforeSend: function() {},
                                url: url,
                                dataType: 'script'
                            });
                        } else if (file.endsWith('.css')) {
                            $('<link rel="stylesheet" type="text/css" />')
                                .attr('href', url)
                                .appendTo('head');
                        }
                    }
                );
            }
        );
        $('#mvr-video').click();
    });

}