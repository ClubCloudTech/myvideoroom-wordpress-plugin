<?php

/**
 * @link              https://clubcloud.tech
 * @since             0.1
 * @package           clubcloud-video
 *
 * @wordpress-plugin
 * Plugin Name: Clubcloud Video Plugin
 * Plugin URI: https://clubcloud.tech
 * Description: Allows you to use a shortcode to enable a Clubcloud meeting into your webpage.
 * Version: 0.1
 * Author: Clubcloud Awesome Developers
 * Author URI: https://clubcloud.tech/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: clubcloud-video
 **/

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Install Clubcloud Databases and Plugin
global $clubcloud_db_version;
$clubcloud_db_version = '1.0';
// This section sets up the plugin and hooks to the WP Instance.

register_activation_hook( __FILE__, 'clubcloud_install' );
add_action( 'wp_ajax_cc_get_connectiondata', 'cc_get_connectiondata' );
add_action( 'wp_ajax_nopriv_cc_get_connectiondata', 'cc_get_connectiondata' );
add_action( 'wp_ajax_set_connectdata', 'set_connectdata', 10, 5 );
add_action( 'wp_ajax_nopriv_set_connectdata', 'set_connectdata', 10, 5 );
add_action( 'wp_enqueue_scripts', 'cc_enqueue_ui_scripts' );
add_action( 'wp_ajax_cc_set_route', 'cc_set_route' );
add_action( 'wp_ajax_nopriv_cc_clearcart', 'cc_clearcart' );
add_action( 'wp_ajax_cc_clearcart', 'cc_clearcart' );
add_action( 'wp_ajax_cc_getcart', 'cc_getcart' );
add_action( 'wp_ajax_cc_setcart', 'cc_setcart' );
add_action( 'wp_ajax_nopriv_cc_setcart', 'cc_setcart' );
add_action( 'wp_ajax_nopriv_cc_call_queue', 'cc_call_queue' );
add_action( 'wp_ajax_cc_call_queue', 'cc_call_queue' );
add_action( 'wp_ajax_cc_get_call_queue', 'cc_get_call_queue' );
add_action( 'wp_ajax_cc_set_route_in', 'cc_set_route_in' );
//Register the Clubcloud Admin Menu

add_action( 'admin_menu', 'clubcloud_setup_menu' );
add_action( 'wp_enqueue_scripts', 'clubcloud_init' );
add_filter( 'cron_schedules', 'clubcloud_add_cron_interval' );
add_action( 'bl_clubcloud_dbmaint', 'bl_clubcloud_dbmaint_exec' );

//This executes the Setup and enablest the Admin Menu in the HTML below
function clubcloud_setup_menu() {
	add_menu_page( 'Clubcloud Video Settings', 'Clubcloud Video Settings', 'manage_options', 'clubcloud-Video-settings', 'clubcloud_admin_page_init', 'dashicons-format-chat' );
	add_action( 'admin_init', 'clubcloud_register_settings' );
}

// Create the Clubcloud wordpress settings

function clubcloud_register_settings() {
	register_setting( 'clubcloud', 'clubcloud_username_pull' );
	register_setting( 'clubcloud', 'clubcloud_email_pull' );
	register_setting( 'clubcloud', 'clubcloud_display_chat_above_footer' );
	register_setting( 'clubcloud', 'clubcloud_url' );
	register_setting( 'clubcloud', 'clubcloud_web_token_key' );
	register_setting( 'clubcloud', 'clubcloud_web_token_guest_key' );
	register_setting( 'clubcloud', 'clubcloud_table_count' );
}

//Initialise the Plugin and setup the Admin Menu.

