<?php
/**
 * Outputs the Permissions Settings for the video plugin
 *
 * @package MyVideoRoomPlugin\Admin
 */

declare( strict_types=1 );
global $wp_roles;

use MyVideoRoomPlugin\Plugin;

/**
 * Render the Security Permissions Admin page
 *
 * @param array $messages     A list of messages to show. Takes the form [type=:string, message=:message][]
 * @param array $all_wp_roles A list of WordPress roles. @see global $wp_roles->roles.
 */
return function (
	array $messages = array(),
	array $all_wp_roles = array()
): string {
	ob_start();
	?>
    <h1 class="myvideoroom-heading-head-top"><?php esc_html_e( 'Video Security', 'myvideoroom' ); ?></h1>
    <div class="myvideoroom-tab-align">
        <ul>
            <?php
            foreach ( $messages as $message ) {
                echo '<li class="notice ' . esc_attr( $message['type'] ) . '"><p>' . esc_html( $message['message'] ) . '</p></li>';
            }
            ?>
        </ul>

        <h3><?php esc_html_e( 'Default Video Room Hosts', 'myvideoroom' ); ?></h3>
        <p>
            <?php
            esc_html_e(
                'By default you have two shortcodes generated for your pages by the room builder. One is for the Host, and one for guest.
                This setting configures, who the Video Engine will treat as a Host in case you haven\'t provided a Host Shortcode. 
                By default, the Application will take your Site Administrators Group as its default host. You can override that section below, and add additional WordPress roles to your Host permissions Matrix. ',
                'myvideoroom'
            );
            ?>
        </p>
        <form method="post" action="">
            <fieldset>
                <table class="form-table" id="myvideoroom-role-display-table"role="presentation">
                    <tbody>
                        <?php
                        foreach ( $all_wp_roles as $key => $single_role ) {
                            $role_object   = get_role( $key );
                            $has_admin_cap = $role_object->has_cap( Plugin::CAP_GLOBAL_ADMIN );

                            echo '<th scope="row" class="tabCtrl-header"><label for="role_' . esc_attr( $key ) . '">' . esc_html( $single_role['name'] ) . '</label>';
                            echo '<input class="myvideoroom-admin-table-format" id="role_' . esc_attr( $key ) . '" name="role_' . esc_attr( $key ) . '" type="checkbox" ' . ( $has_admin_cap ? 'checked="checked" ' : '' ) . '/></th>';
                        }
                        ?>
                    </tbody>
                </table>
            </fieldset>

            <?php wp_nonce_field( 'update_caps', 'nonce' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>

	<?php
	return ob_get_clean();
};
