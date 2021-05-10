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
		var hide_all_non_active = function () {
			var $tabs = $( '.myvideoroom-nav-tab-wrapper a:not(.nav-tab-active)' );

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
						$tabs.removeClass( 'nav-tab-active' );
						hide_all_non_active();

						$tab.addClass( 'nav-tab-active' );
						$( $tab.attr( 'href' ) ).show();

						event.preventDefault();
						return false;
					}
				)
			}
		)
	}
);
