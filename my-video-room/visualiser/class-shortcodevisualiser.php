<?php
/**
 * Allow user to Visualise Shortcodes with specific video preferences
 *
 * @package MyVideoRoomExtrasPlugin\BuddyPress
 */

namespace MyVideoRoomExtrasPlugin\Shortcode;

use MyVideoRoomExtrasPlugin\Core\SiteDefaults;
use MyVideoRoomExtrasPlugin\Entity\UserVideoPreference as UserVideoPreferenceEntity;
use MyVideoRoomExtrasPlugin\Shortcode as Shortcode;
use MyVideoRoomExtrasPlugin\Factory;

/**
 * Class UserVideoPreference
 * Allows the user to select their room display (note NOT Security) display parameters.
 */
class ShortcodeVisualiser extends Shortcode {
	/**
	 * A increment in case the same element is placed on the page twice
	 *
	 * @var int
	 */
	private static int $id_index = 0;

	/**
	 * Install the shortcode
	 */
	public function install() {
		$this->add_shortcode( 'visualizer', array( $this, 'visualiser_shortcode' ) );

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

			$user_id = SiteDefaults::USER_ID_SITE_DEFAULTS;

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

		$user_id        = SiteDefaults::USER_ID_SITE_DEFAULTS;
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

		$available_layouts    = Factory::get_instance( UserVideoPreference::class )->get_available_layouts( array( 'basic', 'premium' ) );
		$available_receptions = Factory::get_instance( UserVideoPreference::class )->get_available_receptions( array( 'basic', 'premium' ) );

		$render = require __DIR__ . '/../views/shortcode-visualiser.php';
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

}
