/**
 * Add dynamic tabs to MyVideoRoom admin pages
 *
 * @package MyVideoRoomPlugin
 */

(function ($) {

	/**
	 * Hide all non active pages
	 */
	var hide_all_non_active = function ($nav_section) {
		var $tabs = $( 'a.nav-tab:not(.nav-tab-active)', $nav_section );

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
		var $tabbed_sections = $( '.myvideoroom-nav-tab-wrapper', $parent );

		$tabbed_sections.each(
			function () {
				var $nav_section = $( this );
				hide_all_non_active( $nav_section );

				var $tabs = $( 'a.nav-tab', $nav_section );
				$tabs.each(
					function () {
						var $tab = $( this );
						$tab.on(
							'click',
							function (event) {

								$tab.trigger( 'focus' );

								$tabs.removeClass( 'nav-tab-active' );
								hide_all_non_active( $nav_section );

								$tab.addClass( 'nav-tab-active' );
								$( $tab.attr( 'href' ) ).show();

								event.preventDefault();
								return false;
							}
						);
						$( '.mvr-notification-button' ).on(
							'click',
							function (event) {

								$tab.trigger( 'focus' );

								$tabs.removeClass( 'nav-tab-active' );
								hide_all_non_active( $nav_section );

								$tab.addClass( 'nav-tab-active' );
								$( $tab.attr( 'href' ) ).show();

								event.preventDefault();
								return false;
							}
						);
					}
				);
			}
		);

	};

	init( $( document ) );

	window.myvideoroom_tabbed_init = init;
})( jQuery );
