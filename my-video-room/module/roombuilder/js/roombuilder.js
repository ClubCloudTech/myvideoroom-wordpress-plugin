/**
 * Enhance the room builder to hide and show sections, and add copy actions
 *
 * @package MyVideoRoomPlugin\Module\RoomBuilder
 */

jQuery.noConflict()(
	function () {
		var $         = jQuery.noConflict();
		var $settings = $( '.myvideoroom-room-builder-settings' );
		var $outputs  = $( '.myvideoroom-room-builder-output' );

		$settings.each(
			function () {
				var $floorplan_checkbox    = $( 'input[name=myvideoroom_room_builder_disable_floorplan_preference]', this );
				var $reception_checkbox    = $( 'input[name=myvideoroom_room_builder_reception_enabled_preference]', this );
				var $custom_video_checkbox = $( 'input[name=myvideoroom_room_builder_reception_custom_video_preference]', this );
				var $reception_dropdown    = $( 'select[name=myvideoroom_room_builder_reception_id_preference]', this );

				var $reception_settings    = $( 'div.reception-settings', this );
				var $custom_video_settings = $( 'div.custom-video-settings', this );
				var $custom_video_url      = $( 'div.custom-video-url', this );

				var fields = $( 'label', this );

				fields.each(
					function ( ) {
						var input_id       = $( this ).attr( 'for' );
						var description_id = $( '#' + input_id ).attr( 'aria-describedby' );
						var $description   = $( '#' + description_id ).addClass( 'card' ).hide();

						if ( $description.length ) {
							$( '<span role="button" class="myvideoroom-show-help"><i class="card dashicons dashicons-editor-help"></i></span>' )
								.appendTo( this )
								.append( $description )
								.on(
									'mouseover',
									function () {
										$description.stop().fadeIn();

										if ($description.offset().left + $description.width() > $( window ).width()) {
											$description.css( 'right', '16px' );
										}
									}
								)
								.on(
									'mouseout',
									function () {
										$description.stop().fadeOut(
											function () {
												$description.css( 'right', '' );
											}
										);
									}
								);

						}
					}
				)

				if ( ! $reception_checkbox.is( ":checked" ) ) {
					$reception_settings.hide();
				}

				if ( ! $custom_video_checkbox.is( ":checked" ) ) {
					$custom_video_url.hide();
				}

				$reception_dropdown.on(
					'change',
					function () {
						var val = $( this ).val();
						if ( $( 'option[value=' + val, this ).data( 'hasVideo' ) ) {
							if ( $custom_video_checkbox.is( ":checked" ) ) {
								$custom_video_url.show();
							}

							$custom_video_settings.show();
						} else {
							$custom_video_settings.hide();
							$custom_video_url.hide();
						}
					}
				)

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

							if ( $floorplan_checkbox.is( ':checked' ) ) {
								$floorplan_checkbox.trigger( 'click' );
							}
						}

					}
				)

				$custom_video_checkbox.on(
					'change',
					function () {
						if ($( this ).is( ":checked" ) ) {
							$custom_video_url.show();
						} else {
							$custom_video_url.hide();
						}
					}
				)
			}
		)

		$outputs.each(
			function () {
				var $copy_to_clipboard_button = $( 'input.copy-to-clipboard ', this );

				$copy_to_clipboard_button.on(
					'click',
					function () {
						var $button      = $( this );
						var code         = $button.parent().children( 'code' ).html();
						var default_text = $button.prop( 'value' );

						navigator.clipboard.writeText( code ).then(
							function () {
								$button.prop( 'value', $outputs.data( 'copied-text' ) );

								setTimeout(
									function () {
										$button.prop( 'value', default_text );
									},
									2000
								)
							}
						);
					}
				);
			}
		)
	}
);
