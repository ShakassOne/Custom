<?php
/**
 * Plugin Name: WinShirt 3D Customizer
 * Description: Configurateur 3D avancé pour WooCommerce avec gestion des zones d'impression, exports HD et intégration complète front/back-office.
 * Version: 1.0.3
 * Author: OpenAI Assistant
 * Text Domain: winshirt-customizer
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WINSHIRT_CUSTOMIZER_VERSION', '1.0.3');
define('WINSHIRT_CUSTOMIZER_PATH', plugin_dir_path(__FILE__));
define('WINSHIRT_CUSTOMIZER_URL', plugin_dir_url(__FILE__));

require_once WINSHIRT_CUSTOMIZER_PATH . 'includes/class-winshirt-loader.php';

Winshirt_Customizer_Loader::init();
