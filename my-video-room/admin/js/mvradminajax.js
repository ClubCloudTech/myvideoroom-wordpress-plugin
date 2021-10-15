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
					$( '.mvr-admin-ajax' ).click(
						function(e){
							e.stopPropagation();
							e.preventDefault();
							let action = $( this ).attr( 'data-action' ),
							module     = $( this ).attr( 'data-module' );
							moduleAction( action, module );
						}
					);
					$( '#user-profile-input' ).on( 'keyup', checkform );
					$( '#group-profile-input' ).on( 'keyup', checkgroupform );
										
					$( '#save-user-tab' ).on( 'click', updateUsertab );
					$( '#save-group-tab' ).on( 'click', updateGrouptab );

					
				}

				/**
				 * Update Account Limits on Database by Subscription Level (used in backend admin page)
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
				 * Update Account Limits on Database by Subscription Level (used in backend admin page)
				 */
				 var updateUsertab = function (){
					var	form_data = new FormData();
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
								$( '#save-user-tab' ).prop('value', state_response.feedback );

							},
							error: function ( response ){
								console.log( 'Error Uploading' );
							}
						}
					);
				}

				/**
				 * Update Account Limits on Database by Subscription Level (used in backend admin page)
				 */
					var updateGrouptab = function (){
					var	form_data = new FormData();
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
								$( '#save-group-tab' ).prop('value', state_response.feedback );

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
					var input_check = $( '#group-profile-input').val().length;
					$( '#save-group-tab' ).prop('value', 'Save');
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
				 * Check if Length Conditions Met for Submit Groups
				 */
				 function checkgroupform(){
					var input_check = $( '#group-profile-input').val().length;
					$( '#save-group-tab' ).prop('value', 'Save');
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
				init();
			}
		);
	}
);
