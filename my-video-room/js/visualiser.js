/**
 * Add dynamic tabs to MyVideoRoom admin pages
 *
 * @package MyVideoRoomPlugin
 */

jQuery.noConflict()(
	function () {
		var $            = jQuery.noConflict();
		var $visualisers = $( '.myvideoroom-visualiser-settings' );

		$visualisers.each(
			function () {
				var $floorplan_checkbox    = $( 'input[name=myvideoroom_visualiser_disable_floorplan_preference]', this );
				var $reception_checkbox    = $( 'input[name=myvideoroom_visualiser_reception_enabled_preference]', this );
				var $custom_video_checkbox = $( 'input[name=myvideoroom_visualiser_reception_custom_video_preference]', this );

				var $reception_settings    = $( 'div.reception-settings', this );
				var $custom_video_settings = $( 'div.custom-video-settings', this );

				if ( ! $reception_checkbox.is( ":checked" ) ) {
					$reception_settings.hide();
				}

				if ( ! $custom_video_checkbox.is( ":checked" ) ) {
					$custom_video_settings.hide();
				}

				$floorplan_checkbox.on(
					'change',
					function () {
						if (
							$( this ).is( ":checked" ) &&
							! $reception_checkbox.is( ':checked' )
						) {
							$reception_checkbox.trigger( 'click' );
						}
					}
				)

				$reception_checkbox.on(
					'change',
					function () {
						if ($( this ).is( ":checked" ) ) {
							$reception_settings.show();
						} else {
							$reception_settings.hide();
						}
					}
				)

				$custom_video_checkbox.on(
					'change',
					function () {
						if ($( this ).is( ":checked" ) ) {
							$custom_video_settings.show();
						} else {
							$custom_video_settings.hide();
						}
					}
				)
			}
		)
	}
);
