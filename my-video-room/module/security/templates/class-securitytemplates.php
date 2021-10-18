<?php
/**
 * Display Security Templates.
 *
 * @package MyVideoRoomPlugin\Module\Security\Templates
 */

namespace MyVideoRoomPlugin\Module\Security\Templates;

/**
 * Class Security Templates
 * This class holds templates for Blocked Access requests.
 */
class SecurityTemplates {

	/**
	 * Blocked By Site Offline Template.
	 *
	 * @return string
	 */
	public static function room_blocked_by_site(): string {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>

		<div class="mvr-row mvr-background">
			<h2 class="mvr-header-text">
				<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This Room is Offline', 'myvideoroom' );
				?>
			</h2>
			<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

			<p class="mvr-template-text">
				<?php
				esc_html_e( 'The Administrators have disabled this room. Please contact the site owner, or an admin for help.', 'myvideoroom' );
				?>
			</p>
		</div>
		<?php

		return \ob_get_clean();
	}

	/**
	 * Blocked By User Template.
	 *
	 * @param int $user_id - the user ID who is blocking.
	 *
	 * @return string
	 */
	public function room_blocked_by_user( int $user_id ): string {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>

		<div class="mvr-row mvr-background">
			<h2 class="mvr-header-text">
				<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This Room is Offline', 'myvideoroom' );
				?>
			</h2>
			<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

			<p class="mvr-template-text">
				<?php
				$new_user           = get_userdata( $user_id );
				$first_display_name = '<strong>' . esc_html__( 'Site Policy', 'my-video-room' ) . '</strong>';
				if ( $new_user ) {
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						$first_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
					} elseif ( $nicename ) {
						$first_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
					}
				}
				$second_display_name = esc_html__( 'the site administrators', 'my-video-room' );
				if ( $new_user ) {
					$first_name = $new_user->user_firstname;
					$nicename   = $new_user->user_nicename;
					if ( $first_name ) {
						$second_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
					} elseif ( $nicename ) {
						$second_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
					}
				}
				echo sprintf(
				/* translators: %1s is the text "Site Policy" and %2s is "the site administrators" */
					esc_html__( ' %1$s  has disabled this room. Please contact the site owner, or  %2$s for more assistance.', 'myvideoroom' ),
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
					$first_display_name,
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
					$second_display_name
				);
				?>
			</p>

		</div>
		<?php

		return \ob_get_clean();
	}

	/**
	 * Blocked Anonymous User Template.
	 *
	 * @param int $user_id - the user ID who is blocking.
	 *
	 * @return string
	 */
	public function anonymous_blocked_by_user( int $user_id ): string {
		ob_start();
		wp_enqueue_style( 'myvideoroom-template' );
		?>
		<div class="mvr-row mvr-background">
			<h2 class="mvr-header-text">
				<?php
				echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
				esc_html_e( 'This room is set to Signed in (known) Users Only', 'myvideoroom' ) . '</h2>';
				?>
				<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

				<p class="mvr-template-text">
					<?php
					$new_user           = get_userdata( $user_id );
					$first_display_name = '<strong>' . esc_html__( 'Site Policy', 'my-video-room' ) . '</strong>';
					if ( $new_user ) {
						$first_name = $new_user->user_firstname;
						$nicename   = $new_user->user_nicename;
						if ( $first_name ) {
							$first_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
						} elseif ( $nicename ) {
							$first_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
						}
					}
					$second_display_name = esc_html__( 'the site administrators', 'my-video-room' );
					if ( $new_user ) {
						$first_name = $new_user->user_firstname;
						$nicename   = $new_user->user_nicename;
						if ( $first_name ) {
							$second_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
						} elseif ( $nicename ) {
							$second_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
						}
					}
					echo sprintf(
					/* translators: %1s is the text "Site Policy" and %2s is "the site administrators" */
						esc_html__( ' %1$s  only allows signed in/registered users to access a video room. To be able to access this room, you must have an account on this site. Please Register for access or ask  %2$s for more assistance.', 'myvideoroom' ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
						$first_display_name,
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
						$second_display_name
					);
					?>
				</p>
		</div>
		<?php

		return \ob_get_clean();
	}

	/**
	 * Blocked By WP Role Template.
	 *
	 * @param int $user_id - the user ID who is blocking.
	 *
	 * @return string
	 */
	public function blocked_by_role_template( int $user_id ): string {
		wp_enqueue_style( 'myvideoroom-template' );
		ob_start();
		?>

		<div class="mvr-row mvr-background">
		<h2 class="mvr-header-text">
		<?php
		echo esc_html( get_bloginfo( 'name' ) ) . ' - ';
		esc_html_e( 'This Room is set to Specific Roles Only', 'myvideoroom' ) . '</h2>';
		?>
		<img class="mvr-access-image" src="
			<?php echo esc_url( plugins_url( '../../../img/noentry.jpg', __FILE__ ) ); ?>" alt="No Entry">

		<p class="mvr-template-text">
			<?php
			$new_user           = get_userdata( $user_id );
			$first_display_name = '<strong>' . esc_html__( 'The Administrator', 'my-video-room' ) . '</strong>';
			if ( $new_user ) {
				$first_name = $new_user->user_firstname;
				$nicename   = $new_user->user_nicename;
				if ( $first_name ) {
					$first_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
				} elseif ( $nicename ) {
					$first_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
				}
			}
			$second_display_name = esc_html__( 'the site administrators', 'my-video-room' );
			if ( $new_user ) {
				$first_name = $new_user->user_firstname;
				$nicename   = $new_user->user_nicename;
				if ( $first_name ) {
					$second_display_name = '<strong>' . esc_html( ucfirst( $first_name ) ) . '</strong>';
				} elseif ( $nicename ) {
					$second_display_name = '<strong>' . esc_html( ucfirst( $nicename ) ) . '</strong>';
				}
			}
			echo sprintf(
			/* translators: %1s is the text "The Administrator" and %2s is "the site administrators" */
				esc_html__( '%1$s has enabled this room only for specific roles of users. You are not in a group that has been given access. Please contact the site owner or %2$s for more assistance.', 'myvideoroom' ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
				$first_display_name,
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above.
				$second_display_name
			);
			?>
		</p>
		<?php

		return \ob_get_clean();
	}

}
