var $ = jQuery.noConflict();
jQuery(document).ready(function () {
    cc_running_interval = null
    cc_queue_id = null
    cc_queue_pos = null
    cc_queue_name = null
    cc_table_array = new Array()
    cc_is_reception = jQuery('#clubcloud_shortcode').attr('cc_is_reception')
    cc_has_reception_sc = null
    cc_username = null
    currentroom = null
    event_prefix = jQuery('#clubcloud_shortcode').attr('cc_event_id') + '-'
    floorplanfile = wpApiSettings.floorplan + jQuery('#clubcloud_shortcode').attr('cc_plan_id') + '.gif'
    tabledatamap = wpApiSettings.floorplan + jQuery('#clubcloud_shortcode').attr('cc_plan_id') + '.json'
    cartconnect = wpApiSettings.enablecart
    cartsync = null
    cc_auth = jQuery('#clubcloud_shortcode').attr('auth')
    yt_url = jQuery('#clubcloud_shortcode').attr('yt_url')
    event_prefix_lower = event_prefix.toLowerCase();
    isrunning = false
    connection_id_var = 'Starting'
    qdisplayinetvalid = null
    norminterval = null


    jQuery("body").delegate(".launchtable", "click", function () {

        var connecttableno = jQuery(this).attr('id');
        connectmeeting(connecttableno.toLowerCase(), connecttableno, 'empty')

    });

    jQuery("body").delegate(".accessreq", "click", function () {

        var jidinfo = jQuery(this).attr('id');
        request_cart_access(jidinfo)

    });

    buildpage()

    if ($('#club_reception_shortcode').length) {
        cc_has_reception_sc = true
    }


    jconfirm.defaults = {
        boxWidth: '500px',
        useBootstrap: false,
        draggable: true,
    }
});


