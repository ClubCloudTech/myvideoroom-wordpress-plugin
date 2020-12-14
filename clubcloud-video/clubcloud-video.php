<?php

/**
 * @link              https://clubcloud.tech
 * @since             0.1
 * @package           clubcloud-video
 *
 * @wordpress-plugin
 * Plugin Name: ClubCloud Video Plugin
 * Plugin URI: https://clubcloud.tech
 * Description: Allows you to use a shortcode to enable a ClubCloud meeting into your webpage.
 * Version: 0.1
 * Author: ClubCloud Awesome Developers
 * Author URI: https://clubcloud.tech/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: clubcloud-video
 **/

if (!defined('WPINC')) {
    die;
}

// Install ClubCloud Databases and Plugin
global $clubcloud_db_version;
$clubcloud_db_version = '1.0';

add_action( 'admin_menu', 'clubcloud_setup_menu' );

function clubcloud_setup_menu() {
    add_menu_page( 'ClubCloud Video Settings', 'ClubCloud Video Settings', 'manage_options', 'clubcloud-Video-settings', 'clubcloud_admin_page_init', 'dashicons-format-chat' );
    add_action( 'admin_init', 'clubcloud_register_settings' );
}

function clubcloud_register_settings() {
    register_setting( 'clubcloud', 'cc_video_server_url' );
    register_setting( 'clubcloud', 'cc_room_server_url' );
    register_setting( 'clubcloud', 'cc_app_url' );
    register_setting( 'clubcloud', 'clubcloud_web_token_key' );
}

function clubcloud_admin_page_init() {
    ?>
    <div class="wrap">
        <h1>ClubCloud Video Short Code Settings</h1>

        <br/><br/>

        <h2>ShortCode</h2>
        <p>You can use the following
            <a href="https://support.wordpress.com/shortcodes/" target="_blank">ShortCode</a> to create a button to start a new Meeting.
        </p>
        <p><code>[clubvideo]</code></p>
        <p><b>Options</b>..</p>
        <ul>
            <li>
                <i>(required)</i> Set the event details event_id sets the event info if set to 'username' the loggined in username will be used as the meeting name
            </li>
            <li><i>(Optional)</i> Set the event details yt_url sets the embedded keynote url i.e. a Youtube live event
            </li>
        </ul>
        <p><b>Example</b>..</p>
        <code>[clubvideo event_id="MainEvent" yt_url="https://www.youtube.com/watch?v=Db5z6rNMF7w" ]</code>
        <br/><br/>
        <h2>Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'clubcloud' ); ?>
            <?php do_settings_sections( 'clubcloud' ); ?>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row">ClubCloud Video URL</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>ClubCloud Server URL</span></legend>
                            <label for="cc_video_server_url">
                                <input type="text" name="cc_video_server_url" value="<?php echo get_option( 'cc_video_server_url' ) ?>" placeholder="eg. https://meet.domain.tld/" id="cc_video_server_url" size="100" class=""/>
                            </label>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ClubCloud Room Manager URL</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>ClubCloud Room Manager URL</span></legend>
                            <label for="cc_video_server_url">
                                <input type="text" name="cc_room_server_url" value="<?php echo get_option( 'cc_room_server_url' ) ?>" placeholder="eg. https://state.domain.tld/" id="cc_room_server_url" size="100" class=""/>
                            </label>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ClubCloud App URL</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>ClubCloud App URL</span></legend>
                            <label for="cc_app_url">
                                <input type="text" name="cc_app_url" value="<?php echo get_option( 'cc_app_url' ) ?>" placeholder="eg. https://app.domain.tld/" id="cc_room_server_url" size="100" class=""/>
                            </label>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ClubCloud Moderator Token Key</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span>ClubCloud Moderator Token Key (Provided by ClubCloud)</span></legend>
                            <label for="clubcloud_web_token_key">
                                <input type="text" name="clubcloud_web_token_key" value="<?php echo get_option( 'clubcloud_web_token_key' ) ?>" placeholder="(Provided by ClubCloud)" id="clubcloud_web_token_key" size="100" class=""/>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Use User Details From Wordpress</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Use User Details for registered users</span>
                            </legend>
                            <label for="clubcloud_username_pull">
                                <input name="clubcloud_username_pull" type="checkbox" id="clubcloud_username_pull" value="1" <?php checked( 1, get_option( 'clubcloud_username_pull' ), true ); ?> />
                                Use Wordpress username and avatar
                            </label>
                            <br/>
                            <label for="clubcloud_email_pull">
                                <input name="clubcloud_email_pull" type="checkbox" id="clubcloud_email_pull" value="1" <?php checked( 1, get_option( 'clubcloud_email_pull' ), true ); ?> />
                                Use users email address
                            </label>
                        </fieldset>
                        <p><b>NB.</b> These will <b>override</b> anything you have set in the ShortCode settings!</p>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>

    </div>
    <?php
}

CONST CC_SHARED_SECRET = 'FbDKL0Tbfe1U7G1KjHVNT5JABqj9CyU8uc5r25UBzBCCd1NiDPRGvqKZY0sC1Gdb';

