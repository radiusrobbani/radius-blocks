<?php

namespace RT\RadiusBlocks\Controllers;

use RT\RadiusBlocks\Helpers\Fns;

class AdminAjaxController
{
    public function __construct() {
        add_action('wp_ajax_nopriv_rtrb_change_block_status', [&$this, 'change_block_status']);
    }


    public function change_block_status() {
        // TODO Need to check ajax referer && nonce
        $block_name = isset($_POST['block_name']) ? sanitize_text_field($_POST['block_name']) : null;

        $enable = isset($_POST['enable']) ? sanitize_text_field($_POST['enable']) : null;

        if (!$block_name || !Fns::block_exists($block_name)) {
            wp_send_json_error(array(
                'error_message' => 'Unknown block name',
            ));
        }

        $uploadDir = wp_upload_dir()['basedir'];
        $canMakeCustomFile = is_writable($uploadDir);

        $saved_blocks = radiusBlocks()->getActiveBlocks();
        $styleNeedToGenerate = false;
        if ($enable) {
            if (!in_array($block_name, $saved_blocks)) {
                array_push($saved_blocks, $block_name);
                $styleNeedToGenerate = true;
            }
        } else if (!empty($saved_blocks)) {
            $flip_blocks = array_flip($saved_blocks);
            if (isset($flip_blocks[$block_name])) {
                unset($saved_blocks[$flip_blocks[$block_name]]);
                $styleNeedToGenerate = true;
            }
        }
        if ($styleNeedToGenerate && !empty($saved_blocks)) {
            error_log('saved' . print_r($saved_blocks, true));
            if ($canMakeCustomFile) {
                if (!file_exists($uploadDir . '/radius-blocks')) {
                    mkdir($uploadDir . '/radius-blocks');
                }
                $frontStyleFile = fopen($uploadDir . '/radius-blocks/blocks.style.build.css', 'w');
//                $adminStyleFile = fopen($uploadDir . '/radius-blocks/blocks.editor.build.css', 'w');
                $blockDir = RTRB_DIR . 'src/blocks/';
            }

            foreach ($saved_blocks as $blockName) {

                if ($canMakeCustomFile) {
                    $blockDirName = strtolower(str_replace(['rtrb/', ' '], ['', '-'], $blockName));
                    $frontStyleLocation = $blockDir . $blockDirName . '/style.css';
//                    $adminStyleLocation = $blockDir . $blockDirName . '/editor.css';
                    if (file_exists($frontStyleLocation)) {
                        fwrite($frontStyleFile, file_get_contents($frontStyleLocation));
                    }
//                    if (file_exists($adminStyleLocation)) {
//                        fwrite($adminStyleFile, file_get_contents($adminStyleLocation));
//                    }
                }
            }

            if ($canMakeCustomFile) {
                fclose($frontStyleFile);
//                fclose($adminStyleFile);
            }
        }

        radiusBlocks()->saveActiveBlocks($saved_blocks);

        wp_send_json_success($saved_blocks);
    }
}