<?php
/**
 * Display Security Templates.
 *
 * @package MyVideoRoomPlugin\Module\Security\Templates
 */

namespace MyVideoRoomPlugin\Module\Security\Templates;

use MyVideoRoomPlugin\Shortcode as Shortcode;

/**
 * Class Security Templates
 * This class holds templates for Blocked Access requests.
 */
class SecurityTemplates {

	/**
	 * Blocked By Site Offline Template.
	 *
	 * @param  int $user_id - The User_id.
	 * @return string
	 */
	public static function room_blocked_by_site( int $user_id = null ) {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This Room is Offline', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
		<?php
		esc_html_e( 'The Administrators have disabled this room. Please contact the site owner, or an admin for help.', 'myvideoroom' );
		?>
	</p>

</div>
		<?php

		return ' ';
	}

	/**
	 * Blocked By User Template.
	 *
	 * @param  int $user_id - the user ID who is blocking.
	 * @return string
	 */
	public function room_blocked_by_user( int $user_id ) {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This Room is Offline', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
					<?php
					$new_user   = get_userdata( $user_id );
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						echo '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
					} elseif ( $nicename ) {
						echo '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
					} else {
						esc_html_e( 'The Administrator', 'my-video-room' );
					}
					esc_html_e( ' has disabled this room. Please contact the site owner, or ', 'myvideoroom' );
					if ( $first_name ) {
						echo \esc_attr( ucfirst( $first_name ) );
					} else {
						echo esc_attr( ucfirst( $nicename ) );
					}
					esc_html_e( ' for more assistance.', 'myvideoroom' );
					?>
	</p>

</div>
		<?php

		return ' ';
	}

	/**
	 * Blocked Anonymous User Template.
	 *
	 * @param  int $user_id - the user ID who is blocking.
	 * @return string
	 */
	public function anonymous_blocked_by_user( $user_id ) {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>
<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This room is set to Signed in (known) Users Only', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
					<?php
					$new_user   = get_userdata( $user_id );
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						echo '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
					} elseif ( $nicename ) {
						echo '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
					} else {
						echo '<strong>' . esc_html_e( 'Site Policy', 'myvideoroom' ) . '</strong>';
					}
					esc_html_e(
						' only allows signed in/registered users to access their video room. To be able to access this room,
					you must have an account on this site. Please Register for access or ask ',
						'myvideoroom'
					);
					if ( $first_name ) {
						echo '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
					} elseif ( $nicename ) {
						echo '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
					} else {
						esc_html_e( 'The Administrator', 'my-video-room' );
					}
					esc_html_e( ' for more assistance.', 'myvideoroom' );
					?>
	</p>
</div>
		<?php

		return ' ';
	}

	/**
	 * Blocked By WP Role Template.
	 *
	 * @param  int $user_id - the user ID who is blocking.
	 * @return string
	 */
	public function blocked_by_role_template( $user_id ) {
		wp_enqueue_style( 'myvideoroom-template' );
		ob_start();
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This Room is set to Specific Roles Only', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
					<?php
					$new_user   = get_userdata( $user_id );
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						echo '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
					} elseif ( $nicename ) {
						echo '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
					} else {
						esc_html_e( 'The Administrator', 'my-video-room' );
					}
					esc_html_e(
						' has enabled this room only for specific roles of users. You are not in a group that has been given access. Please contact the site owner or ',
						'myvideoroom'
					);
					if ( $first_name ) {
						echo \esc_attr( ucfirst( $first_name ) );
					} elseif ( $nicename ) {
						echo esc_attr( ucfirst( $nicename ) );
					} else {
						esc_html_e( 'the site administrators', 'my-video-room' );
					}
					esc_html_e( ' for more assistance.', 'myvideoroom' );
					?>
	</p>
		<?php

		return ' ';
	}

}