function connectmeeting(room, subject, cc_token) {
    //alert(connection_id_var)
    if (currentroom == room) {
    } else {
        if (isrunning == true) {
            if (typeof (api) == 'undefined') {
            } else {
                api.executeCommand('hangup');
                api.dispose();
            }
        }

        jQuery.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'set_connectdata',
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
};


function startvideo($roomname, $subject, $connection) {
    cc_username = $connection.displayname
    userName = $connection.displayname
    userEmail = $connection.mailaddy ?? null;
    domain = $connection.videodomain
    roomName = $roomname
    jwt = $connection.jwt
    myAvatar = $connection.myavatar ?? null;
    lobby = jQuery('#clubcloud_shortcode').attr('cc_enable_lobby')
    height = jQuery('#clubcloud_shortcode').attr('height')
    width = jQuery('#clubcloud_shortcode').attr('width')
    let options = {};

    roomName ? options.roomName = roomName : null;
    jwt ? options.jwt = jwt : null;
    userName || userEmail ? options.userInfo = {} : null;
    userName ? options.userInfo.displayName = userName : null;
    userEmail ? options.userInfo.email = userEmail : null;
    options.parentNode = document.getElementById('TableVideo')
    height ? options.height = height : null;
    width ? options.width = width : null;
    const api = new JitsiMeetExternalAPI(domain, options);
    currentroom = roomName
    api.addEventListener('videoConferenceJoined', function (connectiondata) {
        connection_id_var = connectiondata.id + '@' + connectiondata.roomName
    })

    userName ? api.executeCommand(`displayName`, userName) : null;
    api.executeCommand("avatarUrl", myAvatar);
    api.executeCommand("subject", $subject);
    api.addEventListener("participantRoleChanged", function (event) {
        if (event.role === "moderator") {
            if (lobby === "true") {
                api.executeCommand("toggleLobby", true);
            }
        }
    });
    api.addEventListener("endpointTextMessageReceived", function (event) {
        messagehandler(event.data.eventData.text)
    })


    api.on("readyToClose", () => {
        api.dispose();
        connection_id_var = 'Starting'
    });
    window.api = api
    isrunning = true
    console.log(options);


};


function buildpage() {
    jQuery('#clubcloud_shortcode').append('<div id="MainColumns" style="width:100%"></div>')
    mainwidth = (jQuery('#MainColumns').width() / 2) - 1;
    jQuery('#MainColumns').append('<div id="TableVideo"></div>')
    jQuery('#clubcloud_shortcode').append('<div id="TablePlan" class="responsive-hotspot-wrap" ></div>')
    if (cc_is_reception == 'true') {
        $('#TablePlan').append('<div class="video-link" data-video-id="y-9Auq9mYxFEE"><img id ="ReceptionImage" src="' + wpApiSettings.floorplan + jQuery('#clubcloud_shortcode').attr('cc_receptionimage') + '" class="img-responsive"></img></div>')
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
                        cc_queue_name = this.$content.find('.name').val();
                        if (!cc_queue_name) {
                            $.alert('provide a valid name');
                            return false;
                        }
                        $.alert({
                            title: 'Hi there ' + cc_queue_name,
                            content: 'You are in the queue please enjoy some tv while you wait just click the screen',
                        });
                        if (norminterval == null) {
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
        jQuery('#TablePlan').append('<img id ="FloorPlanImage" src="' + floorplanfile + '" class="img-responsive"></img>')
        if (yt_url != null) {
            jQuery('#KeynoteVideo').append('<iframe src="' + yt_url + '" frameborder="0" allowfullscreen ></iframe>')
        }
        jQuery.getJSON(tabledatamap, function (results) {
            jQuery.each(results, function (i, table) {
                jQuery('#TablePlan').append('<div id="' + event_prefix_lower + table.TableName.toLowerCase() + '" class="launchtable"></div>')
                currtable = '#' + event_prefix_lower + table.TableName.toLowerCase()
                cc_table_array.push(currtable.replace('#', ''))
                jQuery.each(table.Seating, function (b, seats) {
                    jQuery(currtable).append('<div id="' + event_prefix_lower + table.TableName.toLowerCase() + seats.Seat.toLowerCase() + '" class="craigj-hot-spot" Style="top:' + seats.ypc + ';left:' + seats.xpc + '"><div class="circle"></div><div id="' + event_prefix_lower + table.TableName.toLowerCase() + seats.Seat.toLowerCase() + 'tip" class="tooltip">' + seats.Seat + '</div>')

                    dropseat = '#' + event_prefix_lower + table.TableName.toLowerCase() + seats.Seat.toLowerCase()
                    jQuery(dropseat).droppable({
                        drop: personmoved
                    });
                })
            })
        });
        ;
        if (norminterval == null) {
            setInterval(cc_queue, 7500)
        }
    }

}

function cc_queue_display() {
    $.confirm({
        title: 'Queue Status',
        content: 'You are number ' + cc_queue_pos + ' in the queue',
        autoClose: 'Close|5000',
        buttons: {
            Close: function () {
            }
        },
    })
}

function cc_queue() {
    if (cc_is_reception == 'true') {

        //alert(cc_queue_name)
        jQuery.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_call_queue',
                event_id: event_prefix_lower,
                cc_guest_name: cc_queue_name,
                qid: cc_queue_id,
                envelope: wpApiSettings.envelope,
            },
            success: function (response) {
                switch (response.action) {
                    case 'insert':
                        cc_queue_id = response.dbid
                        qdisplayinetvalid = setInterval(cc_queue_display, 45000)
                        break;
                    case 'update':
                        cc_queue_pos = response.qpos + 1
                        break;
                    case 'move':
                        cc_is_reception = 'false'
                        jQuery('#TablePlan').empty()
                        clearInterval(qdisplayinetvalid);
                        buildpage()
                        connectmeeting(response.move, response.move, response.temptoken)
                        break;

                }

            }
        })
    } else {

        jQuery.ajax({
            url: wpApiSettings.ajax_url,
            type: 'post',
            data: {
                action: 'cc_get_connectiondata',
                table_list: cc_table_array,
                connection_id: connection_id_var,
                envelope: wpApiSettings.envelope,
            },
            success: function (response) {
                if (response.move) {

                    connectmeeting(response.move, response.move, response.temptoken)


                }
                //alert("After Return -" + JSON.stringify(response))
                jQuery('.craigj-hot-spot').each(function (h, tooltipid) {
                    jQuery(tooltipid).css('background-image', 'none')
                });

                jQuery.each(response, (function (h, tableentry) {
                    // if(tableentry.currentuser != 1 ){alert(tableentry.currentuser)}
                    currtable = tableentry.RoomName
                    jQuery.each(tableentry.Participants, (function (j, seatentry) {
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
                        jQuery(currentseat).css('background-image', 'url("' + partyavatarurl + '")')
                        jQuery(currentseat).attr('jid', seatentry.JiD)
                        if (wpApiSettings.dragenabled) {
                            jQuery(currentseat).draggable({
                                containment: '#TablePlan',
                                cursor: 'move',
                                helper: 'clone',
                            });

                        }
                        jQuery(currentseattip).html('<li class="craigs-list"><img src="' + partyavatarurl + '" class="craigs-avatar"/><p>' + currentdisplayname + '</p></li>')
                        if (cartconnect == 'yes' && wpApiSettings.dragenabled && connection_id_var != seatentry.JiD && currtable == connection_id_var.split('@')[1]) {
                            jQuery(currentseattip).append('<p>Request Control of this users cart</p><button id="' + seatentry.JiD + '" class="accessreq" type="button">Access Cart</button>')

                        }
                    }))
                }))

            }
        })
        if (cc_has_reception_sc == true) {
            get_q_data()
        }
    }


}

