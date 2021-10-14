/**
 * Ajax control for admin pages.
 *
 * @package ElementalPlugin\Admin\js\AdminAjax.js
 */
window.addEventListener(
	"load",
	function(){
		jQuery(
			function($) {

				/**
				 * Initialise Functions on Load
				 */
				function init(){
												
					$( '.mvr-admin-ajax' ).click(
						function(e){
							e.stopPropagation();
							e.preventDefault();
							let action = $( this ).attr( 'data-action' ),
							module     = $( this ).attr( 'data-module' );
							moduleAction(action, module);
						}
					);
			
				}

				/**
				 * Update Account Limits on Database by Subscription Level (used in backend admin page)
				 */
				var moduleAction = function ( action, module ){
					if ( ! action || ! module ) {
						return false;
					}
					var	form_data = new FormData(),
					frame = $( '#module'+module );
					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'update_module' );
					form_data.append( 'state', action );
					form_data.append( 'module', module );
					form_data.append( 'security', myvideoroom_admin_ajax.security );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_admin_ajax.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								var state_response = JSON.parse( response );
								if ( state_response.button ){
									frame_parent = frame.parent().attr( 'id' );
									parent_element   = $( '#' + frame_parent );
									frame.remove();
									frame.parent().empty();
									parent_element.parent().html( state_response.button );
									init();
								}
								

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				}

				/**
				 * Check email exists (used in main add new user form)
				 */
				var chkEmail = function (event){
					event.stopPropagation();

					$( '#elemental-email-status' ).removeClass( 'elemental-checking' );
					$( '#elemental-email-status' ).addClass( 'elemental-invalid' );
					var email   = event.target.value,
					valid_email = validateEmail( email );

					if ( ! valid_email ) {
						$( '#elemental-email-status' ).removeClass( 'elemental-checking' );
						$( '#elemental-email-status' ).removeClass( 'elemental-email-available' );
						$( '#elemental-email-status' ).removeClass( 'elemental-email-taken' );
						$( '#elemental-email-status' ).html( 'Invalid Address' );
						$( '#elemental-email-status' ).addClass( 'elemental-invalid' );
						return false;
					} else {
						$( '#elemental-email-status' ).removeClass( 'elemental-invalid' );
						$( '#elemental-email-status' ).addClass( 'elemental-checking' );
						$( '#elemental-email-status' ).html( 'Checking is Free' );
					}
					var form_data = new FormData();
					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'check_email' );
					form_data.append( 'email', email );
					form_data.append( 'security', myvideoroom_admin_ajax.security );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_admin_ajax.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								var state_response = JSON.parse( response );
								console.log( state_response.available );
								if (state_response.available === false ) {
									$( '#elemental-email-status' ).removeClass( 'elemental-checking' );
									$( '#elemental-email-status' ).removeClass( 'elemental-invalid' );
									$( '#elemental-email-status' ).addClass( 'elemental-email-taken' );
									$( '#elemental-email-status' ).html( 'Email Taken' );
								} else {
									$( '#elemental-email-status' ).removeClass( 'elemental-checking' );
									$( '#elemental-email-status' ).removeClass( 'elemental-invalid' );
									$( '#elemental-email-status' ).addClass( 'elemental-email-available' );
									$( '#elemental-email-status' ).html( 'Email Available' );
									$( '#elemental-email-status' ).attr( 'data-status','checked' );
									checkShow();
								}

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
							}
					);
				}

				/**
				 * Create New User post checks (used in main add new user form)
				 */
				var createUser = function (event){
					event.stopPropagation();
					$( '#elemental-email-status' ).removeClass( 'elemental-checking' );
					$( '#elemental-email-status' ).removeClass( 'elemental-invalid' );
					$( '#elemental-email-status' ).addClass( 'elemental-email-available' );
					$( '#elemental-email-status' ).html( 'Creating Account' );

					var email      = $( '#elemental-inbound-email' ).val(),
					first_name     = $( '#first_name' ).val(),
					last_name      = $( '#last_name' ).val(),
					account_window = $( '#elemental-membership-table' ),
					counter_window = $( '#elemental-remaining-counter' ),
					form_data      = new FormData();

					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'create_user' );
					form_data.append( 'email', email );
					form_data.append( 'last_name', last_name );
					form_data.append( 'first_name', first_name );
					form_data.append( 'security', myvideoroom_admin_ajax.security );

					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_admin_ajax.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								var state_response = JSON.parse( response );
								console.log( state_response.feedback );
								if ( state_response.feedback == true ) {
									if ( state_response.table ) {
										account_window.html( state_response.table );
									}
									if ( state_response.counter ) {
										mainvideo_parent = counter_window.parent().attr( 'id' );
										parent_element   = $( '#' + mainvideo_parent );
										counter_window.remove();
										counter_window.parent().empty();
										parent_element.html( state_response.counter );
									}

									$( '#elemental-email-status' ).removeClass( 'elemental-checking' );
									$( '#elemental-email-status' ).removeClass( 'elemental-invalid' );
									$( '#elemental-email-status' ).removeClass( 'elemental-email-taken' );
									$( '#elemental-email-status' ).addClass( 'elemental-email-available' );
									$( '#elemental-email-status' ).html( 'Account Created' );
									$( '#submit' ).prop( 'value', 'Account Created' );
									$( '#submit' ).prop( 'disabled', true );
									$( '#first_name' ).prop( 'value', '' );
									$( '#last_name' ).prop( 'value', '' );
									$( '#elemental-inbound-email' ).prop( 'value', '' );
									$( '#elemental-email-status' ).attr( 'data-status','' );
									$( '#first-name-icon' ).hide();
									$( '#last-name-icon' ).hide();
								}
							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
							}
					);
				}

				/**
				 * Delete User (used in main form)
				 */
				var deleteUser = function (event, user_id, nonce, final ){
					event.stopPropagation();
					counter_window = $( '#elemental-remaining-counter' );
					var form_data  = new FormData(),
					notification   = $( '#elemental-notification-frame' ),
					account_window = $( '#elemental-membership-table' ),
					counter_window = $( '#elemental-remaining-counter' );

					form_data.append( 'action','myvideoroom_admin_ajax' );
					if ( final ) {
						form_data.append( 'action_taken', 'delete_final' );
					} else {
						form_data.append( 'action_taken', 'delete_user' );
					}
					form_data.append( 'userid', user_id );
					form_data.append( 'nonce', nonce );
					form_data.append( 'security', myvideoroom_admin_ajax.security );
					$.ajax(
						{
							type: 'post',
							dataType: 'html',
							url: myvideoroom_admin_ajax.ajax_url,
							contentType: false,
							processData: false,
							data: form_data,
							success: function (response) {
								var state_response = JSON.parse( response );

								if ( state_response.confirmation ) {
									notification.html( state_response.confirmation );
									$( '#elemental-membership-table' ).hide();
									init();
								}
								if ( state_response.feedback ) {
									console.log( state_response.feedback );
								}

								if ( state_response.table ) {
									account_window.html( state_response.table );
								}
								if ( state_response.counter ) {
									mainvideo_parent = counter_window.parent().attr( 'id' );
									parent_element   = $( '#' + mainvideo_parent );
									counter_window.remove();
									counter_window.parent().empty();
									parent_element.html( state_response.counter );
									$( '#mvr-main-button-cancel' ).click();
									init();
								}

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				}

				/**
				 * Check if Name and Email conditions are met in main form
				 */
				function checkShow( status ){
					var first_name = $( '#first_name' ).val().length,
					last_name      = $( '#last_name' ).val().length,
					status         = $( '#elemental-email-status' ).data( 'status' );

					if (first_name >= 3) {
						$( '#first-name-icon' ).show();
					} else {
						$( '#first-name-icon' ).hide();
					}
					if (last_name >= 3) {
						$( '#last-name-icon' ).show();
					} else {
						$( '#last-name-icon' ).hide();
					}

					if ( status === 'checked' && first_name >= 3 && last_name >= 3 ) {
						$( '#submit' ).show();
						$( '#submit' ).prop( 'disabled', false );
					} else {
						return false;
					}
				}

				/**
				 * Validate email format JS (pre check)
				 */
				function validateEmail(email) {
					var re = /\S+@\S+\.\S+/;
					return re.test( email );
				}
				init();
			}
		);
	}
);
