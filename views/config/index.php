<form method="post" action="<?= $controller->link_for('config/save_general_config', $marketplace_id) ?>">
    <table>
        <tbody>
            <tr>
                <td>
                    Name for comodity (singular)
                </td>
                <td>
                    <input type="text" name="comodity_name" value="<?= htmlReady($comodity_name) ?>">
                </td>
            </tr>
            <tr>
                <td>
                    Name for comodity (plural)
                </td>
                <td>
                    <input type="text" name="comodity_name_plural" value="<?= htmlReady($comodity_name_plural) ?>">
                </td>
            </tr>
            </tr>
            <tr>
                <td>
                    Name of marketplace
                </td>
                <td>
                    <input type="text" name="marketplace_name" value="<?= htmlReady($marketplace_name) ?>">
                </td>
            </tr>
            <tr>
                <td>
                    Enabled
                </td>
                <td>
                    <input type="checkbox" name="enabled" <?php echo ($enabled == 1 ? 'checked' : ''); ?>>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Save">
</form>