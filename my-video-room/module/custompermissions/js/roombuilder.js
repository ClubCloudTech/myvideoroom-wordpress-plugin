/**
 * Enhance the room builder to hide and show sections, and add copy actions
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

(function ( $ ) {
	var $settings = $( '.myvideoroom-room-builder-settings' );

	$settings.each(
		function () {
			var $custom_permissions        = $( '.custom-permissions', this );
			var $custom_permissions_option = $( 'input[name=myvideoroom_room_builder_room_permissions_preference]', this );

			if ($custom_permissions_option.filter( ':checked' ).val() !== 'use_custom_permissions') {
				$custom_permissions.hide();
			}

			$custom_permissions_option.on(
				'change',
				function () {
					if ($( this ).val() === 'use_custom_permissions') {
						$custom_permissions.show();
					} else {
						$custom_permissions.hide();
					}

				}
			);
		}
	);
})( jQuery );
