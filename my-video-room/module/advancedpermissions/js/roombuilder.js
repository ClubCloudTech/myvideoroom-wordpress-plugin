/**
 * Enhance the room builder to hide and show sections, and add copy actions
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

jQuery.noConflict()(
	function () {
		var $         = jQuery.noConflict();
		var $settings = $( '.myvideoroom-room-builder-settings' );

		$settings.each(
			function () {
				var $advanced_permissions        = $( '.advanced-permissions', this );
				var $advanced_permissions_option = $( 'input[name=myvideoroom_room_builder_room_permissions_preference]', this );

				if ($advanced_permissions_option.filter( ":checked" ).val() !== 'use_advanced_permissions') {
					$advanced_permissions.hide();
				}

				$advanced_permissions_option.on(
					'change',
					function () {
						if ($( this ).val() === 'use_advanced_permissions' ) {
							$advanced_permissions.show();
						} else {
							$advanced_permissions.hide();
						}

					}
				)
			}
		)
	}
);