function cc_route_connected(jid, room_id) {
    jQuery.ajax({
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
        cc_move_room = jQuery(cc_move_to_child).parent().attr('id')
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

                        jQuery.ajax({
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
                                jQuery(cc_draggableid).remove()
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
        cc_move_room = jQuery(cc_move_to_child).parent().attr('id')

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

                        jQuery.ajax({
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

function request_cart_access(user) {
    jid = user.split("@");
    messagedata = {}
    messagedata['userJID'] = connection_id_var
    messagedata['username'] = cc_username
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
                            cartsync = true

                            messagedata = {}
                            messagedata['userJID'] = connection_id_var
                            messagedata['username'] = cc_username
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
    jQuery.ajax({
        url: wpApiSettings.ajax_url,
        type: 'post',
        data: {
            action: 'cc_clearcart',
            data: 'clear',
        },
        success: function (response) {
            jQuery(document.body).trigger('wc_fragment_refresh');
        }

    });

}

function cart_updates_get() {
    jQuery.ajax({
        url: wpApiSettings.ajax_url,
        type: 'post',
        data: {
            action: 'cc_getcart',
            data: 'data',
        },
        success: function (response) {
            messagedata = {}
            messagedata['userJID'] = connection_id_var
            messagedata['username'] = cc_username
            messagedata['message'] = 'CartUpdateSend'
            messagedata['data'] = response
            api.executeCommand('sendEndpointTextMessage', sendinguserjid, messagedata);
            jQuery(document.body).trigger('wc_fragment_refresh');


            //alert(bla)
        }

    });


}

function cart_updates_set(cartdata) {
    //alert(cartdata)
    jQuery.ajax({
        url: wpApiSettings.ajax_url,
        type: 'post',
        data: {
            action: 'cc_setcart',
            data: cartdata,
        },
        success: function (response) {

            jQuery(document.body).trigger('wc_fragment_refresh');

            //alert(bla)
        }

    });


}

function get_q_data() {
    jQuery.ajax({
        url: wpApiSettings.ajax_url,
        type: 'post',
        data: {
            action: 'cc_get_call_queue',
            callkey: event_prefix_lower,
        },
        success: function (response) {
            jQuery.each(response, function (k, queueduser) {
                userqid = '#userid-' + queueduser.guestqid
                if ($(userqid).length) {
                } else {
                    jQuery('#club_reception_shortcode').append('<div id=userid-' + queueduser.guestqid + ' style="z-index:10"><p><img src="' + wpApiSettings.floorplan + 'guest.jpg" style="border-radius: 50%;width:30px" />' + queueduser.guestname + '</p></div>');
                    jQuery(userqid).draggable({
                        containment: 'document',
                        cursor: 'move',
                        helper: 'clone',
                    });
                }
            });

        }

    })
}