function clubcloud_admin_page_init() {
	?>
    <div class="wrap">
        <h1>Clubcloud Video Short Code Settings</h1>

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
                    <th scope="row">Clubcloud Server URL</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Clubcloud Server URL</span></legend>
                            <label for="clubcloud_url">
                                <input type="text" name="clubcloud_url" value="<?php echo get_option( 'clubcloud_url' ) ?>" placeholder="eg. https://meet.domain.tld/" id="clubcloud_url" size="100" class=""/>
                            </label>

                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Clubcloud Moderator Token Key</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span>Clubcloud Moderator Token Key (Provided by Clubcloud)</span></legend>
                            <label for="clubcloud_web_token_key">
                                <input type="text" name="clubcloud_web_token_key" value="<?php echo get_option( 'clubcloud_web_token_key' ) ?>" placeholder="(Provided by Clubcloud)" id="clubcloud_web_token_key" size="100" class=""/>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Clubcloud Guest Token Key</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span>Clubcloud Guest Token Key (Provided by Clubcloud)</span></legend>
                            <label for="clubcloud_web_token_guest_key">
                                <input type="text" name="clubcloud_web_token_guest_key" value="<?php echo get_option( 'clubcloud_web_token_guest_key' ) ?>" placeholder="(Provided by Clubcloud)" id="clubcloud_web_token_guest_key" size="100" class=""/>
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
                a            <label for="clubcloud_email_pull">
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

// The install functions
function clubcloud_install() {
	global $wpdb;
	$table_name      = $wpdb->prefix . "cc_live_call_data";
	$table_name_q    = $wpdb->prefix . "cc_live_queue";
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		lastactive int NOT NULL,
		username_jid text NOT NULL,
		activemeeting text NOT NULL,
        moveto text,
        cartdata text,
        carriermessage text,
        PRIMARY KEY  (id));
        CREATE TABLE $table_name_q (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        callkey text NOT NULL,
        lastactive int NOT NULL,
        guestname text NOT NULL,
        moved text,
        PRIMARY KEY  (id));
        ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'clubcloud_db_version', $clubcloud_db_version );


}

// This enqueues the jQuery UI componenets require for drag and drop as well as the jquery popups
function cc_enqueue_ui_scripts() {
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
}


// This runs id the shortcde is loaded on the page.

function clubcloud_init() {
	wp_register_script( 'cc-ui-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js', [ 'jquery' ], null, true );
	wp_register_script( 'join-script', plugins_url( 'js/clubcloud-video.js', __FILE__ ), [ 'jquery' ], null, true );
	wp_register_script( 'cc_lightbox', plugins_url( 'js/videoLightning.js', __FILE__ ), [ 'jquery' ], null, true );
	wp_register_script( 'cc_reception', plugins_url( 'js/clubcloud-reception.js', __FILE__ ), [ 'jquery' ], null, true );
	add_action( 'wp_enqueue_scripts', 'enqueue_scripts_front_end' );
	$load_scripts = true;
//above and below sets up the scripts and java script files required to run the browser client end these are registered and executed in order please dont change this unless it is tested properly.

	if ( $load_scripts ) {
		$clubcloud_url = get_option( 'clubcloud_url' );
		if ( ! wp_next_scheduled( 'bl_clubcloud_dbmaint' ) ) {
			wp_schedule_event( time(), 'sixty_seconds', 'bl_clubcloud_dbmaint' );
		}

	}
}


// This sets up the clubvideo shortcode

