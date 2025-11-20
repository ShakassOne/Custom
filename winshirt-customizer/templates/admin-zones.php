<div class="wrap winshirt-admin">
    <h1>Zones d'impression</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <?php $zones = !empty($settings['zones']) ? $settings['zones'] : [['name' => '', 'width' => '', 'height' => '', 'pos_x' => '', 'pos_y' => '', 'face' => 'front', 'price' => '', 'order' => '', 'active' => true]]; ?>
        <table class="widefat winshirt-table" data-repeatable="zones">
            <thead><tr><th>Nom</th><th>Largeur (cm)</th><th>Hauteur (cm)</th><th>X %</th><th>Y %</th><th>Face</th><th>Prix</th><th>Ordre</th><th>Active</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($zones as $index => $zone) : ?>
                    <tr>
                        <td><input type="text" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($zone['name']); ?>"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][width]" value="<?php echo esc_attr($zone['width']); ?>"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][height]" value="<?php echo esc_attr($zone['height']); ?>"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][pos_x]" value="<?php echo esc_attr($zone['pos_x']); ?>"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][pos_y]" value="<?php echo esc_attr($zone['pos_y']); ?>"></td>
                        <td>
                            <select name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][face]">
                                <option value="front" <?php selected($zone['face'], 'front'); ?>>Recto</option>
                                <option value="back" <?php selected($zone['face'], 'back'); ?>>Verso</option>
                                <option value="sleeve_left" <?php selected($zone['face'], 'sleeve_left'); ?>>Manche G</option>
                                <option value="sleeve_right" <?php selected($zone['face'], 'sleeve_right'); ?>>Manche D</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][price]" value="<?php echo esc_attr($zone['price']); ?>"></td>
                        <td><input type="number" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][order]" value="<?php echo esc_attr($zone['order']); ?>"></td>
                        <td><input type="checkbox" name="winshirt_customizer_settings[zones][<?php echo esc_attr($index); ?>][active]" <?php checked($zone['active']); ?>></td>
                        <td><button class="button winshirt-remove">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" data-add-row="zones">Ajouter une zone</button></p>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
