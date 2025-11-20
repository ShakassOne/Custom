<?php
/** @var array $settings */
$theme_color = get_option('winshirt_customizer_settings')['theme_color'] ?? '#3b82f6';
$settings = get_option('winshirt_customizer_settings', []);
$mockups3d = $settings['mockups3d'] ?? [];
?>
<div class="winshirt-customizer" data-theme-color="<?php echo esc_attr($theme_color); ?>">
    <div class="winshirt-topbar">
        <div class="winshirt-actions">
            <button class="winshirt-btn" data-action="undo" aria-label="Annuler">↺</button>
            <button class="winshirt-btn" data-action="redo" aria-label="Rétablir">↻</button>
            <button class="winshirt-btn" data-action="reset" aria-label="Réinitialiser">⟲</button>
        </div>
        <div class="winshirt-faces">
            <button class="winshirt-btn" data-face="front">Recto</button>
            <button class="winshirt-btn" data-face="back">Verso</button>
            <button class="winshirt-btn" data-face="sleeve_left">Manche G</button>
            <button class="winshirt-btn" data-face="sleeve_right">Manche D</button>
        </div>
    </div>
    <div class="winshirt-body">
        <aside class="winshirt-sidebar">
            <div class="winshirt-product-panel">
                <h3>Produit</h3>
                <label for="winshirt-product-select">Sélectionner un mockup 3D</label>
                <div class="winshirt-product-picker">
                    <select id="winshirt-product-select" data-product-select>
                        <?php foreach ($mockups3d as $index => $mockup) : ?>
                            <option value="<?php echo esc_attr($index); ?>"><?php echo esc_html($mockup['name'] ?? ('Mockup ' . ($index + 1))); ?></option>
                        <?php endforeach; ?>
                        <?php if (empty($mockups3d)) : ?>
                            <option value="">Aucun mockup configuré</option>
                        <?php endif; ?>
                    </select>
                    <button class="winshirt-btn" type="button" data-product-apply>Charger</button>
                </div>
                <p class="winshirt-hint">Choisissez un produit pour activer son modèle 3D et ses zones d'impression. Déplacez la souris pour le faire tourner.</p>
            </div>
            <div class="winshirt-tabs">
                <button data-tab="images" class="active">📷 Images</button>
                <button data-tab="svg">🧩 SVG</button>
                <button data-tab="text">🔤 Texte</button>
                <button data-tab="qr">� QR Code</button>
                <button data-tab="ai">🤖 IA</button>
                <button data-tab="layers">📑 Calques</button>
                <button data-tab="export">⬇️ Export</button>
            </div>
            <div class="winshirt-tab-content" data-tab="images">
                <div class="winshirt-upload" data-type="image">
                    <p>Glisser/déposer ou <label class="winshirt-link"><input type="file" accept="image/*">importer</label></p>
                    <div class="winshirt-thumbs" data-gallery="images"></div>
                </div>
            </div>
            <div class="winshirt-tab-content" data-tab="svg" hidden>
                <div class="winshirt-upload" data-type="svg">
                    <p>Upload SVG ou choisir un pictogramme</p>
                    <div class="winshirt-thumbs" data-gallery="svgs"></div>
                    <label>Couleur: <input type="color" data-control="svg-fill" value="#111827"></label>
                    <label>Contour: <input type="color" data-control="svg-stroke" value="#000000"></label>
                </div>
            </div>
            <div class="winshirt-tab-content" data-tab="text" hidden>
                <label>Texte live<br><input type="text" data-control="text-value" placeholder="Votre texte"></label>
                <label>Police<br><select data-control="text-font">
                    <option>Inter</option>
                    <option>Montserrat</option>
                    <option>Poppins</option>
                </select></label>
                <label>Taille<br><input type="range" min="8" max="128" value="42" data-control="text-size"></label>
                <label>Espacement<br><input type="range" min="0" max="20" value="2" data-control="text-spacing"></label>
                <label>Alignement<br>
                    <select data-control="text-align">
                        <option value="left">Gauche</option>
                        <option value="center">Centre</option>
                        <option value="right">Droite</option>
                    </select>
                </label>
                <label>Couleur<br><input type="color" value="#111827" data-control="text-color"></label>
                <label>Contour<br><input type="color" value="#ffffff" data-control="text-stroke"></label>
                <label><input type="checkbox" data-control="text-curve"> Transformer en courbe</label>
            </div>
            <div class="winshirt-tab-content" data-tab="qr" hidden>
                <label>URL<br><input type="url" data-control="qr-url" placeholder="https://"></label>
                <label>Correction d'erreur<br>
                    <select data-control="qr-level">
                        <option value="L">L</option>
                        <option value="M">M</option>
                        <option value="Q">Q</option>
                        <option value="H">H</option>
                    </select>
                </label>
                <button class="winshirt-btn" data-action="generate-qr">Générer QR</button>
            </div>
            <div class="winshirt-tab-content" data-tab="ai" hidden>
                <label>Prompt IA<br><textarea data-control="ai-prompt" rows="4" placeholder="t-shirt futuriste, style cyberpunk"></textarea></label>
                <label>Style<br><select data-control="ai-style"><option>Réalisme</option><option>Flat</option><option>Ligne</option></select></label>
                <button class="winshirt-btn" data-action="generate-ai">Générer</button>
                <p class="winshirt-hint">Le rendu est ajouté directement dans le canvas.</p>
            </div>
            <div class="winshirt-tab-content" data-tab="layers" hidden>
                <div class="winshirt-layer-list" data-layer-list></div>
            </div>
            <div class="winshirt-tab-content" data-tab="export" hidden>
                <button class="winshirt-btn" data-action="export-screen">Export vue écran</button>
                <button class="winshirt-btn" data-action="export-print">Export impression HD</button>
                <button class="winshirt-btn" data-action="download-texture">Télécharger la texture</button>
                <button class="winshirt-btn" data-action="export-3d">Capture 3D</button>
                <button class="winshirt-btn" data-action="add-to-cart">Ajouter au panier</button>
            </div>
        </aside>
        <main class="winshirt-canvas-area">
            <div class="winshirt-print-zone" aria-label="Zone d'impression">
                <canvas id="winshirt-2d-canvas" width="1024" height="1024"></canvas>
                <div class="winshirt-overlay" data-overlay></div>
            </div>
            <div class="winshirt-3d">
                <canvas id="winshirt-3d-canvas"></canvas>
                <div class="winshirt-zoom">Zoom molette / pinch | Rotation drag</div>
            </div>
        </main>
    </div>
    <footer class="winshirt-footer">
        <div class="winshirt-preview" data-preview>Prévisualisation 2D</div>
        <div class="winshirt-export-actions">
            <button class="winshirt-btn" data-action="quick-preview">Aperçu</button>
            <button class="winshirt-btn" data-action="quick-export">Export</button>
            <button class="winshirt-btn primary" data-action="quick-cart">Ajouter au panier</button>
        </div>
    </footer>
    <input type="hidden" name="winshirt_customizer_nonce" value="<?php echo esc_attr(wp_create_nonce('winshirt_customizer_nonce')); ?>">
</div>
