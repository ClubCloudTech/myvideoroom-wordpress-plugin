/**
 * Add dynamic tabs to MyVideoRoom admin pages
 *
 * @package MyVideoRoomPlugin
 */

jQuery.noConflict()(
	function () {
		var $     = jQuery.noConflict();
		var $tabs = $( '.myvideoroom-nav-tab-wrapper a' );

		/**
		 * Hide all non active pages
		 */
		var hideAll = function () {
			var $tabs = $( '.myvideoroom-nav-tab-wrapper a:not(.nav-tab-active)' );

			$tabs.each(
				function () {
					var $item    = $( this );
					var $section = $( $item.attr( 'href' ) );
					$section.hide();
				}
			)
		}

		hideAll();

		$tabs.each(
			function () {
				var $item = $( this );
				$item.on(
					'click',
					function (event) {
						$tabs.each(
							function () {
								var $item = $( this );
								$item.removeClass( 'nav-tab-active' );
							}
						)

						$item.addClass( 'nav-tab-active' );

						var $section = $( $item.attr( 'href' ) );
						$section.show();

						hideAll();

						event.preventDefault();
						return false;
					}
				)
			}
		)
	}
);
