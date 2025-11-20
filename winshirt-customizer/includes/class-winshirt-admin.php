<?php
/**
 * Gestion du back-office.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Winshirt_Customizer_Admin
{
    private string $optionKey = 'winshirt_customizer_settings';

    public function register_menu(): void
    {
        add_menu_page(
            __('WinShirt Customizer', 'winshirt-customizer'),
            __('WinShirt Customizer', 'winshirt-customizer'),
            'manage_options',
            'winshirt-customizer',
            [$this, 'render_mockups_3d'],
            'dashicons-art'
        );

        add_submenu_page('winshirt-customizer', __('Mockups 3D', 'winshirt-customizer'), __('Mockups 3D', 'winshirt-customizer'), 'manage_options', 'winshirt-customizer', [$this, 'render_mockups_3d']);
        add_submenu_page('winshirt-customizer', __('Mockups 2D', 'winshirt-customizer'), __('Mockups 2D', 'winshirt-customizer'), 'manage_options', 'winshirt-customizer-2d', [$this, 'render_mockups_2d']);
        add_submenu_page('winshirt-customizer', __('Zones d\'impression', 'winshirt-customizer'), __('Zones d\'impression', 'winshirt-customizer'), 'manage_options', 'winshirt-customizer-zones', [$this, 'render_zones']);
        add_submenu_page('winshirt-customizer', __('Tarifs', 'winshirt-customizer'), __('Tarifs', 'winshirt-customizer'), 'manage_options', 'winshirt-customizer-pricing', [$this, 'render_pricing']);
        add_submenu_page('winshirt-customizer', __('Options globales', 'winshirt-customizer'), __('Options globales', 'winshirt-customizer'), 'manage_options', 'winshirt-customizer-settings', [$this, 'render_settings']);
    }

    private function get_settings(): array
    {
        $defaults = [
            'dpi' => 300,
            'theme_color' => '#3b82f6',
            'enable_ai' => true,
            'enable_qr' => true,
            'fallback_2d' => false,
            'pricing' => [],
            'zones' => [],
            'mockups3d' => [],
            'mockups2d' => [],
        ];
        return wp_parse_args(get_option($this->optionKey, []), $defaults);
    }

    private function save_settings(array $settings): void
    {
        update_option($this->optionKey, $settings);
    }

    public function render_mockups_3d(): void
    {
        $settings = $this->get_settings();
        if (isset($_POST['winshirt_customizer_settings'])) {
            check_admin_referer('winshirt_customizer_settings');
            $settings['mockups3d'] = array_values(array_map(function ($item) {
                $file = $item['file'] ?? $item['front'] ?? '';
                $zones = json_decode(wp_unslash($item['zones'] ?? '[]'), true);

                if (!is_array($zones)) {
                    $zones = [];
                }

                $zones = array_values(array_map(function ($zone) {
                    return [
                        'id' => sanitize_text_field($zone['id'] ?? ($zone['label'] ?? '')),
                        'label' => sanitize_text_field($zone['label'] ?? ($zone['id'] ?? '')),
                        'x' => max(0, min(1, floatval($zone['x'] ?? 0))),
                        'y' => max(0, min(1, floatval($zone['y'] ?? 0))),
                        'width' => max(0.05, min(1, floatval($zone['width'] ?? 0.25))),
                        'height' => max(0.05, min(1, floatval($zone['height'] ?? 0.25))),
                        'face' => sanitize_text_field($zone['face'] ?? ''),
                    ];
                }, $zones));

                return [
                    'name' => sanitize_text_field($item['name'] ?? ''),
                    'file' => esc_url_raw($file),
                    'texture' => esc_url_raw($item['texture'] ?? ''),
                    'zones' => $zones,
                ];
            }, $_POST['winshirt_customizer_settings']['mockups3d'] ?? []));
            $this->save_settings($settings);
            add_settings_error('winshirt_customizer', 'saved', __('Mockups 3D mis à jour.', 'winshirt-customizer'), 'updated');
        }

        include WINSHIRT_CUSTOMIZER_PATH . 'templates/admin-mockups-3d.php';
    }

    public function render_mockups_2d(): void
    {
        $settings = $this->get_settings();
        if (isset($_POST['winshirt_customizer_settings'])) {
            check_admin_referer('winshirt_customizer_settings');
            $settings['mockups2d'] = array_values(array_map(fn($item) => [
                'name' => sanitize_text_field($item['name'] ?? ''),
                'front' => esc_url_raw($item['front'] ?? ''),
                'back' => esc_url_raw($item['back'] ?? ''),
                'sleeve_left' => esc_url_raw($item['sleeve_left'] ?? ''),
                'sleeve_right' => esc_url_raw($item['sleeve_right'] ?? ''),
            ], $_POST['winshirt_customizer_settings']['mockups2d'] ?? []));
            $this->save_settings($settings);
            add_settings_error('winshirt_customizer', 'saved', __('Mockups 2D mis à jour.', 'winshirt-customizer'), 'updated');
        }

        include WINSHIRT_CUSTOMIZER_PATH . 'templates/admin-mockups-2d.php';
    }

    public function render_zones(): void
    {
        $settings = $this->get_settings();
        if (isset($_POST['winshirt_customizer_settings'])) {
            check_admin_referer('winshirt_customizer_settings');
            $settings['zones'] = array_values(array_map(fn($item) => [
                'name' => sanitize_text_field($item['name'] ?? ''),
                'width' => floatval($item['width'] ?? 0),
                'height' => floatval($item['height'] ?? 0),
                'pos_x' => floatval($item['pos_x'] ?? 0),
                'pos_y' => floatval($item['pos_y'] ?? 0),
                'face' => sanitize_text_field($item['face'] ?? 'front'),
                'price' => floatval($item['price'] ?? 0),
                'order' => intval($item['order'] ?? 0),
                'active' => !empty($item['active']),
            ], $_POST['winshirt_customizer_settings']['zones'] ?? []));
            $this->save_settings($settings);
            add_settings_error('winshirt_customizer', 'saved', __('Zones mises à jour.', 'winshirt-customizer'), 'updated');
        }
        include WINSHIRT_CUSTOMIZER_PATH . 'templates/admin-zones.php';
    }

    public function render_pricing(): void
    {
        $settings = $this->get_settings();
        if (isset($_POST['winshirt_customizer_settings'])) {
            check_admin_referer('winshirt_customizer_settings');
            $settings['pricing'] = array_values(array_map(fn($item) => [
                'zone' => sanitize_text_field($item['zone'] ?? ''),
                'price' => floatval($item['price'] ?? 0),
                'ai' => floatval($item['ai'] ?? 0),
                'qr' => floatval($item['qr'] ?? 0),
            ], $_POST['winshirt_customizer_settings']['pricing'] ?? []));
            $this->save_settings($settings);
            add_settings_error('winshirt_customizer', 'saved', __('Tarifs mis à jour.', 'winshirt-customizer'), 'updated');
        }
        include WINSHIRT_CUSTOMIZER_PATH . 'templates/admin-pricing.php';
    }

    public function render_settings(): void
    {
        $settings = $this->get_settings();
        if (isset($_POST['winshirt_customizer_settings'])) {
            check_admin_referer('winshirt_customizer_settings');
            $settings['theme_color'] = sanitize_hex_color($_POST['winshirt_customizer_settings']['theme_color'] ?? '#3b82f6');
            $settings['dpi'] = max(72, intval($_POST['winshirt_customizer_settings']['dpi'] ?? 300));
            $settings['enable_ai'] = !empty($_POST['winshirt_customizer_settings']['enable_ai']);
            $settings['enable_qr'] = !empty($_POST['winshirt_customizer_settings']['enable_qr']);
            $settings['fallback_2d'] = !empty($_POST['winshirt_customizer_settings']['fallback_2d']);
            $this->save_settings($settings);
            update_option('winshirt_customizer_dpi', $settings['dpi']);
            add_settings_error('winshirt_customizer', 'saved', __('Options globales mises à jour.', 'winshirt-customizer'), 'updated');
        }
        include WINSHIRT_CUSTOMIZER_PATH . 'templates/admin-settings.php';
    }
}
