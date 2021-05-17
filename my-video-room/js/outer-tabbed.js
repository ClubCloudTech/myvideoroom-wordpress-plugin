/**
 * Add dynamic tabs to MyVideoRoom Outer Navigation Templates
 *
 * @package MyVideoRoomPlugin
 */

jQuery.noConflict()(
	function () {
		var $     = jQuery.noConflict();
		var $tabs = $( '.myvideoroom-outer-tab-wrapper a' );

		/**
		 * Hide all non active pages
		 */
		var hide_all_non_active = function () {
			var $tabs = $( '.myvideoroom-outer-tab-wrapper a:not(.mvr-outer-tab-active)' );

			$tabs.each(
				function () {
					var target = $( this ).attr( 'href' );
					$( target ).hide();
				}
			)
		}

		hide_all_non_active();

		$tabs.each(
			function () {
				var $tab = $( this );
				$tab.on(
					'click',
					function (event) {
						$tabs.removeClass( 'mvr-outer-tab-active' );
						hide_all_non_active();

						$tab.addClass( 'mvr-outer-tab-active' );
						$( $tab.attr( 'href' ) ).show();

						event.preventDefault();
						return false;
					}
				)
			}
		)
	}
);
