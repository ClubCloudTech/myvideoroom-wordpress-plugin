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
							moduleAction( action, module );
						}
					);
					$( '.myvideoroom-edit-page-trigger' ).click(
						function(e){
							e.stopPropagation();
							e.preventDefault();
							let id = $( this ).attr( 'data-id' ),
							offset = $( this ).attr( 'data-offset' );
							$( '#urlinput_' + id + '-' + offset ).toggle();
							$( '#button_' + id + '-' + offset ).toggle();
						}
					);
					// For Room Entitity Room Edits
					$( ".myvideoroom-input-url-trigger" ).keyup(
						function() {
							let id   = $( this ).attr( 'data-id' ),
							offset   = $( this ).attr( 'data-offset' ),
							core_url = 'https://' + document.domain + '/';
							$( '#urlchange_' + id + '-' + offset ).html( core_url + this.value.toLowerCase() );
							let targetid = '#button_' + id + '-' + offset;
							checkShow( targetid, this.value.toLowerCase() );
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

					$( '.myvideoroom-roomname-submit-form' ).click(
						function(e){
							e.stopPropagation();
							e.preventDefault();
							let id = $( this ).attr( 'data-id' ),
							offset = $( this ).attr( 'data-offset' );
							updateSlug( id, offset );
						}
					);

					$( '#button_add_new' ).click(
						function(e){
							e.stopPropagation();
							e.preventDefault();
							addRoom();
						}
					);

					$( '#user-profile-input' ).on( 'keyup', checkform );
					$( '#group-profile-input' ).on( 'keyup', checkgroupform );

					$( '#save-user-tab' ).on( 'click', updateUsertab );
					$( '#save-group-tab' ).on( 'click', updateGrouptab );

				}

				/**
				 * Room Manager Ajax Functions
				 * Used by Room Manager Ajax pages to update room URL(slugs)
				 */

				/**
				 * Handles Module Activation and De-activation Button
				 */
				var moduleAction = function ( action, module ){
					if ( ! action || ! module ) {
						return false;
					}
					var	form_data = new FormData(),
					frame         = $( '#module' + module );
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
								if ( state_response.button ) {
									frame_parent   = frame.parent().attr( 'id' );
									parent_element = $( '#' + frame_parent );
									frame.remove();
									frame.parent().empty();
									parent_element.parent().html( state_response.button );
									refreshTables();
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
				 * Check if Room Length is sufficient, and then checks availability of room name.
				 */
				function checkShow( targetid, input ){
					var length = input.length;
					console.log( targetid );
					if ( length < 3) {
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
				 var chkSlug = function (targetid, input) {
					var form_data = new FormData();
					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'check_slug' );
					form_data.append( 'slug', input );
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

								if (state_response.available === true ) {
									$( targetid ).prop( 'disabled', false );
									$( targetid ).prop( 'value', 'Save' )
								} else {
									$( targetid ).prop( 'disabled', true );
									$( targetid ).prop( 'value', 'Taken' )
								}
							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				 }
				/**
				 * Update Slug - used to update page slug from Room Manager Pages
				 */
				var updateSlug = function (id, offset) {
					var form_data = new FormData();
					var input     = $( '#urlinput_' + id + '-' + offset ).val(),
					maintable     = $( '#mvr-table-basket-frame_main' );
					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'update_slug' );
					form_data.append( 'post_id', id );
					form_data.append( 'slug', input );
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
								$( '#button_' + id + '-' + offset ).prop( 'value', state_response.feedback );
								$( '#button_' + id + '-' + offset ).prop( 'disabled', true );
								maintable.empty();
								if (maintable) {
									maintable.html( state_response.maintable );
								}
								if ( state_response.personalmeeting ) {
									let pmm = $( '#mvr-table-basket-frame_personal-meeting-module' );
									pmm.html( state_response.personalmeeting );
								}
								if ( state_response.conference ) {
									let conf = $( '#mvr-table-basket-frame_site-conference-room' );
									conf.html( state_response.conference );
								}

								init();
								reloadJs( 'myvideoroom-monitor-js' );

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				}

				/**
				 * Add New Room - used to add a new room from Site Conference Pages
				 */
				 var addRoom = function () {
					var form_data = new FormData();
					var input     = $( '#room-url-link' ).val().toLowerCase(),
					display_title = $( '#room-display-name' ).val(),
					maintable     = $( '#mvr-table-basket-frame_main' );

					if ( display_title.length < 3 || input.length < 3 ) {
						console.log( 'too short' );
						return false;
					} else {
						console.log( 'continue' );
					}

					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'add_new_room' );
					form_data.append( 'display_title', display_title );
					form_data.append( 'slug', input );
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
								$( '.myvideoroom-roomname-submit-form' ).prop( 'value', state_response.feedback );
								$( '.myvideoroom-roomname-submit-form' ).prop( 'disabled', true );
								maintable.empty();
								if (maintable) {
									maintable.html( state_response.maintable );
								}
								if ( state_response.personalmeeting ) {
									let pmm = $( '#mvr-table-basket-frame_personal-meeting-module' );
									pmm.html( state_response.personalmeeting );
								}
								if ( state_response.conference ) {
									let conf = $( '#mvr-table-basket-frame_site-conference-room' );
									conf.html( state_response.conference );
								}

								init();
								reloadJs( 'myvideoroom-monitor-js' );

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				 }

				/**
				 * Refresh Tables - used to refresh room page tables (used post module activation)
				 */
				var refreshTables = function (id, offset) {
					var form_data = new FormData(),
					maintable     = $( '#mvr-table-basket-frame_main' );
					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'refresh_tables' );
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
								maintable.empty();
								if (maintable) {
									maintable.html( state_response.maintable );
								}
								if ( state_response.personalmeeting ) {
									let pmm = $( '#mvr-table-basket-frame_personal-meeting-module' );
									pmm.html( state_response.personalmeeting );
								}
								if ( state_response.conference ) {
									let conf = $( '#mvr-table-basket-frame_site-conference-room' );
									conf.html( state_response.conference );
								}

								init();
								reloadJs( 'myvideoroom-monitor-js' );

							},
							error: function ( response ){
								console.log( 'Error Refreshing' );
							}
						}
					);
				}
				/**
				 * Reload a Script by ID and re-initialise.
				 */
				function reloadJs(id) {
					src = $( '#' + id ).attr( 'src' );
					src = $( 'script[src$="' + src + '"]' ).attr( "src" );
					$( 'script[src$="' + src + '"]' ).remove();
					$( '<script/>' ).attr( 'src', src ).appendTo( 'head' );
				}

				/**
				 * BuddyPress User and Group Ajax Tab Functions
				 * Used to update Group Tab Names, and User Video Tab Names from BuddyPress module.
				 */

				/**
				 * Update User Display Name Tab in BuddyPress
				 */
				 var updateUsertab = function (){
					var	form_data    = new FormData();
					tab_user_profile = $( '#user-profile-input' ).val();
					form_data.append( 'action','myvideoroom_admin_ajax' );
					form_data.append( 'action_taken', 'update_user_tab_name' );
					form_data.append( 'user_tab_name', tab_user_profile );
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
								$( '#save-user-tab' ).prop( 'value', state_response.feedback );

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				 }

				/**
				 * Update Group Display Tab in BuddyPress
				 */
					var updateGrouptab = function (){
						var	form_data    = new FormData();
						tab_user_profile = $( '#group-profile-input' ).val();
						form_data.append( 'action','myvideoroom_admin_ajax' );
						form_data.append( 'action_taken', 'update_group_tab_name' );
						form_data.append( 'group_tab_name', tab_user_profile );
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
									$( '#save-group-tab' ).prop( 'value', state_response.feedback );

								},
								error: function ( response ){
									console.log( 'Error Uploading' );
								}
							}
						);
					}
				/**
				 * Check if Length Conditions Met for Submit Users
				 */
				function checkform(){
					var input_check = $( '#user-profile-input' ).val().length;
					$( '#save-user-tab' ).prop( 'value', 'Save' );
					if (input_check >= 5) {
						$( '#save-user-tab' ).show();
						$( '#save-user-tab' ).prop( 'disabled', false );
					} else {
						$( '#save-user-tab' ).hide();
					}
					if ( input_check < 5 ) {
						return false;
					}
				}
				/**
				 * Check if Length Conditions Met for Submit Groups
				 */
				function checkgroupform(){
					var input_check = $( '#group-profile-input' ).val().length;
					$( '#save-group-tab' ).prop( 'value', 'Save' );
					if (input_check >= 5) {
						$( '#save-group-tab' ).show();
						$( '#save-group-tab' ).prop( 'disabled', false );
					} else {
						$( '#save-group-tab' ).hide();
					}
					if ( input_check < 5 ) {
						return false;
					}
				}
				/**
				 * Init
				 */

				init();
			}
		);
	}
);
