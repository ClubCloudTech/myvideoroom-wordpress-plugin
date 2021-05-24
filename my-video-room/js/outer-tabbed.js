/**
 * Add dynamic tabs to MyVideoRoom admin pages
 *
 * @package MyVideoRoomPlugin
 */

(function ($) {
	/**
	 * Hide all non active pages
	 */
	var hide_all_non_active = function () {
		var $tabs = $( '.myvideoroom-outer-nav-tab-wrapper a:not(.outer-nav-tab-active)' );

		$tabs.each(
			function () {
				var target = $( this ).attr( 'href' );
				$( target ).hide();
			}
		);
	};

	/**
	 * Initialise the plugin
	 *
	 * @param {JQuery} $parent
	 */
	var init = function ($parent) {
		hide_all_non_active();

		var $tabs = $( '.myvideoroom-outer-nav-tab-wrapper a', $parent );
		$tabs.each(
			function () {
				var $tab = $( this );
				$tab.on(
					'click',
					function (event) {
						$tabs.removeClass( 'outer-nav-tab-active' );
						hide_all_non_active();

						$tab.addClass( 'outer-nav-tab-active' );
						$( $tab.attr( 'href' ) ).show();

						event.preventDefault();
						return false;
					}
				);
			}
		);
	};

	init( $( document ) );

	window.myvideoroom_tabbed_init = init;
})( jQuery );
