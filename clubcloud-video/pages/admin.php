<div class="wrap">
    <h1>ClubCloud Video Short Code Settings</h1>

    <br/><br/>

    <h2>App ShortCode</h2>
    <p>You can use the following
        <a href="https://support.wordpress.com/shortcodes/" target="_blank">ShortCodes</a> to add the ClubCloud video app to a page.
    </p>

    <h3>ClubCloud App</h3>
    <p>This shows the video app</p>
    <code>
        [
            <?= ClubCloudVideoPlugin_AppShortcode::SHORTCODE_TAGS[0]; ?>
            name="ClubCloud.tech"
            map="clubcloud"
            lobby=true
            admin=true
        ]
    </code><br />
    <table>
        <thead>
            <tr>
                <th>Param</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>name</th>
                <td>The name of the room</td>
                <td>Required</td>
            </tr>
            <tr>
                <th>map</th>
                <td>The id of the map to display</td>
                <td>Required</td>
            </tr>
            <tr>
                <th>admin</th>
                <td>Whether the user should be an admin</td>
                <td>Optional: default=false</td>
            </tr>
            <tr>
                <th>loading-text</th>
                <td>Test to show while the app is loading</td>
                <td>Optional: default="Loading..."</td>
            </tr>
        </tbody>

        <tbody>
            <tr><th colspan="3">Non-admin settings</th></tr>

            <tr>
                <th>lobby</th>
                <td>Whether the lobby inside the video app should be enabled for non admin users</td>
                <td>Optional: default=false</td>
            </tr>
        </tbody>

        <tbody>
            <tr><th colspan="3">Non-admin settings</th></tr>

            <tr>
                <th>reception</th>
                <td>Whether the reception before entering the app should be enabled</td>
                <td>Optional: default=false</td>
            </tr>
            <tr>
                <th>reception-id</th>
                <td>The id of the reception to use</td>
                <td>Optional: default="office"</td>
            </tr>
            <tr>
                <th>reception-video</th>
                <td>A link to a video to play in the reception. Will only work if the selected reception supports video</td>
                <td>Optional: default=(Use reception setting)</td>
            </tr>
            <tr>
                <th>floorplan</th>
                <td>Whether the floorplan should be shown</td>
                <td>Optional: default=false</td>
            </tr>
        </tbody>
    </table>
    <br />

    <h3>ClubCloud Reception Widget</h3>
    <p>This shows the number of people currently waiting in a room</p>
    <code>
        [
            <?= ClubCloudVideoPlugin_MonitorShortcode::SHORTCODE_TAGS[0]; ?>
            name="ClubCloud.tech"
            text-empty="Nobody is currently waiting"
            text-single="One person is waiting in reception"
            text-plural="{{count}} people are waiting in reception"
        ]
    </code><br/>
    <table>
        <thead>
        <tr>
            <th>Param</th>
            <th>Details</th>
            <th>Required</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>name</th>
            <td>The name of the room</td>
            <td>Required</td>
        </tr>
        <tr>
            <th>text-empty</th>
            <td>The text to show when nobody is waiting</td>
            <td>Optional: default="Nobody is currently waiting"</td>
        </tr>
        <tr>
            <th>text-single</th>
            <td>The text to show when a single person is waiting</td>
            <td>Optional: default="One person is waiting in reception"</td>
        </tr>
        <tr>
            <th>text-plural</th>
            <td>The text to show when a more than one person is waiting. "{{count}}" will be substituted with the actual count</td>
            <td>Optional: default="{{count}} people are waiting in reception"</td>
        </tr>
        <tr>
            <th>loading-text</th>
            <td>The text to show while the widget is loading</td>
            <td>Optional: default="Loading..."</td>
        </tr>
        <tr>
            <th>type</th>
            <td>
                The type of count to show:
                <dl>
                    <dt>reception</dt>
                    <dd>The number of people waiting in reception</dd>

                    <dt>seated</dt>
                    <dd>The number of people currently seated</dd>

                    <dt>all</dt>
                    <dd>The total number of people, including reception, seated and non-seated admins</dd>
                </dl>

            </td>
            <td>Optional: default="reception"</td>
        </tr>
        </tbody>
    </table>
    <br/>


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
