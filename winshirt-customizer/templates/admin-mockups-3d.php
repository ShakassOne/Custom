<div class="wrap winshirt-admin">
    <h1>Mockups 3D</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <?php $mockups = !empty($settings['mockups3d']) ? $settings['mockups3d'] : [['name' => '', 'front' => '', 'back' => '', 'sleeve_left' => '', 'sleeve_right' => '', 'texture' => '']]; ?>
        <table class="widefat winshirt-table" data-repeatable="mockups3d">
            <thead><tr><th>Nom</th><th>GLB/OBJ Recto</th><th>GLB/OBJ Verso</th><th>Manche G</th><th>Manche D</th><th>Texture défaut</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($mockups as $index => $mockup) : ?>
                    <tr>
                        <td><input type="text" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($mockup['name']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][front]" value="<?php echo esc_url($mockup['front']); ?>" placeholder="URL GLB/OBJ"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][back]" value="<?php echo esc_url($mockup['back']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][sleeve_left]" value="<?php echo esc_url($mockup['sleeve_left']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][sleeve_right]" value="<?php echo esc_url($mockup['sleeve_right']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][texture]" value="<?php echo esc_url($mockup['texture']); ?>" placeholder="URL PNG"></td>
                        <td><button class="button winshirt-remove">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" data-add-row="mockups3d">Ajouter un mockup</button></p>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
