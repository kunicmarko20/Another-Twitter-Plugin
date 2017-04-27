<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>
<span style="line-height: 2;"><strong>Atributes from Twitter:</strong>
    <span class="twitter-attributes">
            <button type="button" data-name="username" class="thickbox button" title="Username that Published Tweet">Username</button>
            <button type="button" data-name="id" class="thickbox button" title="ID of User that Published Tweet">User ID</button>
            <button type="button" data-name="status" class="thickbox button" title="Tweet Text">Status</button>
            <button type="button" data-name="image" class="thickbox button" title="Profile Image of User">Image</button>
            <button type="button" data-name="url" class="thickbox button" title="Twitter URL of Tweet">Url</button>
            <button type="button" data-name="date" class="thickbox button" title="Date when Published">Date</button>
    </span>
</span><br/>
<?php
printf(
        '<textarea class="large-text" cols="15" rows="15" name="display_settings_option_name[dt_atp_style]" id="dt_atp_style">%s</textarea>',
        isset( $this->display_settings_options['dt_atp_style'] ) ? esc_attr( $this->display_settings_options['dt_atp_style']) : ''
);