add_shortcode('clubvideo', 'clubcloud_shortcode');
function clubcloud_shortcode($atts)
{
    $roomName = $atts['name'] ?: $atts['cc_event_id'];
    $mapId = $atts['map'] ?: $atts['cc_plan_id'];
    $enableLobby = !!($atts['lobby'] ?: $atts['cc_enable_lobby']);

    $admin = !!($atts['admin'] ?: $atts['auth']);

    $domain = (parse_url(get_option('cc_video_server_url'), PHP_URL_HOST));

    $server = get_option('cc_room_server_url');
    //$server = 'http://localhost:4001';

    $appEndpoint = get_option('cc_app_url');
    //$appEndpoint = 'http://localhost:3000';

    $roomHash = md5(json_encode([
        'type' => 'roomHash',
        'roomName' => $roomName,
        'mapId' => $mapId,
        'domain' => $domain,
        'sharedSecret' => CC_SHARED_SECRET
    ]));

    $password = hash('sha256', json_encode([
        'type' => 'password',
        'roomName' => $roomName,
        'mapId' => $mapId,
        'domain' => $domain,
        'sharedSecret' => CC_SHARED_SECRET
    ]));

    $securityToken = hash('sha256', json_encode([
            'domain' => $domain,
            'roomName' => $roomName,
            'admin' => $admin,
            'sharedSecret' => CC_SHARED_SECRET
    ]));

    $jwtEndpoint = get_site_url() . '/wp-json/clubcloud/jwt';

    $currentUser = wp_get_current_user();
    $userName = $currentUser ? $currentUser->display_name : null;
    $avatarUrl = getAvatar($currentUser);


    return <<<EOT

        <script>
            var $ = jQuery.noConflict();
            $.get("${appEndpoint}/asset-manifest.json").then(function (data) {
                data.entrypoints.map(function (entrypoint) {
                    if (entrypoint.endsWith(".js")) {
                        $.getScript("${appEndpoint}/"+ entrypoint);
                    } else {
                        $('<link rel="stylesheet" href="${appEndpoint}/' + entrypoint + '" type="text/css" />').appendTo('head');
                    }
                });
            })
        </script>
        
        <div
                style="width: 100%; border: 1px solid black"
                id="clubcloud-video-react-app"
                data-embedded="true"
                data-room-name="${roomName}"
                data-map-id="${mapId}"
                data-domain="${domain}"
                data-jwt-endpoint="${jwtEndpoint}"
                data-server-endpoint="${server}"
                data-admin="${admin}"
                data-enable-lobby="${enableLobby}"
                data-room-hash="${roomHash}"
                data-password="${password}"
                data-security-token="${securityToken}"
                data-name="${userName}"
                data-avatar="${avatarUrl}"
                data-rooms-endpoint="${appEndpoint}"
        ></div>
EOT;
}

add_action( 'rest_api_init', 'prefix_register_example_routes' );
function prefix_register_example_routes() {
    // register_rest_route() handles more arguments but we are going to stick to the basics for now.
    register_rest_route( 'clubcloud', '/jwt', array(
        // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
        'methods'  => WP_REST_Server::READABLE,
        // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
        'callback' => function ($data) {
            $roomName = $data->get_param( 'room' );
            $roomId = $data->get_param( 'room_id' );
            $token = $data->get_param( 'token' );
            $domain = (parse_url(get_option('cc_video_server_url'), PHP_URL_HOST));

            $securityToken = hash('sha256', json_encode([
                'domain' => $domain,
                'roomName' => $roomName,
                'admin' => true,
                'sharedSecret' => CC_SHARED_SECRET
            ]));

            if ($securityToken !== $token) {
                return wp_send_json_error('Incorrect token', 403);
            }

            return rest_ensure_response([
                'jwt'=> cc_get_jwt($roomId)
            ]);
        },
    ) );
}

function add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

function cc_get_jwt( $cc_roomname ) {
    $cc_user = null;
    $domain = (parse_url(get_option('cc_video_server_url'), PHP_URL_HOST));
    $client_token  = ( get_option( 'clubcloud_web_token_key' ) );                          // this collects the toke string in the admin settings used for all encryption and decryption
    $cc_roomname   = strtolower( $cc_roomname );                                          // sets the roomname/conf to lower case for the token to be issued

    $header             = json_encode( [ 'typ' => 'JWT', 'alg' => 'HS256', 'kid' => $domain ] );
    $payload            = json_encode( [
        'user_id' => $cc_user,
        "iss"     => $domain,
        "iat"     => idate( 'U' ),
        "exp"     => idate( 'U' ) + 3000,
        "aud"     => $domain,
        "sub"     => $domain,
        "room"    => $cc_roomname
    ] );
    $base64UrlHeader    = str_replace( [ '+', '/', '=' ], [
        '-',
        '_',
        ''
    ], base64_encode( $header ) );  // above is the setup to generate the jwt this setss the heder information
    $base64UrlPayload   = str_replace( [ '+', '/', '=' ], [
        '-',
        '_',
        ''
    ], base64_encode( $payload ) );       // sets the payload for the video platform
    $signature          = hash_hmac( 'sha256', $base64UrlHeader . "." . $base64UrlPayload, $client_token, true );      // base 64 hashes the payload and then ecrypts the header and payload with the client key stored in the admin settings
    $base64UrlSignature = str_replace( [ '+', '/', '=' ], [ '-', '_', '' ], base64_encode( $signature ) );
    $jwt                = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;  // concatinates the ecrypted string and returns the jwt

    return $jwt;
}

function getAvatar( $user ) {
    return $user ? get_avatar_url($user)  : null;
}
