var $ = jQuery.noConflict();

$(document).ready(function () {
    var $clubCloudShortCode = $('#clubcloud_shortcode'),
        $clubCloudReceptionShortCode = $('#club_reception_shortcode'),
        $body = $('body'),

        ccIsReception = $clubCloudShortCode.attr('cc_is_reception'),
        eventPrefix = $clubCloudShortCode.attr('cc_event_id') + '-',
        ccAuth = $clubCloudShortCode.attr('auth'),
        ytUrl = $clubCloudShortCode.attr('yt_url'),

        floorPlanFile = wpApiSettings.floorplan + $clubCloudShortCode.attr('cc_plan_id') + '.gif',
        tableDataMap = wpApiSettings.floorplan + $clubCloudShortCode.attr('cc_plan_id') + '.json',
        cartcCnnect = wpApiSettings.enablecart,

        ccQueueId = null,
        ccQueuePosition = null,
        ccQueueName = null,
        ccTableArray = [],
        ccHasReceptionSC = $clubCloudReceptionShortCode.length > 0,
        ccUsername = null,
        currentRoom = null,
        cartSync = null,
        eventPrefixLower = eventPrefix.toLowerCase(),
        isRunning = false,
        connectionIDVar = 'Starting',
        qDisplayInetValid = null,
        normInterval = null;

    $body.delegate(".launchtable", "click", function () {
        var connectTableNumber = $(this).attr('id');
        connectmeeting(connectTableNumber.toLowerCase(), connectTableNumber, 'empty')

    });

    $body.delegate(".accessreq", "click", function () {
        var jidInfo = $(this).attr('id');
        requestCartAccess(jidInfo)

    });

    buildpage()

    jconfirm.defaults = {
        boxWidth: '500px',
        useBootstrap: false,
        draggable: true,
    }

    function connectmeeting(room, subject, cc_token) {
        //alert(connection_id_var)
        if (currentRoom !== room) {
            if (isRunning && typeof (api) !== 'undefined') {
                api.executeCommand('hangup');
                api.dispose();
            }

            jQuery.ajax({
                url: wpApiSettings.ajax_url,
                type: 'post',
                data: {
                    action: 'set_connectdata',
                    table_id: room,
                    auth: ccAuth,
                    envelope: wpApiSettings.envelope,
                    temptoken: cc_token,
                    currentjid: connectionIDVar,
                },
                success: function (response) {
                    //alert(response)
                    startvideo(room, subject, response[0])
                }
            });
        }
    }


    function startvideo($roomname, $subject, $connection) {
        ccUsername = $connection.displayname;
        userName = $connection.displayname;
        userEmail = $connection.mailaddy ?? null;
        domain = $connection.videodomain;
        roomName = $roomname;
        jwt = $connection.jwt;
        myAvatar = $connection.myavatar ?? null;
        lobby = $clubCloudShortCode.attr('cc_enable_lobby');
        height = $clubCloudShortCode.attr('height');
        width = $clubCloudShortCode.attr('width');
        var options = {};

        roomName ? options.roomName = roomName : null;
        jwt ? options.jwt = jwt : null;
        userName || userEmail ? options.userInfo = {} : null;
        userName ? options.userInfo.displayName = userName : null;
        userEmail ? options.userInfo.email = userEmail : null;
        options.parentNode = document.getElementById('TableVideo')
        height ? options.height = height : null;
        width ? options.width = width : null;
        var api = new JitsiMeetExternalAPI(domain, options);
        currentRoom = roomName
        api.addEventListener('videoConferenceJoined', function (connectiondata) {
            connectionIDVar = connectiondata.id + '@' + connectiondata.roomName
        })

        userName ? api.executeCommand('displayName', userName) : null;
        api.executeCommand("avatarUrl", myAvatar);
        api.executeCommand("subject", $subject);
        api.addEventListener("participantRoleChanged", function (event) {

            var password = 'password';

            if (event.role === 'moderator') {
                // api.executeCommand('password', password);

                if (lobby === "true") {
                    api.executeCommand("toggleLobby", true);
                }
            } else {
                // setTimeout(function () {
                //     // why timeout: I got some trouble calling event listeners without setting a timeout :)
                //
                //     // when local user is trying to enter in a locked room
                //     api.addEventListener('passwordRequired', function () {
                //         api.executeCommand('password', password);
                //     });
                //
                //     // when local user has joined the video conference
                //     api.addEventListener('videoConferenceJoined', function () {
                //         setTimeout(function () {
                //             api.executeCommand('password', password);
                //         }, 0);
                //     });
                //
                // }, 0);
            }
        });

        api.addEventListener("endpointTextMessageReceived", function (event) {
            messagehandler(event.data.eventData.text)
        })


        api.on("readyToClose", function () {
            api.dispose();
            connectionIDVar = 'Starting'
        });
        window.api = api
        isRunning = true
        console.log(options);
    }


    function buildpage() {
        $clubCloudShortCode.append('<div id="MainColumns" style="width:100%"></div>')
        mainwidth = ($('#MainColumns').width() / 2) - 1;
        $('#MainColumns').append('<div id="TableVideo"></div>')
        $clubCloudShortCode.append('<div id="ccTablePlan" class="responsive-hotspot-wrap" ></div>')
        if (ccIsReception == 'true') {
            $('#ccTablePlan').append('<div class="video-link" data-video-id="y-9Auq9mYxFEE"><img id ="ReceptionImage" src="' + wpApiSettings.floorplan + $clubCloudShortCode.attr('cc_receptionimage') + '" class="img-responsive"></img></div>')
            videoLightning({
                elements: [
                    {
                        ".video-link": {
                            id: "y-9Auq9mYxFEE",
                            autoplay: true,
                            color: "white"
                        }
                    }
                ]
            });
            $.confirm({
                title: 'Clubcloud video call queueing system',
                content: '' +
                    '<form action="" class="cc_name_form">' +
                    '<div class="form-group">' +
                    '<label>Please enter yor name</label>' +
                    '<input type="text" placeholder="Your name" class="name form-control" required />' +
                    '</div>' +
                    '</form>',
                buttons: {
                    formSubmit: {
                        text: 'Submit',
                        btnClass: 'btn-blue',
                        action: function () {
                            ccQueueName = this.$content.find('.name').val();
                            if (!ccQueueName) {
                                $.alert('provide a valid name');
                                return false;
                            }
                            $.alert({
                                title: 'Hi there ' + ccQueueName,
                                content: 'You are in the queue please enjoy some tv while you wait just click the screen',
                            });
                            if (normInterval == null) {
                                setInterval(cc_queue, 7500)
                            }

                        }
                    },
                    cancel: function () {
                        //close
                    },
                },
                onContentReady: function () {
                    // bind to events
                    var jc = this;
                    this.$content.find('form').on('submit', function (e) {
                        // if the user submits the form by pressing enter in the field.
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click'); // reference the button and click it
                    });
                }
            });

        } else {
            $('#ccTablePlan').append('<img id ="FloorPlanImage" src="' + floorPlanFile + '" class="img-responsive"></img>')
            if (ytUrl != null) {
                $('#KeynoteVideo').append('<iframe src="' + ytUrl + '" frameborder="0" allowfullscreen ></iframe>')
            }
            $.getJSON(tableDataMap, function (results) {
                $.each(results, function (i, table) {
                    $('#ccTablePlan').append('<div id="' + eventPrefixLower + table.TableName.toLowerCase() + '" class="launchtable"></div>')
                    currtable = '#' + eventPrefixLower + table.TableName.toLowerCase()
                    ccTableArray.push(currtable.replace('#', ''))
                    $.each(table.Seating, function (b, seats) {
                        $(currtable).append('<div id="' + eventPrefixLower + table.TableName.toLowerCase() + seats.Seat.toLowerCase() + '" class="hot-spot" Style="top:' + seats.ypc + ';left:' + seats.xpc + '"><div class="circle"></div><div id="' + eventPrefixLower + table.TableName.toLowerCase() + seats.Seat.toLowerCase() + 'tip" class="tooltip">' + seats.Seat + '</div>')

                        dropseat = '#' + eventPrefixLower + table.TableName.toLowerCase() + seats.Seat.toLowerCase()
                        $(dropseat).droppable({
                            drop: personmoved
                        });
                    })
                })
            });

            if (normInterval == null) {
                setInterval(cc_queue, 7500)
            }
        }

    }

    function cc_queue_display() {
        $.confirm({
            title: 'Queue Status',
            content: 'You are number ' + ccQueuePosition + ' in the queue',
            autoClose: 'Close|5000',
            buttons: {
                Close: function () {
                }
            },
        })
    }

    function cc_queue() {
        if (ccIsReception == 'true') {

            //alert(ccQueueName)
            $.ajax({
                url: wpApiSettings.ajax_url,
                type: 'post',
                data: {
                    action: 'cc_call_queue',
                    event_id: eventPrefixLower,
                    cc_guest_name: ccQueueName,
                    qid: ccQueueId,
                    envelope: wpApiSettings.envelope,
                },
                success: function (response) {
                    switch (response.action) {
                        case 'insert':
                            ccQueueId = response.dbid
                            qDisplayInetValid = setInterval(cc_queue_display, 45000)
                            break;
                        case 'update':
                            ccQueuePosition = response.qpos + 1
                            break;
                        case 'move':
                            ccIsReception = 'false'
                            $('#ccTablePlan').empty()
                            clearInterval(qDisplayInetValid);
                            buildpage()
                            connectmeeting(response.move, response.move, response.temptoken)
                            break;

                    }

                }
            })
        } else {

            $.ajax({
                url: wpApiSettings.ajax_url,
                type: 'post',
                data: {
                    action: 'cc_get_connectiondata',
                    table_list: ccTableArray,
                    connection_id: connectionIDVar,
                    envelope: wpApiSettings.envelope,
                },
                success: function (response) {
                    if (response.move) {

                        connectmeeting(response.move, response.move, response.temptoken)


                    }
                    //alert("After Return -" + JSON.stringify(response))
                    $('.hot-spot').each(function (h, tooltipid) {
                        $(tooltipid).css('background-image', 'none')
                    });

                    $.each(response, (function (h, tableentry) {
                        // if(tableentry.currentuser != 1 ){alert(tableentry.currentuser)}
                        currtable = tableentry.RoomName
                        $.each(tableentry.Participants, (function (j, seatentry) {
                            seatno = j + 1
                            currentseat = '#' + tableentry.RoomName + 'seat' + seatno
                            if (seatentry.AvatarUrl) {
                                partyavatarurl = seatentry.AvatarUrl
                                currentdisplayname = seatentry.DisplayName

                            } else {
                                partyavatarurl = wpApiSettings.floorplan + 'guest.jpg'
                                currentdisplayname = "GuestUser"
                            }

                            currentseattip = currentseat + 'tip'
                            $(currentseat).css('background-image', 'url("' + partyavatarurl + '")')
                            $(currentseat).attr('jid', seatentry.JiD)
                            if (wpApiSettings.dragenabled) {
                                $(currentseat).draggable({
                                    containment: '#ccTablePlan',
                                    cursor: 'move',
                                    helper: 'clone',
                                });

                            }
                            $(currentseattip).html('<span class="avatar-container"><img src="' + partyavatarurl + '" class="avatar"/><p>' + currentdisplayname + '</p></span>')
                            if (cartcCnnect == 'yes' && wpApiSettings.dragenabled && connectionIDVar != seatentry.JiD && currtable == connectionIDVar.split('@')[1]) {
                                $(currentseattip).append('<p>Request Control of this users cart</p><button id="' + seatentry.JiD + '" class="accessreq" type="button">Access Cart</button>')

                            }
                        }))
                    }))

                }
            })
            if (ccHasReceptionSC == true) {
                get_q_data()
            }
        }


    }

    function cc_route_connected(jid, room_id) {
        $.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_callroute',
                table_id: room_id,
                jidnumber: jid,
                envelope: wpApiSettings.envelope,
            },
            success: function (response) {
                wpApiSettings.jid = jid
            }
        });

    }

    function personmoved(event, ui) {
        if (ui.draggable.attr('id').startsWith('userid-')) {
            cc_draggableid = '#' + ui.draggable.attr('id')
            cc_dragged_user_qid = ui.draggable.attr('id').split('-')
            //alert(cc_dragged_user_qid)
            cc_move_to_child = '#' + event.target.id;
            cc_move_room = $(cc_move_to_child).parent().attr('id')
            //alert(cc_move_room)
            $.confirm({
                title: 'You are moving a user to ' + cc_move_room,
                content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<label>Check this box to make the user a moderator in this room</label>' +
                    '<input id="cc_get_moderator" type="checkbox" class="cc_moderator_val form-control"/>' +
                    '</div>' +
                    '</form>' +
                    'This may take up to 10 seconds after this box closes to execute please wait...',
                buttons: {
                    formSubmit: {
                        text: 'Submit',
                        btnClass: 'btn-blue',
                        action: function () {
                            moderator = this.$content.find('#cc_get_moderator').is(':checked');

                            $.ajax({
                                url: wpApiSettings.ajax_url,
                                type: 'post',
                                data: {
                                    action: 'cc_set_route_in',
                                    table_id: cc_move_room,
                                    qidnumber: cc_dragged_user_qid[1],
                                    moderator_mv: moderator,
                                    envelope: wpApiSettings.envelope,
                                },
                                success: function (response) {
                                    $(cc_draggableid).remove()
                                    //alert(response)
                                }

                            });
                        }
                    },
                    cancel: function () {
                        //close

                    },
                },
                onContentReady: function () {
                    // bind to events
                    var jc = this;
                    this.$content.find('form').on('submit', function (e) {
                        // if the user submits the form by pressing enter in the field.
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click'); // reference the button and click it
                    });
                }
            });

        } else {
            cc_dragged_person = ui.draggable.attr('jid');
            cc_move_to_child = '#' + event.target.id;
            cc_move_room = $(cc_move_to_child).parent().attr('id')

            $.confirm({
                title: 'You are moving a user to ' + cc_move_room,
                content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<label>Check this box to make the user a moderator in this room</label>' +
                    '<input id="cc_get_moderator" type="checkbox" class="cc_moderator_val form-control"/>' +
                    '</div>' +
                    '</form>' +
                    'This may take up to 10 seconds after this box closes to execute please wait...',
                buttons: {
                    formSubmit: {
                        text: 'Submit',
                        btnClass: 'btn-blue',
                        action: function () {
                            moderator = this.$content.find('#cc_get_moderator').is(':checked');

                            $.ajax({
                                url: wpApiSettings.ajax_url,
                                type: 'post',
                                data: {
                                    action: 'cc_set_route',
                                    table_id: cc_move_room,
                                    jidnumber: cc_dragged_person,
                                    moderator_mv: moderator,
                                    envelope: wpApiSettings.envelope,
                                },
                                success: function (response) {
                                    //alert('Data Parsed')
                                }

                            });
                        }
                    },
                    cancel: function () {
                        //close

                    },
                },
                onContentReady: function () {
                    // bind to events
                    var jc = this;
                    this.$content.find('form').on('submit', function (e) {
                        // if the user submits the form by pressing enter in the field.
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click'); // reference the button and click it
                    });
                }
            });

        }

        //alert( 'The square with ID "' + cc_dragged_person + '" was dropped onto ' + cc_move_room);
    }

    function requestCartAccess(user) {
        jid = user.split("@");
        messagedata = {}
        messagedata['userJID'] = connectionIDVar
        messagedata['username'] = ccUsername
        messagedata['message'] = 'RequestCartAccess'
        api.executeCommand('sendEndpointTextMessage', jid[0], messagedata);
    }


    function messagehandler(message) {
        //$userdata = $.parseJSON(message)

        sendinguser = message.userJID.split("@");
        sendinguserjid = sendinguser[0]
        sendingroomname = sendinguser[1]
        workmessage = message.message
        sendingusername = message.username
        //alert($userdata[0])
        switch (workmessage) {
            case 'RequestCartAccess':
                // alert('RequestCartAccess')
                $.confirm({
                    title: sendingusername + ' would like to access your shopping cart?',
                    content: 'All items in your cart will be cleared for this process.',
                    buttons: {
                        cc_allow: {
                            text: 'Allow Access',
                            btnClass: 'btn-blue',
                            action: function () {
                                cartSync = true

                                messagedata = {}
                                messagedata['userJID'] = connectionIDVar
                                messagedata['username'] = ccUsername
                                messagedata['message'] = 'CartAccessGranted'
                                api.executeCommand('sendEndpointTextMessage', sendinguserjid, messagedata);
                                clearCart()

                                $.alert('Cart Access granted');

                            }
                        },
                        cc_reject: {
                            text: 'No Access',
                            btnClass: 'btn-blue',
                            action: function () {
                                $.alert('Access rejected');

                            }
                        }
                    }
                });

                break;
            case 'CartAccessGranted':
                $.confirm({
                    title: 'Cart synchronisation accepted by ' + sendingusername,
                    content: 'You may now add items to your cart and they will be synchronised to USER. Be sure not to leave the meeting while doing so or you will loose the data and have to start again.',
                    type: 'green',
                    typeAnimated: true,
                    buttons: {
                        close: function () {
                        }
                    }
                });
                clearCart()
                setInterval(cart_updates_get, 7000)

                //clearInterval(refreshIntervalId);

                break;
            case 'CartUpdateSend':
                cart_updates_set(message.data)
                break;

            default:
            // code block
        }

    }

    function clearCart() {
        $.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_clearcart',
                data: 'clear',
            },
            success: function (response) {
                $(document.body).trigger('wc_fragment_refresh');
            }

        });

    }

    function cart_updates_get() {
        $.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_getcart',
                data: 'data',
            },
            success: function (response) {
                messagedata = {}
                messagedata['userJID'] = connectionIDVar
                messagedata['username'] = ccUsername
                messagedata['message'] = 'CartUpdateSend'
                messagedata['data'] = response
                api.executeCommand('sendEndpointTextMessage', sendinguserjid, messagedata);
                $(document.body).trigger('wc_fragment_refresh');


                //alert(bla)
            }

        });


    }

    function cart_updates_set(cartdata) {
        //alert(cartdata)
        $.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_setcart',
                data: cartdata,
            },
            success: function (response) {

                $(document.body).trigger('wc_fragment_refresh');

                //alert(bla)
            }

        });


    }

    function get_q_data() {
        $.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_get_call_queue',
                callkey: eventPrefixLower,
            },
            success: function (response) {
                $.each(response, function (k, queueduser) {
                    userqid = '#userid-' + queueduser.guestqid
                    if ($(userqid).length) {
                    } else {
                        $('#club_reception_shortcode').append('<div id=userid-' + queueduser.guestqid + ' style="z-index:10"><p><img src="' + wpApiSettings.floorplan + 'guest.jpg" style="border-radius: 50%;width:30px" />' + queueduser.guestname + '</p></div>');
                        $(userqid).draggable({
                            containment: 'document',
                            cursor: 'move',
                            helper: 'clone',
                        });
                    }
                });

            }

        })
    }


});
