/**
 * Handle Ajax requests on Site Video Frontend
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
					$( '.myvideoroom-sitevideo-settings' ).click(
						function(e) {
							e.stopPropagation();
							e.stopImmediatePropagation();
							e.preventDefault();
							var room_id    = $( this ).data( 'roomId' ),
								input_type = $( this ).data( 'inputType' );
							moduleCore( room_id, input_type );
						}
					);
					$( '.myvideoroom-sitevideo-delete' ).click(
						function(e) {
							e.stopPropagation();
							e.stopImmediatePropagation();
							e.preventDefault();
							let room      = parseInt( $( this ).attr( 'data-room-id' ) ),
								nonce     = $( this ).attr( 'data-nonce' ),
								room_name = $( this ).attr( 'data-room-name' );

							deleteRoom( room, nonce, room_name );
						}
					);
					// For Cancel Buttons.
					$( '.mvr-confirmation-cancel' ).click(
						function(e) {
							e.stopPropagation();
							e.stopImmediatePropagation();
							e.preventDefault();
							$( '.mvr-security-room-host' ).empty();
							$( '.myvideoroom-sitevideo-hide-button' ).hide();
						}
					);

					// For Confirmation Buttons.
					$( '.mvr-confirmation-button' ).click(
						function(e) {
							e.stopPropagation();
							e.stopImmediatePropagation();
							e.preventDefault();
							let room       = parseInt( $( this ).attr( 'data-room-id' ) ),
								nonce      = $( this ).attr( 'data-nonce' ),
								input_type = $( this ).attr( 'data-input-type' );
							// Button type handlers
							if (input_type === 'delete-approved') {
								deleteRoom( room, nonce, input_type );
							}
						}
					);

					// For New Room Adds.
					$( ".myvideoroom-input-new-trigger" ).keyup(
						function() {
							let core_url = 'https://' + document.domain + '/';
							$( '#update_url_newroom' ).html( core_url + this.value.toLowerCase() );
							let target = '#button_add_new';
							checkShow( target, this.value.toLowerCase() );
						}
					);
					// For Add New Room.
					$( '#button_add_new' ).click(
						function(e) {
							e.stopPropagation();
							e.stopImmediatePropagation();
							e.preventDefault();
							addRoom();
						}
					);
				}

				/**
				 * Check if Room Length is sufficient, and then checks availability of room name.
				 */
				function checkShow(targetid, input) {
					var length = input.length;
					if (length < 3) {
						$( targetid ).prop( 'value', 'Too Short' );
						$( targetid ).prop( 'disabled', true );
					} else {
						$( targetid ).prop( 'value', 'Checking Availability' );
						chkSlug( targetid, input );
					}
					return false;

				}

				/**
				 * Check Slug Exists or Is available (used for changing page slug for rooms)
				 */
				var chkSlug = function(targetid, input) {
					var form_data = new FormData();
					form_data.append( 'action', 'myvideoroom_sitevideo_settings' );
					form_data.append( 'action_taken', 'check_slug' );
					form_data.append( 'slug', input );
					form_data.append( 'security', myvideoroom_sitevideo_settings.security );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_sitevideo_settings.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function(response) {
								var state_response = JSON.parse( response );

								if (state_response.available === true) {
									$( targetid ).prop( 'disabled', false );
									$( targetid ).prop( 'value', 'Save' )
								} else {
									$( targetid ).prop( 'disabled', true );
									$( targetid ).prop( 'value', 'Taken' )
								}
							},
							error: function(response) {
								console.log( 'Error Uploading' );
							}
						}
					);
				}

				/**
				 * Add New Room - used to add a new room from Site Conference Pages
				 */
				var addRoom = function() {

					var container    = $( '.mvr-security-room-host' );
					var loading_text = '<h1 style="padding:20px">Room Update in Progress....</h1>';
					container.html( '<h1 style = "padding:20px" > ' + loading_text + '</h1>' );
					var form_data     = new FormData();
					var input         = $( '#room-url-link' ).val().toLowerCase(),
						display_title = $( '#room-display-name' ).val(),
						table         = $( '#mvr-table-basket-frame_site-conference-room' ),
						shortcode     = table.attr( 'data-type' );

					form_data.append( 'action_taken', 'add_new_room_shortcode' );

					if (display_title.length < 3 || input.length < 3) {
						console.log( 'too short' );
						return false;
					}

					form_data.append( 'action', 'myvideoroom_sitevideo_settings' );

					form_data.append( 'display_title', display_title );
					form_data.append( 'slug', input );
					form_data.append( 'type', shortcode );
					form_data.append( 'security', myvideoroom_sitevideo_settings.security );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_sitevideo_settings.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function(response) {
								var state_response = JSON.parse( response ),
								main               = $( '#mvr-table-basket-frame_main' ),
								sctable            = $( '#mvr-table-basket-frame_site-conference-room' );

								console.log( state_response.shortcode + ' ok fired' );
								$( '#button_add_new' ).prop( 'value', state_response.feedback );
								$( '#button_add_new' ).prop( 'disabled', true );
								$( '#room-display-name' ).val( "" );
								$( '#room-url-link' ).val( "" );
								$( '.myvideoroom-sitevideo-hide-button' ).hide();
								container.empty();
								sctable.empty();
								sctable.html( state_response.maintable );

								window.myvideoroom_monitor_load();
								init();
							},
							error: function(response) {
								console.log( 'Error Uploading' );
							}
						}
					);
				}

				/**
				 * Handles Core Room View, Settings Click, and Default Room Appearance.
				 */
				var moduleCore = function(room_id, input_type) {

						var container    = $( '.mvr-security-room-host' );
						var loading_text = container.data( 'loadingText' );
						$( '.myvideoroom-sitevideo-hide-button' ).show();
					if (input_type === 'close') {
						container.empty();
						$( '#mvr-close_' + room_id ).hide();
						return false;
					}
						container.html( '<h1 style = "padding:20px" > ' + loading_text + '</h1>' );
						var form_data = new FormData();
						form_data.append( 'action', 'myvideoroom_sitevideo_settings' );
						form_data.append( 'action_taken', 'core' );
						form_data.append( 'roomId', room_id );
						form_data.append( 'inputType', input_type );
						form_data.append( 'security', myvideoroom_sitevideo_settings.security );

						$.ajax(
							{
								type: 'post',
								dataType: 'html',
								url: myvideoroom_sitevideo_settings.ajax_url,
								contentType: false,
								processData: false,
								data: form_data,
								success: function(response) {
									var state_response = JSON.parse( response );

									window.myvideoroom_tabbed_init;
									container.html( state_response.mainvideo );

									if (window.myvideoroom_tabbed_init) {
										window.myvideoroom_tabbed_init( container );
									}

									if (window.myvideoroom_app_init) {
										window.myvideoroom_app_init( container[0] );
									}

									if (window.myvideoroom_app_load) {
										window.myvideoroom_app_load();
									}

									if (window.myvideoroom_shoppingbasket_init) {
										window.myvideoroom_shoppingbasket_init();
									}

								}
							}
						);

				}
					/**
					 * Handles Deletion of Room
					 */
				var deleteRoom = function(room_id, nonce, room_name) {

					var container    = $( '.mvr-security-room-host' );
					var loading_text = container.data( 'loadingText' );
					$( '.myvideoroom-sitevideo-hide-button' ).show();

					var form_data = new FormData();
					form_data.append( 'action', 'myvideoroom_sitevideo_settings' );

					if (room_name === 'delete-approved') {
						form_data.append( 'action_taken', 'delete_approved' );
						container.html( '<h1 style = "padding:20px" > Delete in Progress.... </h1>' );
					} else {
						container.html( '<h1 style = "padding:20px" > ' + loading_text + '</h1>' );
						form_data.append( 'action_taken', 'delete_room' );

					}

					form_data.append( 'roomId', room_id );
					form_data.append( 'roomName', room_name );
					form_data.append( 'nonce', nonce );
					form_data.append( 'security', myvideoroom_sitevideo_settings.security );

					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_sitevideo_settings.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function(response) {
								var state_response = JSON.parse( response );
								if (room_name !== 'delete-approved') {
									window.myvideoroom_tabbed_init;
									container.html( state_response.mainvideo );

									if (window.myvideoroom_tabbed_init) {
										window.myvideoroom_tabbed_init( container );
									}

									if (window.myvideoroom_app_init) {
										window.myvideoroom_app_init( container[0] );
									}

									if (window.myvideoroom_app_load) {
										window.myvideoroom_app_load();
									}

									if (window.myvideoroom_shoppingbasket_init) {
										window.myvideoroom_shoppingbasket_init();
									}
								} else {
									sctable = $( '#mvr-table-basket-frame_site-conference-room' );
									$( '.mvr-security-room-host' ).empty();
									sctable.empty();
									sctable.html( state_response.mainvideo );
									$( '.myvideoroom-sitevideo-hide-button' ).hide();
									window.myvideoroom_monitor_load();
								}

								init();
							}
						}
					);

				}
				init();
			}
		);
	}
);
