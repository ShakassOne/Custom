<div class="wrap winshirt-admin">
    <h1>Tarifs</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <?php $pricingRows = !empty($settings['pricing']) ? $settings['pricing'] : [['zone' => '', 'price' => '', 'ai' => '', 'qr' => '']]; ?>
        <table class="widefat winshirt-table" data-repeatable="pricing">
            <thead><tr><th>Zone</th><th>Prix base</th><th>Option IA</th><th>Option QR</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($pricingRows as $index => $pricing) : ?>
                    <tr>
                        <td><input type="text" name="winshirt_customizer_settings[pricing][<?php echo esc_attr($index); ?>][zone]" value="<?php echo esc_attr($pricing['zone']); ?>" placeholder="A3 / A4 / custom"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[pricing][<?php echo esc_attr($index); ?>][price]" value="<?php echo esc_attr($pricing['price']); ?>"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[pricing][<?php echo esc_attr($index); ?>][ai]" value="<?php echo esc_attr($pricing['ai']); ?>"></td>
                        <td><input type="number" step="0.01" name="winshirt_customizer_settings[pricing][<?php echo esc_attr($index); ?>][qr]" value="<?php echo esc_attr($pricing['qr']); ?>"></td>
                        <td><button class="button winshirt-remove">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" data-add-row="pricing">Ajouter un tarif</button></p>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
