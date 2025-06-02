<form action="options.php" method="post" novalidate="novalidate">
    <?php settings_fields('hobc_settings_group'); ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Player per page</th>
                <td>
                    <input name="player_per_page" type="number" class="regular-text" value="<?php echo esc_attr(get_option('player_per_page', 10)); ?>">
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>