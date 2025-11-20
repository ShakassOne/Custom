<div class="wrap winshirt-admin">
    <h1>Mockups 2D</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <?php $mockups = !empty($settings['mockups2d']) ? $settings['mockups2d'] : [['name' => '', 'front' => '', 'back' => '', 'sleeve_left' => '', 'sleeve_right' => '']]; ?>
        <table class="widefat winshirt-table" data-repeatable="mockups2d">
            <thead><tr><th>Nom</th><th>PNG Recto</th><th>PNG Verso</th><th>Manche G</th><th>Manche D</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($mockups as $index => $mockup) : ?>
                    <tr>
                        <td><input type="text" name="winshirt_customizer_settings[mockups2d][<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($mockup['name']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups2d][<?php echo esc_attr($index); ?>][front]" value="<?php echo esc_url($mockup['front']); ?>" placeholder="URL PNG"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups2d][<?php echo esc_attr($index); ?>][back]" value="<?php echo esc_url($mockup['back']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups2d][<?php echo esc_attr($index); ?>][sleeve_left]" value="<?php echo esc_url($mockup['sleeve_left']); ?>"></td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups2d][<?php echo esc_attr($index); ?>][sleeve_right]" value="<?php echo esc_url($mockup['sleeve_right']); ?>"></td>
                        <td><button class="button winshirt-remove">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" data-add-row="mockups2d">Ajouter un mockup</button></p>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
