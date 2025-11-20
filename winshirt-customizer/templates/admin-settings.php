<div class="wrap winshirt-admin">
    <h1>Options globales</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <table class="form-table">
            <tr>
                <th>Couleur du thème</th>
                <td><input type="color" name="winshirt_customizer_settings[theme_color]" value="<?php echo esc_attr($settings['theme_color']); ?>"></td>
            </tr>
            <tr>
                <th>DPI export</th>
                <td><input type="number" min="72" name="winshirt_customizer_settings[dpi]" value="<?php echo esc_attr($settings['dpi']); ?>"></td>
            </tr>
            <tr>
                <th>Activer IA</th>
                <td><input type="checkbox" name="winshirt_customizer_settings[enable_ai]" <?php checked($settings['enable_ai']); ?>></td>
            </tr>
            <tr>
                <th>Activer QR</th>
                <td><input type="checkbox" name="winshirt_customizer_settings[enable_qr]" <?php checked($settings['enable_qr']); ?>></td>
            </tr>
            <tr>
                <th>Mode 2D fallback</th>
                <td><input type="checkbox" name="winshirt_customizer_settings[fallback_2d]" <?php checked($settings['fallback_2d']); ?>></td>
            </tr>
        </table>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
