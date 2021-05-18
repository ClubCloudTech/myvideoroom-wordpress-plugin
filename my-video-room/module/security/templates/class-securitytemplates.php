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
class SecurityTemplates extends Shortcode {

	/**
	 * Blocked By Site Offline Template.
	 *
	 * @param  int $user_id - The User_id.
	 * @return string
	 */
	public static function room_blocked_by_site( int $user_id = null ) {
		ob_start();
		wp_enqueue_style( 'mvr-template' );
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				echo esc_html_e( 'This Room is Offline', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
		<?php
		echo esc_html_e( 'The Administrators have disabled this room. Please contact the site owner, or an admin for help.', 'myvideoroom' );
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
		wp_enqueue_style( 'mvr-template' );
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				echo esc_html_e( 'This Room is Offline', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
					<?php
					$new_user   = get_userdata( $user_id );
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						echo '<b>' . esc_html( ucfirst( $first_name ) ) . '</b>';
					} elseif ( $nicename ) {
						echo '<b>' . esc_html( ucfirst( $nicename ) ) . '</b>';
					} else {
						echo esc_html__( 'The Administrator', 'my-video-room' );
					}
					echo esc_html_e( ' has disabled this room. Please contact the site owner, or ', 'myvideoroom' );
					if ( $first_name ) {
						echo \esc_attr( ucfirst( $first_name ) );
					} else {
						echo esc_attr( ucfirst( $nicename ) );
					}
					echo esc_html_e( ' for more assistance.', 'myvideoroom' );
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
		wp_enqueue_style( 'mvr-template' );
		?>
<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				echo esc_html_e( 'This room is set to Signed in (known) Users Only', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
					<?php
					$new_user   = get_userdata( $user_id );
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						echo '<b>' . esc_html( ucfirst( $first_name ) ) . '</b>';
					} else {
						echo '<b>' . esc_html( ucfirst( $nicename ) ) . '</b>';
					}
					echo esc_html_e(
						' only allows signed in/registered users to access their video room. To be able to access this room,
					you must have an account on this site. Please Register for access or ask ',
						'myvideoroom'
					);
					if ( $first_name ) {
						echo '<b>' . esc_html( ucfirst( $first_name ) ) . '</b>';
					} elseif ( $nicename ) {
						echo '<b>' . esc_html( ucfirst( $nicename ) ) . '</b>';
					} else {
						echo esc_html__( 'The Administrator', 'my-video-room' );
					}
					echo esc_html_e( ' for more assistance.', 'myvideoroom' );
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
		wp_enqueue_style( 'mvr-template' );
		ob_start();
		?>

<div class="mvr-row">
	<h2 class="mvr-header-text">
			<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				echo esc_html_e( 'This Room is set to Specific Roles Only', 'myvideoroom' ) . '</h2>';
			?>
	<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

	<p class="mvr-template-text">
					<?php
					$new_user   = get_userdata( $user_id );
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						echo '<b>' . esc_html( ucfirst( $first_name ) ) . '</b>';
					} elseif ( $nicename ) {
						echo '<b>' . esc_html( ucfirst( $nicename ) ) . '</b>';
					} else {
						echo esc_html__( 'The Administrator', 'my-video-room' );
					}
					echo esc_html_e(
						' has enabled this room only for specific roles of users. You are not in a group that has been given access. Please contact the site owner or ',
						'myvideoroom'
					);
					if ( $first_name ) {
						echo \esc_attr( ucfirst( $first_name ) );
					} else {
						echo esc_attr( ucfirst( $nicename ) );
					}
					echo esc_html_e( ' for more assistance.', 'myvideoroom' );
					?>
	</p>
		<?php

		return ' ';
	}
	/**
	 * Blocked By Group Membership Template.
	 *
	 * @param  int $user_id - the user ID who is blocking.
	 * @return string
	 */
	public function blocked_by_group_membership( $user_id = null ) {
		ob_start();
		wp_enqueue_style( 'mvr-template' );
		?>
<div class="mvr-row">

	<table class="mvr-table">
		<tr>
			<th style="width:50%">
				<img class="mvr-user-image" src="
										" alt="Image">
			</th>
			<th>
				<h2 class="mvr-reception-header"><?php echo esc_html( esc_html( get_bloginfo( 'name' ) ) ) . esc_html__( ' This room is set to Group Members Only', 'my-video-room' ); ?></h2>

				<img class="mvr-access-image" src="
					<?php
					//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - Get site URL is already escaped and the rest is text.
					echo get_site_url() . '/wp-content/plugins/myvideoroom-extras/noentry.jpg';
					?>
				" alt="Site Logo">
			</th>
	</table>
	<p class="mvr-header-text">
		<?php

		$new_user   = get_userdata( $user_id );
		if ( $new_user->user_firstname ) {
			$initial_admin_name = '<strong>' . esc_html( $new_user->user_firstname ) . '</strong>';
			$secondary_admin_name = $initial_admin_name;
		} elseif ( $new_user->user_nicename ) {
			$initial_admin_name  = '<strong>' . esc_html( $new_user->user_nicename ) . '</strong>';
			$secondary_admin_name = $initial_admin_name;
		} else {
			$initial_admin_name  = esc_html__( 'The administrator', 'my-video-room' );
			$secondary_admin_name  = esc_html__( 'the administrator', 'my-video-room' );
		}

		printf(
			/* translators: Both %s refer to the name of the administrator */
			esc_html__( '%1$s or one of the moderators have enabled this room only for specific membership of the group. You are not in a class of user that %2$s or the group moderators have allowed. Please contact any of the group admins or moderators for assistance.' ),
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
			$initial_admin_name,
			//phpcs:ignore --WordPress.Security.EscapeOutput.OutputNotEscaped - output already escaped in function
			$secondary_admin_name
		)
		?>

</div>
		<?php
		return ' ';
	}
}
