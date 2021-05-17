/**
 * Process the invite link
 *
 * @package MyVideoRoomPlugin\Module\PersonalMeetingRooms
 */

/*global myvideroom_personalmeetingrooms_invite*/

jQuery.noConflict()(
	function () {
		var $ = jQuery.noConflict();

		var $link_container = $( '.myvideoroom-personalmeetingrooms-invite .link' );
		var link_url        = $link_container.html().trim();

		$( '<input type="button" value="' + $link_container.data( 'copyText' ) + '" />' )
			.appendTo( $link_container ).on(
				'click',
				function () {
					var $copy_button = $( this );
					navigator.clipboard.writeText( link_url ).then(
						function () {
							$copy_button.val( $link_container.data( 'copiedText' ) )
								.addClass( 'success' );

							setTimeout(
								function () {
									$copy_button.val( $link_container.data( 'copyText' ) )
										.removeClass( 'success' );
								},
								1500
							);
						}
					);
				}
			);

		var $invite_form = $( '.myvideoroom-personalmeetingrooms-invite form' );
		var ajax_url     = myvideroom_personalmeetingrooms_invite.ajax_url;

		$invite_form.on(
			'submit',
			function (e) {
				var $form = $( this );

				var $submit = $( 'input[type=submit]', $form );

				if ($submit.is( ':disabled' )) {
					return;
				}

				$submit.prop( 'disabled', true );

				var nonce = $( 'input[name=myvideoroom_nonce]', $form ).val();
				var email = $( 'input[name=myvideoroom_personalmeetingrooms_invite_address]', $form ).val();
				var link  = $( 'input[name=myvideoroom_personalmeetingrooms_invite_link]', $form ).val();

				var $status = $form.siblings( 'span.status' );
				if ( ! $status.length) {
					$status = $( '<span class="status"></span>' ).insertAfter( $form );
				}

				$status.removeClass( ['error', 'success'] ).html( $form.data( 'sendingText' ) );

				$.ajax(
					{
						type: 'post',
						dataType: 'json',
						url: ajax_url,
						data: {
							action: 'myvideroom_personalmeetingrooms_invite',
							nonce: nonce,
							email: email,
							link: link
						},
						success: function (response, data) {
							$status.removeClass( ['error', 'success'] )
								.addClass( 'success' )
								.html( response.message );

							$submit.prop( 'disabled', false );
						},
						error: function (response) {
							$status.removeClass( ['error', 'success'] )
								.addClass( 'error' )
								.html( response.responseJSON.message );

							$submit.prop( 'disabled', false );
						}
					}
				);
				e.preventDefault();
				return false;
			}
		);
	}
);
