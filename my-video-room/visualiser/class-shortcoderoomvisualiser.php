<?php
/**
 * Allow user to Visualise Shortcodes with specific video preferences
 *
 * @package MyVideoRoomPlugin\ShortcodeRoomVisualiser
 */

namespace MyVideoRoomPlugin\Visualiser;

use MyVideoRoomPlugin\Visualiser\UserVideoPreference as UserVideoPreferenceEntity;

/**
 * Class UserVideoPreference
 * Allows the user to select their room display (note NOT Security) display parameters.
 */
class ShortcodeRoomVisualiser {
	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Install the shortcode
	 */
	public function init() {
		add_shortcode( 'visualizer', array( $this, 'visualiser_shortcode' ) );

		add_action( 'admin_head', fn() => do_action( 'myvideoroom_head' ) );

	}

	/**
	 * Render shortcode to allow user to update their settings
	 *
	 * @param array $params List of shortcode params.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function visualiser_shortcode( $params = array() ): string {

		$room_name    = $params['room'] ?? 'default';
		$allowed_tags = array_map( 'trim', explode( ',', $params['tags'] ?? '' ) );
		// Not strictly needed as its a demo render- but preserving consistent structure with main Video Function.

			$user_id = MyVideoRoomApp::USER_ID_SITE_DEFAULTS;

		return $this->visualiser_worker( $user_id, $room_name, $allowed_tags );
	}

	/**
	 * Show drop down for user to change their settings
	 *
	 * @param int    $user_id The user id to fetch.
	 * @param string $room_name The room name to fetch.
	 * @param array  $allowed_tags List of tags to allow.
	 *
	 * @return string
	 * @throws \Exception When the update fails.
	 */
	public function visualiser_worker( int $user_id, string $room_name, array $allowed_tags = array() ) {
		// we only enqueue the scripts if the shortcode is called to prevent it being added to all admin pages.
		do_action( 'myvideoroom_enqueue_scripts' );

		/*
			First Section - Handle Data from Inbound Form, and process it.

		*/

		$user_id        = MyVideoRoomApp::USER_ID_SITE_DEFAULTS;
		$show_floorplan = false;
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'myvideoroom_extras_update_user_video_preference', 'nonce' );
		}

			$room_name             = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_room_name'] ?? 'Your Room Name' ) );
			$video_template        = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_layout_id_preference'] ?? null ) );
			$reception_template    = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_id_preference'] ?? null ) );
			$reception_setting     = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_enabled_preference'] ?? '' ) ) === 'on';
			$video_reception_state = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_reception_video_enabled_preference'] ?? '' ) ) === 'on';
			$show_floorplan        = sanitize_text_field( wp_unslash( $_POST['myvideoroom_visualiser_show_floorplan_preference'] ?? '' ) ) === 'on';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, - esc_url raw does the appropriate sanitisation.
			$video_reception_url = esc_url_raw( $_POST['myvideoroom_visualiser_reception_waiting_video_url'] ?? '' );

		if ( $show_floorplan ) {
				$show_floorplan    = true;
				$reception_setting = true;
		}

				$current_user_setting = new UserVideoPreferenceEntity(
					$user_id,
					$room_name,
					$video_template,
					$reception_template,
					$reception_setting,
					$video_reception_state,
					$video_reception_url,
					$show_floorplan
				);

		$available_layouts    = $this->get_available_layouts( array( 'basic', 'premium' ) );
		$available_receptions = $this->get_available_receptions( array( 'basic', 'premium' ) );

		$render = require __DIR__ . '/views-visualiser.php';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All upstream variables have already been sanitised in their function.
		echo $render( $available_layouts, $available_receptions, $current_user_setting, $room_name, self::$id_index++, $user_id, $video_reception_url );

		/*
			Second Section - Handle Rendering of Inbound Shortcodes for correct construction.

			Use Entered Form Data to Build a Host and Guest Shortcode Pair
		*/

		if (
			isset( $_SERVER['REQUEST_METHOD'] ) &&
			'POST' === $_SERVER['REQUEST_METHOD']
		) {
			// Guest Section.
				$myvideoroom_guest = MyVideoRoomApp::create_instance(
					$room_name,
					$video_template,
				);
			// Reception Handling.
			if ( $reception_setting && $reception_template ) {
				$myvideoroom_guest->enable_reception()->set_reception_id( $reception_template );

				if ( $video_reception_state && $video_reception_url ) {
					$myvideoroom_guest->set_reception_video_url( $video_reception_url );
				}
			}
			// Guest Floorplan option.
			if ( $show_floorplan ) {
				$myvideoroom_guest->enable_floorplan();
			}

			// Host Section Shortcode.
			$myvideoroom_host = MyVideoRoomApp::create_instance(
				$room_name,
				$video_template,
			)->enable_admin();

			// Construct Shortcode Template - and execute.

			$shortcode_host       = $myvideoroom_host->output_shortcode();
			$shortcode_guest      = $myvideoroom_guest->output_shortcode();
			$text_shortcode_host  = $myvideoroom_host->output_shortcode( 'shortcode-view-only' );
			$text_shortcode_guest = $myvideoroom_guest->output_shortcode( 'shortcode-view-only' );

			/*
				Third Section - Render Result Post Submit

			*/

			?>

			<table class ="cc-table" style=" width:100% ;border: 3px solid #969696;	background: #ebedf1; padding: 12px;	margin: 5px;">

			<tr>
				<th style="width:50%" ><h3>Host View</h3>
				<th style="width:50%" ><h3>Guest View</h3></th>
			</tr>
			<tr>
				<td>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Shortcode function already sanitised by its constructor function.
				echo $shortcode_host;
				?>
				</td>myvideoroom-extras-plugin/views/shortcode-visualiser.php

				<td>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Shortcode function already sanitised by its constructor function.
				echo $shortcode_guest;
				?>
				</td>
			</tr>

			<tr>
					<td>
						<strong>Shortcode</strong><br>
						<code style="user-select: all">[<?php echo esc_html( $text_shortcode_host ); ?>]</code>
					</td>
					<td>
						<strong>Shortcode</strong><br>
						<code style="user-select: all">[<?php echo esc_html( $text_shortcode_guest ); ?>]</code>
					</td>
			</tr>

			</table>

			<?php
		}

		return 'My Video Room by ClubCloud';
	}

	/**
	 * Get a list of available layouts from MyVideoRoom
	 *
	 * @param array $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_layouts( array $allowed_tags = array( 'basic' ) ): array {
		$scenes = $this->get_available_scenes( 'layouts', $allowed_tags );
		if ( $scenes ) {
			return $scenes;
		} else {
			return array( 'No Layouts Found' );
		}
	}

	/**
	 * Get a list of available receptions from MyVideoRoom
	 *
	 * @param array $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_receptions( array $allowed_tags = array( 'basic' ) ): array {
		return $this->get_available_scenes( 'receptions', $allowed_tags );
	}

	/**
	 * Get a list of available scenes from MyVideoRoom
	 *
	 * @param string         $uri The type of scene (layouts/receptions).
	 * @param array|string[] $allowed_tags List of tags to fetch.
	 *
	 * @return array
	 */
	public function get_available_scenes( string $uri, array $allowed_tags = array( 'basic' ) ): array {
		$url     = 'https://rooms.clubcloud.tech/' . $uri;
		$tag_uri = array();

		foreach ( $allowed_tags as $allowed_tag ) {
			$tag_uri[] = 'tag[]=' . $allowed_tag;
		}

		if ( $tag_uri ) {
			$url .= '?' . implode( '&', $tag_uri );
		}

		$request = \wp_remote_get( $url );

		if ( \is_wp_error( $request ) ) {
			return array();
		}

		$body = \wp_remote_retrieve_body( $request );

		return \json_decode( $body );
	}


	/**
	 * Display_room_template_browser
	 *
	 * @return void
	 */
	public function display_room_template_browser() {

		?>

	<div class="wrap">


		<h1>Room Template Browser</h1>
		<p> Use the Template Browser tab to view room selection templates<br>    </p>

		<script type="text/javascript">
	function activateTab(pageId) {
		var tabCtrl = document.getElementById( 'tabCtrl' );
		var pageToActivate = document.getElementById(pageId);
		for (var i = 0; i < tabCtrl.childNodes.length; i++) {
			var node = tabCtrl.childNodes[i];
			if (node.nodeType == 1) { /* Element */
				node.style.display = (node == pageToActivate) ? 'block' : 'none';
			}
		}
	}
	</script>
	<ul class="menu" style="display: flex;    justify-content: space-between;    width: 50%;">
		<a class="cc-menu-header" href="javascript:activateTab( 'page1' )" style="text-align: justify ;color: #000000;    font-family: Montserrat, Sans-serif; font-size: 20px;     font-weight: 200;    text-transform: capitalize;">Video Room Templates</a>
		<a class="cc-menu-header" href="javascript:activateTab( 'page2' )" style="text-align: justify ;color: #000000;    font-family: Montserrat, Sans-serif; font-size: 20px;     font-weight: 200;    text-transform: capitalize;">Reception Templates</a>
		<a class="cc-menu-header" href="javascript:activateTab( 'page3' )" style="text-align: justify ;color: #000000;    font-family: Montserrat, Sans-serif; font-size: 20px;     font-weight: 200;    text-transform: capitalize;">Using Templates</a>
	</ul>
		<div id="tabCtrl">
			<div id="page1" style="display: block; "><iframe src="https://rooms.clubcloud.tech/views/layout?tag[]=basic&tag[]=premium&embed=tru" width="100%" height="1600px" frameborder="0" scrolling="yes" align="left"> </iframe>
			</div>
			<div id="page2" style="display: none;"><iframe src="https://rooms.clubcloud.tech/views/reception?tag[]=basic&tag[]=premium&embed=true" width="100%" height="1600px" frameborder="0" scrolling="yes" align="left"> </iframe>
			</div>
			<div id="page3" style="display: none;">
				<h1>How to Use Templates</h1>
				<p> Templates can be used as arguments into any shortcode you build manually with [clubvideo], or in drop down boxes of Menus of Club Cloud Video Extras</p>
			</div>
		</div>
	</div>

	</div>
		<?php

	}



}
