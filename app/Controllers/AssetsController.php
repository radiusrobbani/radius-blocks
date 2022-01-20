<?php

namespace RT\RadiusBlocks\Controllers;

use RT\RadiusBlocks\Helpers\Fns;
use RT\RadiusBlocks\Helpers\Installer;

class AssetsController
{
    private $plugin_name;
    /**
     * @var string
     */
    private $version;

    public function __construct() {
        $this->plugin_name = RTRB_SLUG;
        $this->version = RTRB_VERSION;
    }

    public function init() {
        add_action('wp_enqueue_scripts', [&$this, 'frontend_assets']);
        add_action('admin_enqueue_scripts', [&$this, 'admin_assets']);
        add_action('enqueue_block_editor_assets', [&$this, 'editor_assets']);
        add_action('enqueue_block_assets', [&$this, 'block_assets']);
        add_action('wp_head', [&$this, 'block_attribute_css']);
    }

    public function block_assets() {
        if (is_singular() and has_blocks()) {
            $main_assets_loaded = false;

            $widget_blocks = Fns::get_widget_block_list();
            foreach ($widget_blocks as $block) {
                if (strpos($block['blockName'], 'rtrb/') === 0) {
                    $this->load_assets();
                    $main_assets_loaded = true;
                    break;
                }
            }

            if (!$main_assets_loaded) {
                $presentBlocks = Fns::getPresentBlocks();

                foreach ($presentBlocks as $block) {
                    if (strpos($block['blockName'], 'ub/') === 0) {
                        $this->load_assets();
                        break;
                    }
                }
            }

        } elseif (Fns::is_gutenberg_page()) {
            $this->load_assets();
        }
    }

    public function frontend_assets() {
        $script_dep_path = RTRB_DIR . 'dist/frontend.asset.php';
        $script_info = file_exists($script_dep_path)
            ? include $script_dep_path
            : array(
                'dependencies' => array(),
                'version'      => $this->version,
            );
        $script_dep = array_merge($script_info['dependencies'], array('wp-i18n', 'wp-element', 'wp-api-fetch'));

        // Scripts.
        wp_register_script(
            $this->plugin_name . '-frontend-js',
            radiusBlocks()->get_dist_uri('frontend.js'),
            $script_dep,
            $script_info['version'],
            true
        );
    }

    public function admin_assets() {
        wp_enqueue_style($this->plugin_name, radiusBlocks()->get_assets_uri('css/admin.css'), [], $this->version);
        wp_enqueue_script($this->plugin_name, radiusBlocks()->get_assets_uri('js/admin.js'), ['jquery'], $this->version, false);
    }

    public function editor_assets() {

        // Scripts.
        wp_enqueue_script(
            $this->plugin_name . '-block-editor-js',
            radiusBlocks()->get_dist_uri('blocks.build.js'),
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-api'],
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-deactivator-js',
            radiusBlocks()->get_dist_uri('deactivator.build.js'),
            ['wp-editor', 'wp-blocks', 'wp-i18n', 'wp-element'],
            $this->version,
            true
        );


        wp_enqueue_style(
            $this->plugin_name . '-block-editor-css',
            file_exists(wp_upload_dir()['basedir'] . '/' . $this->plugin_name . '/blocks.editor.build.css') ?
                content_url('/uploads/' . $this->plugin_name . '/blocks.editor.build.css') :
                radiusBlocks()->get_dist_uri('blocks.editor.build.css'),
            ['wp-edit-blocks'],
            $this->version
        );
    }

    public function block_attribute_css() {
        $blockStylesheets = "";

        $presentBlocks = array_unique(array_merge(Fns::getPresentBlocks(), Fns::get_widget_block_list(true)), SORT_REGULAR);
        if (empty($presentBlocks)) {
            return;
        }

        foreach ($presentBlocks as $block) {
            $blockStylesheets .= apply_filters('rtrb_attribute_css_' . $block['blockName'], $blockStylesheets, $block);
        }
        $blockStylesheets = preg_replace('/\s+/', ' ', $blockStylesheets);
        if (!$blockStylesheets) {
            return;
        }
        ob_start(); ?>

        <style><?php echo($blockStylesheets); ?></style>

        <?php
        ob_end_flush();
    }

    private function load_assets() {
        if (file_exists(wp_upload_dir()['basedir'] . '/radius-blocks/blocks.style.build.css') && get_option('rtrb_version') != RTRB_VERSION) {
            $frontStyleFile = fopen(wp_upload_dir()['basedir'] . '/radius-blocks/blocks.style.build.css', 'w');
            $blockDir = RTRB_DIR . 'src/blocks/';
            $activeBlocks = radiusBlocks()->getActiveBlocks();

            foreach ($activeBlocks as $blockName) {
                $blockDirName = strtolower(str_replace(['rtrb/', ' '], ['', '-'], $blockName));
                $frontStyleLocation = $blockDir . $blockDirName . '/style.css';

                if (file_exists($frontStyleLocation)) {
                    fwrite($frontStyleFile, file_get_contents($frontStyleLocation));
                }
            }
            fclose($frontStyleFile);
            Installer::update_version();
        }

        wp_enqueue_style(
            'rtrb_blocks-style-css', // Handle.
            file_exists(wp_upload_dir()['basedir'] . '/radius-blocks/blocks.style.build.css') ?
                content_url('/uploads/radius-blocks/blocks.style.build.css') :
                radiusBlocks()->get_dist_uri('blocks.style.build.css'), // Block style CSS.
            array(),
            $this->version
        );
    }
}
