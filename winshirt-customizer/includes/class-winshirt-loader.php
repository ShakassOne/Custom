<?php
/**
 * Chargeur principal du plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Winshirt_Customizer_Loader
{
    public static function init(): void
    {
        add_action('init', [__CLASS__, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_front_assets']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
        add_action('admin_menu', [__CLASS__, 'register_admin_pages']);
        add_action('woocommerce_add_cart_item_data', [__CLASS__, 'inject_cart_metadata'], 10, 3);
        add_filter('upload_mimes', [__CLASS__, 'allow_3d_uploads']);
    }

    public static function register_shortcodes(): void
    {
        require_once WINSHIRT_CUSTOMIZER_PATH . 'includes/class-winshirt-frontend.php';
        add_shortcode('winshirt_customizer', ['Winshirt_Customizer_Frontend', 'render_shortcode']);
    }

    public static function enqueue_front_assets(): void
    {
        $version = WINSHIRT_CUSTOMIZER_VERSION;
        $placeholderDataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO2V7r8AAAAASUVORK5CYII=';
        wp_register_style(
            'winshirt-customizer-front',
            WINSHIRT_CUSTOMIZER_URL . 'assets/css/front.css',
            [],
            $version
        );
        wp_register_script(
            'three',
            'https://cdn.jsdelivr.net/npm/three@0.165.0/build/three.min.js',
            [],
            $version,
            true
        );
        wp_register_script(
            'winshirt-customizer-front',
            WINSHIRT_CUSTOMIZER_URL . 'assets/js/front.js',
            ['three'],
            $version,
            true
        );

        wp_localize_script('winshirt-customizer-front', 'WinshirtCustomizerData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('winshirt_customizer_nonce'),
            'placeholderTexture' => $placeholderDataUrl,
            'exportDpi' => (int) get_option('winshirt_customizer_dpi', 300),
        ]);

        wp_enqueue_style('winshirt-customizer-front');
        wp_enqueue_script('winshirt-customizer-front');
    }

    public static function enqueue_admin_assets(string $hook): void
    {
        if (strpos($hook, 'winshirt-customizer') === false) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'three',
            'https://cdn.jsdelivr.net/npm/three@0.165.0/build/three.min.js',
            [],
            WINSHIRT_CUSTOMIZER_VERSION,
            true
        );

        wp_enqueue_style(
            'winshirt-customizer-admin',
            WINSHIRT_CUSTOMIZER_URL . 'assets/css/admin.css',
            [],
            WINSHIRT_CUSTOMIZER_VERSION
        );
        wp_enqueue_script(
            'winshirt-customizer-admin',
            WINSHIRT_CUSTOMIZER_URL . 'assets/js/admin.js',
            ['jquery', 'three'],
            WINSHIRT_CUSTOMIZER_VERSION,
            true
        );

        wp_localize_script('winshirt-customizer-admin', 'WinshirtCustomizerAdmin', [
            'nonce' => wp_create_nonce('winshirt_customizer_admin_nonce'),
        ]);
    }

    public static function register_admin_pages(): void
    {
        require_once WINSHIRT_CUSTOMIZER_PATH . 'includes/class-winshirt-admin.php';
        $admin = new Winshirt_Customizer_Admin();
        $admin->register_menu();
    }

    public static function inject_cart_metadata(array $cartItemData, $productId, $request): array
    {
        if (!isset($_POST['winshirt_customizer_payload'])) {
            return $cartItemData;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['winshirt_customizer_nonce'] ?? ''));
        if (!wp_verify_nonce($nonce, 'winshirt_customizer_nonce')) {
            return $cartItemData;
        }

        $payload = json_decode(stripslashes($_POST['winshirt_customizer_payload']), true);
        if (!$payload) {
            return $cartItemData;
        }

        $cartItemData['winshirt_customizer'] = [
            'preview' => esc_url_raw($payload['preview'] ?? ''),
            'hd_file' => esc_url_raw($payload['hd_file'] ?? ''),
            'layers' => $payload['layers'] ?? [],
            'zone' => sanitize_text_field($payload['zone'] ?? ''),
        ];

        return $cartItemData;
    }

    public static function allow_3d_uploads(array $mimes): array
    {
        $mimes['glb'] = 'model/gltf-binary';
        $mimes['gltf'] = 'model/gltf+json';
        $mimes['obj'] = 'model/obj';

        return $mimes;
    }
}
