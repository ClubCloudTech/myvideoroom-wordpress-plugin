<div class="wrap">
    <h1>ClubCloud Video Short Code Settings</h1>

    <br/><br/>

    <h2>ShortCode</h2>
    <p>You can use the following
        <a href="https://support.wordpress.com/shortcodes/" target="_blank">ShortCode</a> to create a button to start a new Meeting.
    </p>
    <p><code>[<?= ClubCloudVideoPlugin_Shortcode::SHORTCODE_TAG; ?>]</code></p>
    <p><b>Example</b>..</p>
    <code>[<?= ClubCloudVideoPlugin_Shortcode::SHORTCODE_TAG; ?> name="ClubCloud.tech" map="clubcloud" lobby=true admin=true ]</code>
    <br/><br/>
    <h2>Settings</h2>
    <form method="post" action="options.php">
		<?php

		settings_fields( ClubCloudVideoPlugin::PLUGIN_NAMESPACE );

		?>

        <fieldset>
            <table class="form-table" role="presentation">
                <tbody>

				<?php
				$settings = [
					ClubCloudVideoPlugin::SETTING_VIDEO_SERVER_URL => [
						'enabled'     => true,
						'title'       => 'ClubCloud Video URL',
						'placeholder' => 'e.g. https://meet.domain.tld/'
					],
					ClubCloudVideoPlugin::SETTING_ROOM_SERVER_URL  => [
						'enabled'     => true,
						'title'       => 'ClubCloud Room Manager URL',
						'placeholder' => 'e.g. https://state.domain.tld/'
					],
					ClubCloudVideoPlugin::SETTING_APP_SERVER_URL   => [
						'enabled'     => true,
						'title'       => 'ClubCloud App URL',
						'placeholder' => 'e.g. https://app.domain.tld/'
					],
					ClubCloudVideoPlugin::SETTING_PRIVATE_KEY    => [
						'enabled'     => true,
						'title'       => 'ClubCloud Private Key',
						'placeholder' => '(Provided by ClubCloud)',
                        'type'        => 'textarea'
					],

				];

				foreach ( $settings as $value => $setting ) {
					if ( $setting['enabled'] ) {
						?>
                        <tr>
                            <th scope="row">
                                <label for="<?= $value; ?>"><?= $setting['title']; ?>
                                    <br/>[<?= $value; ?>]</label>
                            </th>
                            <td>
                                <?php

                                switch($setting['type']) {
                                    case 'textarea':
                                        ?>
                                            <textarea
                                                name="<?= $value; ?>"
                                                id="<?= $value; ?>"
                                                placeholder="<?= $setting['placeholder']; ?>"
                                            ><?= get_option( $value ) ?></textarea>
                                        <?php
                                        break;
                                    default:
                                        ?>
                                        <input
                                                type="text"
                                                name="<?= $value; ?>"
                                                value="<?= get_option( $value ); ?>"
                                                placeholder="<?= $setting['placeholder']; ?>"
                                                id="<?= $value; ?>"
                                                size="100"
                                        />
                                        <?php
                                }

                                ?>

                            </td>
                        </tr>
						<?php
					}
				}
				?>
                </tbody>
            </table>
        </fieldset>

        <fieldset>
            <table class="form-table" role="presentation">
                <tbody>
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