add_shortcode( 'clubvideo', 'clubcloud_shortcode' );
function clubcloud_shortcode( $atts ) {
	$atts = shortcode_atts( [
		//To ask... do room names with spaces do what?
		'cc_event_id'       => 'MyEvent',
		'cc_plan_id'        => 'hall1',
		'cc_enable_lobby'   => false,
		'cc_enable_cart'    => false,
		'cc_is_reception'   => false,
		'cc_receptionimage' => "",
		'width'             => '100%',
		'height'            => '395px',
		'auth'              => false,
		'cc_is_event'       => false,
	], $atts, 'cc_clubcloud' );
//the settings for the shortcode
// the following sets up the user environments required as follows:
	if ( $atts['cc_is_reception'] == true ) {
		wp_enqueue_script( 'cc_lightbox' );
	}

	$cc_cartenable = $atts['cc_enable_cart'];

	if ( is_user_logged_in() == true && $atts['auth'] == true ) {             // 1 Auth = true is set in the shortcode and the user is logged in
		$enabledrag = true;
		$user_id    = get_current_user_id();
		$user_info  = get_userdata( $user_id );
		$encdata    = [
			'username' => $user_info->user_login,
			'usertype' => 'Owner',
		];
	} elseif ( $atts['cc_is_event'] == true ) {
		if ( is_user_logged_in() == true && $atts['auth'] == false ) {         // 2 Auth = false(this is on a guest page) and the user is logged in
			$user_id   = get_current_user_id();
			$user_info = get_userdata( $user_id );
			$encdata   = [
				'username' => $user_info->user_login,
				'usertype' => 'Owner',
			];

			$enabledrag = false;
		} else {
			$encdata = [                                               // 3 the user s a guest user
				'username' => 'GuestUser',
				'usertype' => 'Owner',
			];
			//cc_encidenc is the function below that encrypts the profile data for the moment we are only using username and user type -- Owner/Moderator -- Guest
			$enabledrag = false;

		}
	} else {
		$encdata    = [                                               // 3 the user s a guest user
			'username' => 'GuestUser',
			'usertype' => 'Guest',
		];
		$enabledrag = false;
	}
	$userencdata   = cc_encidenc( $encdata, 'enc', 86400 );
	$cc_uploadsdir = wp_get_upload_dir();
	$cc_floorplan  = 'https://manage.clubcloud.tech/rooms/';                                   // sets the url for the map files
	wp_enqueue_script( 'join-script' );                                                         // this enqueues the scripts registered above
	wp_enqueue_script( 'cc-ui-script' );

	wp_enqueue_style( "cc-table-plan", plugins_url( 'css/table-plan.css', __FILE__ ) );
	wp_enqueue_style( "cc-ui-style", 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css' );

	wp_enqueue_script( 'connect-js', esc_url( trailingslashit( get_option( 'clubcloud_url' ) ) ) . 'external_api.js', false );
	wp_localize_script( 'join-script', 'wpApiSettings', [                                  // this sets wpApiSettings.variable in the browser client for later use
		'ajax_url'    => admin_url( 'admin-ajax.php' ),
		'floorplan'   => $cc_floorplan,
		'dragenabled' => $enabledrag,
		'envelope'    => $userencdata,
		'enablecart'  => $cc_cartenable
	] );

	if ( isset( $_GET["cc_username"] ) ) {
		$atts['cc_event_id'] = $_GET["cc_username"];                                              // this sets the event id to a username required or personal spaces when a user joins from /meet
	}

	if ( $atts['cc_event_id'] == 'username' ) {
		$atts['cc_event_id'] = $user_info->user_login;                                         // sets the event id = the logged in user when 'username' is used in the short code
	}

	$hashArguments = [
		'secret'       => get_option( 'clubcloud_web_token_key' ),
		'lobbyEnabled' => $atts['cc_enable_lobby'],
		'planId'       => $atts['cc_plan_id'],
		'eventId'      => $atts['cc_event_id']
	];
	$password      = hash( 'sha256', json_encode( $hashArguments ) );

	if ( $atts['cc_enable_lobby'] ) {
		$atts['cc_event_id'] .= '-' . $password;
	}

	return '
        <div
            id="clubcloud_shortcode"
            data-auth="' . $atts['auth'] . '"
            data-cc_is_reception="' . $atts['cc_is_reception'] . '"
            data-cc_receptionimage="' . $atts['cc_receptionimage'] . '"
            data-cc_enable_lobby="' . $atts['cc_enable_lobby'] . '"
            data-cc_plan_id="' . $atts['cc_plan_id'] . '"
            data-cc_event_id="' . $atts['cc_event_id'] . '"
            data-password="' . $password . '"
            width="' . $atts['width'] . '"
            height="' . $atts['height'] . '"
        ></div>';
} // above returnes the shortcode for the page with all the correct variable set in both the browser as well, as the server

function getAvatar( $user ) {
	if ( function_exists( bp_core_fetch_avatar ) ) {
		$url = bp_core_fetch_avatar( [
			'item_id' => $user->Id,
			'html'    => false,
			'type'    => 'full'
		] );
	} else {
		$url = 'https://www.gravatar.com/avatar/' . md5( strtolower( trim( $user->user_email ) ) );
	}

	return $url;
}

// the following code is called by the browser to connect the user to a video conf
function set_connectdata() {
	try {
		$cc_room_id      = $_POST['table_id'];                                               // the actual id of the conf either set by 'username', manually, or set by code eexcuted on freds switch
		$cc_auth         = $_POST['auth'];                                                      // whther the Auth switch on the shortcode is set or not
		$cc_token        = $_POST['temptoken'];                                                // If a user is being moved from one user conf to another this is passed as security and validated later in the code (used if a guest is to be made a moderator)
		$cc_jid_coll     = $_POST['currentjid'];                                            // this is the conferance id of the user as a hash generated by the video platform for the user and the conferance id formatted id@confid
		$cc_userlogin    = cc_encidenc( $_POST['envelope'], 'dec', 86400 );                    // this is the decrypted profile token of the user to get username cc_encidenc is described in detail below
		$cc_video_domain = parse_url( esc_url( trailingslashit( get_option( 'clubcloud_url' ) ) ) );     //parces the video server url from the settings admin page
		if ( is_object( $cc_userlogin ) == true ) {                                                         //validates the decrypted login information if returned false the toke is stale or failed decryption
			$cc_userdata = $cc_userlogin;
			$cc_username = $cc_userdata->username;
			$cc_usertype = $cc_userdata->usertype;
			if ( $cc_usertype == 'Owner' ) {                                                                    // processing starts for the meeting owners
				$user           = get_user_by( 'login', $cc_username );                                                // as we got the username from the token we get the wp user by using the login
				$connect_data[] = [                                                                    // sets up the connection data for the browser to connect to the conf
					'displayname' => $cc_username,
					'jwt'         => cc_get_jwt( $cc_room_id, $cc_username ),
					'videodomain' => $cc_video_domain['host'],
					'mailaddy'    => $user->user_email,
					'myavatar'    => getAvatar( $user )
				];
				$cc_flag        = 'Owner';
			} else {
				if ( $cc_username == 'GuestUser' ) {                                                              // sets the connection data for a guest
					$connect_data[] = [
						'displayname' => 'GuestUser',
						'jwt'         => null,
						'videodomain' => $cc_video_domain['host']
					];
				} else {
					$user           = get_user_by( 'login', $cc_username );                                            // sets the connection data for guest user that is logged in.
					$connect_data[] = [
						'displayname' => $cc_username,
						'videodomain' => $cc_video_domain['host'],
						'jwt'         => null,
						'mailaddy'    => $user->user_email,
						'myavatar'    => getAvatar( $user )
					];
				}
			}
			if ( $cc_token == 'empty' ) {                                         // this gets a place holder set in the client where it is the first connection made
				wp_send_json( $connect_data );                                                                // sends the user connectin data
			} elseif ( cc_encidenc( $cc_token, 'dec', 600 ) == $cc_jid_coll ) {                                    // this validates  the temporary key issued below if the user is being moved with moderator rights
				$connect_data[0]['jwt'] = cc_get_jwt( $cc_room_id, $cc_username );                         // this issues a java web token for the guest user if they are to be a moderator
				wp_send_json( $connect_data );
			} elseif ( cc_encidenc( $cc_token, 'dec', 600 ) == $cc_room_id ) {
				$connect_data[0]['jwt'] = cc_get_jwt( $cc_room_id, $cc_username );
				wp_send_json( $connect_data );
			} else {
				wp_send_json( $connect_data );
			}


		}

	} catch ( Exception $e ) {
		var_dump( $e );
	}

	wp_die();

}

// this function issues a java web token
function cc_get_jwt( $cc_roomname, $cc_user ) {
	$clubcloud_url = parse_url( get_option( 'clubcloud_url' ) );                          // this gets the video url defined in admin settings
	$client_token  = ( get_option( 'clubcloud_web_token_key' ) );                          // this collects the toke string in the admin settings used for all encryption and decryption
	$cc_roomname   = strtolower( $cc_roomname );                                          // sets the roomname/conf to lower case for the token to be issued

	$header             = json_encode( [ 'typ' => 'JWT', 'alg' => 'HS256', 'kid' => $clubcloud_url['host'] ] );
	$payload            = json_encode( [
		'user_id' => $cc_user,
		"iss"     => $clubcloud_url['host'],
		"iat"     => idate( 'U' ),
		"exp"     => idate( 'U' ) + 3000,
		"aud"     => $clubcloud_url['host'],
		"sub"     => $clubcloud_url['host'],
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

// the following function is called on the heartbeat of the client -- it collects the information from the video platform and delivers any data required by the client to the client
function cc_get_connectiondata() {

	try {
		$cc_received_data  = $_POST['table_list'];                                           //collects the information from the client (list of tables on the age and if the client is connected to a conf the id as above)
		$cc_connection_id  = $_POST['connection_id'];
		$cc_envelope_cover = cc_encidenc( $_POST['envelope'], 'dec', 86000 );                   // decrypts the user envelope/ profile data

		if ( $cc_envelope_cover !== false ) {                                                   // validaes the user profile before continueing

			if ( $cc_connection_id != 'Starting' ) {                                                         // checks to see if this is the first connection from the user on the page
				$cc_moveto = cc_route( $cc_connection_id );                                                // this checks to see if the call has been queued for routing
				if ( $cc_moveto != false ) {                                                                 // routes/moves the call if required
					wp_send_json( $cc_moveto );
				}
			}
			$clubcloud_url = parse_url( get_option( 'clubcloud_url' ) );

			// If we didn't receive our data, don't send any back.
			if ( isset( $cc_received_data ) ) {
				//this gets the current ---live--- call data from the video platform -- the code is stored i the /usr/share/jitsi-meet/prosody-plugins/mod_muc_status.lua -- this file is also stored in development folder on sharepoint
				foreach ( $cc_received_data as $table_i ) {
					$allparticipants = [];
					$json            = file_get_contents( 'http://prosody.jitsi-meet.svc.cluster.local:5280/roomdata?room=' . $table_i . '@conference.' . $clubcloud_url['host'] );   // builds the json data to be sent to the client
					$table_data_i    = json_decode( $json );

					foreach ( $table_data_i as $participant ) {
						$user     = get_user_by( 'login', $participant->display_name );
						$basicjid = explode( '/', $participant->jid )[1] . '@' . $table_i;

						$allparticipants[] = [
							'RoomName'    => $table_i,
							'JiD'         => $basicjid,
							'DisplayName' => $participant->display_name,
							'Role'        => $participant->role,
							'AvatarUrl'   => getAvatar( $user ),
							'Participant' => $participant
						];
					}
					$myrooms[] = [
						'currentuser'  => $cc_connection_id,
						'RoomName'     => $table_i,
						'Participants' => $allparticipants
					];
				}
				$response = $myrooms;
			} else {
				$response = 'Failed';
			}
			wp_send_json( $myrooms );                                 //sends the current live data for the map to the client
		}
		echo 'Failed';
		wp_die();
	} catch ( Exception $e ) {
		var_dump( $e );
	}
}

// this disables the lobby for a moderator before they join a call the code is in the same file as above
function cc_lobbycheck( $room_id ) {
	$clubcloud_url = parse_url( get_option( 'clubcloud_url' ) );
	$json          = file_get_contents( 'http://' . $clubcloud_url['host'] . ':5280/cc_setlobby?room=' . $room_id . '@conference.' . $clubcloud_url['host'] );

	return;
}


//this is called from the client it lists all the users conected to the platform and where required sends the move user to a new conferance commands
function cc_route( $cc_current_call_id ) {
	//get the live call data from the database.
	global $wpdb;
	$cc_table_name      = $wpdb->prefix . 'cc_live_call_data';
	$cc_selected_user_q = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ".$cc_table_name." WHERE username_jid = %s', [
		$cc_current_call_id
	] ) );
	if ( $cc_selected_user_q != null ) {                                    //make sure we are actually looking for a valid user
		if ( $cc_selected_user_q->moveto == null ) {                              // check if the user is to me moved and then execute otherwise just update the timestamp
			$cc_dt_update = [
				'lastactive' => time()
			];
			$wpdb->update( $cc_table_name, $cc_dt_update, [ 'id' => $cc_selected_user_q->id ] );       //if the user is to be moved then queue to the database
		} else {
			$movedata = explode( ",", $cc_selected_user_q->moveto );                                   // the data is stored as confid , true or false 'event-table1,true' true means move the user as a mderator false as a guest
			$moveto   = $movedata[0];
			if ( $movedata[1] == 'true' ) {                                                             //check for guest or moderator
				$get_cc_authkey = cc_encidenc( $cc_current_call_id, 'enc', 86000 );                       // setup a temporary key for the user to pass back for access as a moderator
			} else {
				$get_cc_authkey = 'empty';                                                           // leave a place holder if the user is a guest
			}

			$moveto = [
				'move'      => $moveto,
				// send the data back to the heartbat function
				'temptoken' => $get_cc_authkey
			];

			return $moveto;
		}
	} else {
		$room_id             = explode( '@', $cc_current_call_id )[1];                                                 //if this is a new call -- write the data
		$cc_insert_call_data = [
			'username_jid'  => $cc_current_call_id,
			'lastactive'    => time(),
			'activemeeting' => $room_id
		];
		$wpdb->insert( $cc_table_name, $cc_insert_call_data, [ '%s', '%d', '%s' ] );
	}

	return false;
}

//this is the call routing function -- getts the data from the client/receptionist and writes to the db for moving a call
function cc_set_route() {
	global $wpdb;
	$cc_table_name = $wpdb->prefix . 'cc_live_call_data';
	$cc_move_room  = $_POST['table_id'];
	$cc_move_user  = $_POST['jidnumber'];
	$cc_auth_user  = $_POST['moderator_mv'];
	$cc_move_auth  = $cc_move_room . "," . $cc_auth_user;
	$cc_envelope   = cc_encidenc( $_POST['envelope'], 'dec', 86000 );
	$move_update   = [
		'moveto' => $cc_move_auth
	];
	$wpdb->update( $cc_table_name, $move_update, [ 'username_jid' => $cc_move_user ] );
	echo 'Success';
	wp_die();
}

// this sets up a data cleanup cron for 60 seconds
function clubcloud_add_cron_interval( $schedules ) {
	$schedules['sixty_seconds'] = [
		'interval' => 60,
		'display'  => esc_html__( 'Every 60 Seconds' ),
	];

	return $schedules;
}

function cc_set_route_in() {
	global $wpdb;
	$cc_table_name = $wpdb->prefix . 'cc_live_queue';
	$cc_move_room  = $_POST['table_id'];
	$cc_move_user  = $_POST['qidnumber'];
	$cc_auth_user  = $_POST['moderator_mv'];
	$cc_move_auth  = $cc_move_room . "," . $cc_auth_user;
	$cc_envelope   = cc_encidenc( $_POST['envelope'], 'dec', 86000 );
	$move_update   = [
		'moved' => $cc_move_auth
	];
	$wpdb->update( $cc_table_name, $move_update, [ 'id' => $cc_move_user ] );
	wp_send_json( $cc_move_auth );
//echo 'Success';
	wp_die();


}

//this the actual data cleanup operation the bl_ is required for worpress to access this function lease do not change
function bl_clubcloud_dbmaint_exec() {
	global $wpdb;
	$cc_table_name = $wpdb->prefix . 'cc_live_call_data';
	$timedel       = time() - 300;
	$lastactive    = time() - 300;
	$query         = $wpdb->prepare( "SELECT * FROM " . $cc_table_name . " WHERE lastactive < %s", [ $lastactive ] );
	$result        = $wpdb->get_results( $query, ARRAY_A );
	foreach ( $result as $li ) {
		$cc_del_line = [
			'id' => $li['id']

		];
		$wpdb->delete( $cc_table_name, $cc_del_line, [ '%d' ] );
	}
	$wpdb->delete( $cc_table_name, $cc_del_line, [ '%d' ] );
	$cc_table_name = $wpdb->prefix . 'cc_live_queue';
	$lastactive    = time() - 30;
	$query         = $wpdb->prepare( "SELECT * FROM " . $cc_table_name . " WHERE lastactive < %s", [ $lastactive ] );
	$result        = $wpdb->get_results( $query, ARRAY_A );
	foreach ( $result as $li ) {
		$cc_del_line = [
			'id' => $li['id']
		];
		$wpdb->delete( $cc_table_name, $cc_del_line, [ '%d' ] );
	}
}

// this is the ecryp/decrypt function it ---''REQUIRES''--- the following parameters $userjasondata a string to be encrypted/decrypted $cc_type 'enc' 'dec' encrypt or decrypt and a lifetime this is allways required but only used i decryption
function cc_encidenc( $userjasondata, $cc_type, $lifetime ) {        // the lifetime used in decryption (seconds) checks the issue time from then to now and returns true or false for valid / invalid
	$encryption_key = get_option( 'clubcloud_web_token_key' );
	if ( $cc_type == 'enc' ) {
		$jsoncode        = json_encode( [ 'timestamp' => time(), 'ecrypteddata' => $userjasondata ] );
		$crypted_cc_data = openssl_encrypt( $jsoncode, "AES-128-ECB", $encryption_key );

		return $crypted_cc_data;

	} else {
		$uncrypted_cc_data = openssl_decrypt( $userjasondata, "AES-128-ECB", $encryption_key );
		if ( $uncrypted_cc_data !== false ) {
			$normaldata = json_decode( $uncrypted_cc_data );
			$timdiff    = time() - $normaldata->timestamp;
			if ( $timdiff > $lifetime ) {
				return 'Failed';
			} else {
				$uncrypted_cc_data = $normaldata->ecrypteddata;
			}

			return $uncrypted_cc_data;
		}

		return 'Failed';

	}
}


// this code is not used but it may be usefull at a later date ----- validaes a JWT -- ours are validated on Jitsi
function cc_validate_token( $jwt ) {
	$signature = ( get_option( 'clubcloud_web_token_key' ) );
	// split the jwt
	$tokenParts         = explode( '.', $jwt );
	$header             = base64_decode( $tokenParts[0] );
	$payload            = base64_decode( $tokenParts[1] );
	$signature_provided = $tokenParts[2];

	// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
	$expiration       = json_decode( $payload )->exp;
	$is_token_expired = ( $expiration - time() ) < 0;

	// build a signature based on the header and payload using the secret
	$base64_url_header    = base64url_encode( $header );
	$base64_url_payload   = base64url_encode( $payload );
	$signature            = hash_hmac( 'SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true );
	$base64_url_signature = base64url_encode( $signature );

	// verify it matches the signature provided in the jwt
	$is_signature_valid = ( $base64_url_signature === $signature_provided );

	if ( $is_token_expired || ! $is_signature_valid ) {
		return false;
	} else {
		return true;
	}
}

function cc_clearcart() {
	if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
		wc()->cart->empty_cart();

		// echo 'Success';
		die;
	}
}

function cc_getcart() {
	if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
		$proddata = [];
		$passdata = wc()->cart->get_cart_contents();
		foreach ( $passdata as $selectedprod ) {
			$proddata[] = [
				'product_id'   => $selectedprod['product_id'],
				'variation_id' => $selectedprod['variation_id'],
				'quantity'     => $selectedprod['quantity']
			];
		}
		wp_send_json( $proddata );
		wp_die();
	}
}

