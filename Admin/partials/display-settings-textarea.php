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
    <span style="float:right;">
            <button  onclick="insertAtCaret(\'textareaid\',\'screen_name\');return false;" class="thickbox button" title="Username that Published Tweet">Username</button>
            <button  onclick="insertAtCaret(\'textareaid\',\'id\');return false;" class="thickbox button" title="ID of User that Published Tweet">User ID</button>
            <button  onclick="insertAtCaret(\'textareaid\',\'status\');return false;" class="thickbox button" title="Tweet Text">Status</button>
            <button  onclick="insertAtCaret(\'textareaid\',\'image\');return false;" class="thickbox button" title="Profile Image of User">Image</button>
            <button  onclick="insertAtCaret(\'textareaid\',\'url\');return false;" class="thickbox button" title="Twitter URL of Tweet">Url</button>
            <button  onclick="insertAtCaret(\'textareaid\',\'created_at\');return false;" class="thickbox button" title="Date when Published">Date</button>
    </span>
</span><br/>
<?php
printf(
        '<textarea class="large-text" cols="15" rows="15" name="display_settings_option_name[test2_1]" id="test2_1">%s</textarea>',
        isset( $this->display_settings_options['test2_1'] ) ? esc_attr( $this->display_settings_options['test2_1']) : ''
);