/**
 * Process the invite link
 *
 * @package MyVideoRoomPlugin\Module\PersonalMeetingRooms
 */

/*global myvideroom_personalmeetingrooms_invite*/

jQuery.noConflict()(
	function () {
		var $            = jQuery.noConflict();
		var $invite_form = $( '.myvideoroom-personalmeetingrooms-invite form' );
		var ajax_url     = myvideroom_personalmeetingrooms_invite.ajax_url;

		$invite_form.on(
			'submit',
			function (e) {
				var $form = $( this );

				var nonce = $( 'input[name=myvideoroom_nonce]', $form ).val();
				var email = $( 'input[name=myvideoroom_personalmeetingrooms_invite_address]', $form ).val();
				var link  = $( 'input[name=myvideoroom_personalmeetingrooms_invite_link]', $form ).val();

				$.ajax(
					{
						type : "post",
						dataType : "json",
						url : ajax_url,
						data : {
							action: 'myvideroom_personalmeetingrooms_invite',
							nonce: nonce,
							email: email,
							link: link
						},
						success: function( response, data) {
							$form.after( '<span class="status success">' + response.message + '</span>' );
						},
						error: function ( response ) {
							$form.after( '<span class="status error">' + response.responseJSON.message + '</span>' );
						}
					}
				);
				e.preventDefault();
				return false;
			}
		)
	}
);
