<?php
/**
 * Created by PhpStorm.
 * User: markokunic
 * Date: 4/27/17
 * Time: 11:48 AM
 */
?>
<div id="loader"></div>
<table class="form-table">
    <tbody>
    <form action="<?php echo dt_atp_plugin_url; ?>wp-admin/admin-ajax.php?action=dt_atp_get_new_tweets" method="post">
        <tr>
            <th scope="row"><strong>Last Update</strong></th>
            <td>
                <label><?php echo $this->dt_atp_last_update_time(); ?></label>
            </td>
            <td>
                <?php submit_button("Update Now", "secondary","btnUpdate",false ); ?>
            </td>
        </tr>
    </form>
        <tr>
            <th scope="row"><strong>Shortcode</strong></th>
            <td>
                <p>[dt_atp_twitter]</p>
            </td>
        </tr>
    <form action="<?php echo dt_atp_plugin_url; ?>wp-admin/admin-ajax.php?action=dt_atp_reset_tweets" method="post">
        <tr>
            <th scope="row"><strong>Reset</strong></th>
            <td>
                <?php submit_button("Reset", "secondary", "btnReset", false); ?>
            </td>
        </tr>
    </form>
    </tbody>
</table>