function cc_setcart() {

	if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
		$passdata = $_POST['data'];
		wc()->cart->empty_cart();
		foreach ( $passdata as $data ) {
			wc()->cart->add_to_cart( $data['product_id'], $data['quantity'], $data['variation_id'] );
		}
		echo 'Success';
		wp_die();
	}

}

function cc_call_queue() {
	$dbid          = $_POST['qid'];
	$cc_callkey    = $_POST['event_id'];
	$cc_guest_name = $_POST['cc_guest_name'];
	global $wpdb;
	$cc_table_name = $wpdb->prefix . 'cc_live_queue';
	$qdata         = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $cc_table_name . " WHERE id = %s", [ $dbid ] ) );
	if ( $qdata != null ) {
		if ( $qdata->moved === null ) {                              // check if the user is to me moved and then execute otherwise just update the timestamp
			$cc_dt_update = [
				'lastactive' => time()
			];
			$wpdb->update( $cc_table_name, $cc_dt_update, [ 'id' => $qdata->id ] );       //if the user is to be moved then queue to the database
			$query      = $wpdb->prepare( "SELECT * FROM " . $cc_table_name . " WHERE callkey = %s ORDER BY id ASC", [ $cc_callkey ] );
			$result     = $wpdb->get_results( $query, ARRAY_A );
			$key        = array_search( $dbid, array_column( $result, 'id' ) );
			$returndata = [
				'action' => 'update',
				'qid'    => $dbid,
				'qpos'   => $key
			];
			wp_send_json( $returndata );

		} else {
			//$movedata = $qdata->moved;
			$movedata = explode( ",", $qdata->moved );                                   // the data is stored as confid , true or false 'event-table1,true' true means move the user as a mderator false as a guest
			$moveto   = $movedata[0];
			if ( $movedata[1] == 'true' ) {                                                             //check for guest or moderator
				$get_cc_authkey = cc_encidenc( $moveto, 'enc', 86000 );                       // setup a temporary key for the user to pass back for access as a moderator
			} else {
				$get_cc_authkey = 'empty';                                                           // leave a place holder if the user is a guest
			}

			$moveto  = [
				'action'    => 'move',
				'move'      => $moveto,
				// send the data back to the heartbeat function
				'temptoken' => $get_cc_authkey
			];
			$deldata = [
				'id' => $dbid
			];
			$wpdb->delete( $cc_table_name, $deldata, [ '%d' ] );
			wp_send_json( $moveto );
		}

	} else {
		$lastactive          = time();
		$cc_insert_call_data = [
			'callkey'    => $cc_callkey,
			'lastactive' => $lastactive,
			'guestname'  => $cc_guest_name
		];
		$wpdb->insert( $cc_table_name, $cc_insert_call_data, [ '%s', '%d', '%s' ] );
		$returndata       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $cc_table_name . " WHERE lastactive = %s", [ $lastactive ] ) );
		$cc_return_update = [
			'action' => 'insert',
			'dbid'   => $returndata->id,
		];
		wp_send_json( $cc_return_update );
	}

	wp_die();
}

add_shortcode( 'club_reception', 'club_reception_shortcode' );
function club_reception_shortcode() {

	return '<div id="club_reception_shortcode"><h3>Call Queue</h3></div>';

}

function cc_get_call_queue() {
	$cc_call_q = $_POST['callkey'];
	global $wpdb;
	$cc_table_name = $wpdb->prefix . 'cc_live_queue';
	$query         = $wpdb->prepare( "SELECT id,guestname FROM " . $cc_table_name . " WHERE callkey = '%s'", [ $cc_call_q ] );
	$qdata         = $wpdb->get_results( $query, ARRAY_A );
	$returndata    = [];
	foreach ( $qdata as $ccitem ) {
		$returndata[] = [ 'guestqid' => $ccitem['id'], 'guestname' => $ccitem['guestname'] ];
	}
	wp_send_json( $returndata );
	wp_die();
}
