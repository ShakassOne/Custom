<?php
/**
 * Gestion du front-office et du shortcode.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Winshirt_Customizer_Frontend
{
    public static function render_shortcode(): string
    {
        ob_start();
        include WINSHIRT_CUSTOMIZER_PATH . 'templates/front.php';
        return ob_get_clean();
    }
}
