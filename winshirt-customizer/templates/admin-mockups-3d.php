<div class="wrap winshirt-admin" data-available-zones="<?php echo esc_attr(wp_json_encode($settings['zones'] ?? [])); ?>">
    <h1>Mockups 3D</h1>
    <?php settings_errors('winshirt_customizer'); ?>
    <form method="post">
        <?php wp_nonce_field('winshirt_customizer_settings'); ?>
        <?php $mockups = !empty($settings['mockups3d']) ? $settings['mockups3d'] : [['name' => '', 'file' => '', 'texture' => '', 'zones' => []]]; ?>
        <table class="widefat winshirt-table" data-repeatable="mockups3d">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Fichier 3D (GLB/OBJ)</th>
                    <th>Texture défaut</th>
                    <th>Aperçu 3D & zones</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mockups as $index => $mockup) : ?>
                    <tr>
                        <td><input type="text" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($mockup['name'] ?? ''); ?>"></td>
                        <td class="winshirt-3d-file">
                            <input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][file]" value="<?php echo esc_url($mockup['file'] ?? ''); ?>" placeholder="URL GLB/GLTF/OBJ">
                            <button class="button winshirt-upload-3d" type="button">Choisir dans la médiathèque</button>
                        </td>
                        <td><input type="url" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][texture]" value="<?php echo esc_url($mockup['texture'] ?? ''); ?>" placeholder="URL PNG"></td>
                        <td class="winshirt-preview-cell">
                            <div class="winshirt-3d-config" data-zones="<?php echo esc_attr(wp_json_encode($mockup['zones'] ?? [])); ?>">
                                <div class="winshirt-3d-viewer">
                                    <canvas class="winshirt-3d-canvas" width="360" height="220"></canvas>
                                    <div class="winshirt-zone-overlay" aria-label="Zones d'impression" data-zone-overlay></div>
                                </div>
                                <div class="winshirt-3d-tools">
                                    <label>Zones disponibles</label>
                                    <div class="winshirt-zone-picker">
                                        <select data-zone-select>
                                            <option value="">Sélectionner une zone</option>
                                            <?php foreach (($settings['zones'] ?? []) as $zone) : ?>
                                                <?php if (empty($zone['active'])) { continue; } ?>
                                                <option value="<?php echo esc_attr($zone['name']); ?>"><?php echo esc_html($zone['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="button" data-add-zone type="button">Ajouter</button>
                                    </div>
                                    <p class="description">Glissez-déposez les cadres pour positionner les zones.</p>
                                    <div class="winshirt-zone-list" data-zone-list></div>
                                    <input type="hidden" name="winshirt_customizer_settings[mockups3d][<?php echo esc_attr($index); ?>][zones]" data-zone-input value="<?php echo esc_attr(wp_json_encode($mockup['zones'] ?? [])); ?>">
                                </div>
                            </div>
                        </td>
                        <td><button class="button winshirt-remove">Supprimer</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" data-add-row="mockups3d">Ajouter un mockup</button></p>
        <p><button class="button-primary">Enregistrer</button></p>
    </form>
</div>
