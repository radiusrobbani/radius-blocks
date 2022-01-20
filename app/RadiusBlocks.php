<?php

require_once __DIR__ . './../vendor/autoload.php';

defined('ABSPATH') || exit;

use RT\RadiusBlocks\Abstracts\Block;
use RT\RadiusBlocks\Controllers\AdminAjaxController;
use RT\RadiusBlocks\Helpers\Fns;
use RT\RadiusBlocks\Controllers\AssetsController;
use RT\RadiusBlocks\Helpers\Installer;
use RT\RadiusBlocks\Models\Dependencies;

final class RadiusBlocks
{
    const PLUGIN_NAME = 'radius-blocks';
    /**
     * Store the singleton object.
     */
    private static $singleton = false;


    /**
     * Create an inaccessible constructor.
     */
    private function __construct() {
        $this->define_constants();
        $dependence = Dependencies::getInstance();
        //if ($dependence->check()) {
        new AdminAjaxController();
        $assets = new AssetsController();
        $assets->init();
        $this->init_hooks();
        //}
    }

    /**
     * Fetch an instance of the class.
     */
    final public static function getInstance() {
        if (false === self::$singleton) {
            self::$singleton = new self();
        }

        return self::$singleton;
    }

    public function on_plugins_loaded() {
        do_action('wp_blocks_loaded');
    }

    public function init() {
        $this->load_language();
        // Other hooks which is need to run all first
    }

    private function init_hooks() {

        register_activation_hook(RTRB_FILE, [Installer::class, 'activate']);
        register_deactivation_hook(RTRB_FILE, [Installer::class, 'deactivate']);
        register_shutdown_function([$this, 'log_errors']);
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), -1);
        add_action('init', array($this, 'init'), 0);
    }

    public function register_blocks() {
        if (function_exists('register_block_type')) {
            $blocks = Fns::blocks();
            if (!empty($blocks) && is_array($blocks)) {
                foreach ($blocks as $block) {
                    if (empty($block['class'])) {
                        return;
                    }
                    if (is_string($block['class'])) {
                        new $block['class']();
                    }
                }
            }
        }
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function get_assets_uri($file) {
        $file = ltrim($file, '/');

        return trailingslashit(RTRB_URL . '/assets') . $file;
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function get_dist_uri($file) {
        $file = ltrim($file, '/');

        return trailingslashit(RTRB_URL . '/dist') . $file;
    }

    /**
     * Get Plugin name
     *
     * @return string
     */
    public function plugin_name() {
        return self::PLUGIN_NAME;
    }

    private function define_constants() {
        $this->define('RTRB_DIR', plugin_dir_path(RTRB_FILE));
        $this->define('RTRB_URL', plugins_url('', RTRB_FILE));
        $this->define('RTRB_SLUG', basename(dirname(RTRB_FILE)));
    }

    private function load_language() {
        $locale = determine_locale();
        $locale = apply_filters('plugin_locale', $locale, RTRB_SLUG);
        unload_textdomain(RTRB_SLUG);
        load_textdomain(RTRB_SLUG, WP_LANG_DIR . '/' . RTRB_SLUG . '/' . RTRB_SLUG . '-' . $locale . '.mo');
        load_plugin_textdomain(RTRB_SLUG, false, plugin_basename(dirname(RTRB_FILE)) . '/languages');
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  constant name
     * @param bool|string $value constant value
     */
    public function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    public function log_errors() {
        $error = error_get_last();
        do_action('rtrb_shutdown_error', $error);
    }

    /**
     * @return mixed
     */
    public function getActiveBlocks() {
        $active_blocks = get_option('rtrb_blocks', []);
        if (!is_array($active_blocks)) {
            $active_blocks = [];
        }
        return apply_filters('rtrb_active_blocks', $active_blocks);
    }

    public function saveActiveBlocks($blocks) {
        $blocks = array_unique($blocks);
        update_option('rtrb_blocks', $blocks);
    }
}

/**
 * @return bool|RadiusBlocks
 */
function radiusBlocks() {
    return RadiusBlocks::getInstance();
}

radiusBlocks();
