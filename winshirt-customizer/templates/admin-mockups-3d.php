<div class="wrap winshirt-admin">
    <h1>Mockups 3D</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <?php $mockups = !empty($settings['mockups3d']) ? $settings['mockups3d'] : [['name' => '', 'file' => '', 'texture' => '']]; ?>
        <table class="widefat winshirt-table" data-repeatable="mockups3d">
            <thead><tr><th>Nom</th><th>Fichier 3D (GLB/OBJ)</th><th>Texture défaut</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($mockups as $index => $mockup) : ?>
                    <tr>
                        <td><input type="text" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($mockup['name'] ?? ''); ?>"></td>
                        <td class="winshirt-3d-file">
                            <input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][file]" value="<?php echo esc_url($mockup['file'] ?? ''); ?>" placeholder="URL GLB/GLTF/OBJ">
                            <button class="button winshirt-upload-3d" type="button">Choisir dans la médiathèque</button>
                        </td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][texture]" value="<?php echo esc_url($mockup['texture'] ?? ''); ?>" placeholder="URL PNG"></td>
                        <td><button class="button winshirt-remove">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" data-add-row="mockups3d">Ajouter un mockup</button></p>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
