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
		<?php settings_fields( ClubCloudVideoPlugin::PLUGIN_NAMESPACE ); ?>

        <fieldset>
            <table class="form-table" role="presentation">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="<?= ClubCloudVideoPlugin::SETTING_VIDEO_SERVER; ?>">
                            ClubCloud Video URL<br/>
                            [<?= ClubCloudVideoPlugin::SETTING_VIDEO_SERVER; ?>]
                        </label>
                    </th>
                    <td>
                        <input
                                type="text"
                                name="<?= ClubCloudVideoPlugin::SETTING_VIDEO_SERVER; ?>"
                                value="<?= get_option( ClubCloudVideoPlugin::SETTING_VIDEO_SERVER ); ?>"
                                placeholder="e.g. abada.clubcloud.tech"
                                id="<?= ClubCloudVideoPlugin::SETTING_VIDEO_SERVER; ?>"
                                size="100"
                        />
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?= ClubCloudVideoPlugin::SETTING_PRIVATE_KEY; ?>">
                            ClubCloud Private Key<br/>
                            [<?= ClubCloudVideoPlugin::SETTING_PRIVATE_KEY; ?>]
                        </label>
                    </th>
                    <td>
                            <textarea
                                    name="<?= ClubCloudVideoPlugin::SETTING_PRIVATE_KEY; ?>"
                                    id="<?= ClubCloudVideoPlugin::SETTING_PRIVATE_KEY; ?>"
                                    placeholder="(Provided by ClubCloud)"
                            ><?= get_option( ClubCloudVideoPlugin::SETTING_PRIVATE_KEY ) ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>

		<?php submit_button(); ?>
    </form>

</div>
