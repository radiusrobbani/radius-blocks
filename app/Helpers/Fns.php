<?php

namespace RT\RadiusBlocks\Helpers;


use RT\RadiusBlocks\Blocks\RtPostReact;
use RT\RadiusBlocks\Blocks\RtPosts;
use RT\RadiusBlocks\Blocks\TitleBlock;

class Fns
{
    public static function blocks() {

        $blocks = [
            array(
                'label' => 'RT Posts',
                'name'  => 'rtrb/posts',
                'class' => RtPosts::class,
            ),
            array(
                'label' => 'RT React Posts',
                'name'  => 'rtrb/postsreact',
                'class' => RtPostReact::class,
            ),
            array(
                'label' => 'RT Title',
                'name'  => 'rtrb/title',
                'class' => TitleBlock::class
            )
        ];

        return apply_filters('rtrb_blocks', $blocks);
    }


    /**
     * Check block exists.
     *
     * @param string $name Block Name.
     *
     * @return bool
     * @since    1.0.2
     */
    public static function block_exists($name) {
        $blocks = self::blocks();

        foreach ($blocks as $block) {
            if ($block['name'] === $name) {
                return true;
            }
        }
        return false;
    }

    public static function is_gutenberg_page() {

        // The Gutenberg plugin is on.
        if (function_exists('is_gutenberg_page') && is_gutenberg_page()) {
            return true;
        }

        // Gutenberg page on WordPress 5+.
        $current_screen = get_current_screen();
        if ($current_screen !== NULL && method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
            return true;
        }

        return false;

    }

    public static function checkInnerBlocks($block) {
        $currentBlocks = [];

        if( strpos($block['blockName'], 'rt-radius-blocks/' ) !== 0){
            return $currentBlocks;
        }

        $current = $block;

        if ($block['blockName'] == 'core/block') { //reusable block
            $current = parse_blocks(get_post_field('post_content', $block['attrs']['ref']))[0];
        }

        if ($current['blockName'] != '') {
            array_push($currentBlocks, $current);
            if (count($current['innerBlocks']) > 0) {
                foreach ($current['innerBlocks'] as $innerBlock) {
                    self::checkInnerBlocks($innerBlock);
                }
            }
        }
        return $currentBlocks;
    }

    public static function getPresentBlocks($block_array = null) {
        $presentBlocks = [];

        $post_array = get_post();

        if ($post_array || $block_array) {
            foreach (parse_blocks($block_array ?: $post_array->post_content) as $block) {
                $presentBlocks = self::checkInnerBlocks($block);
            }
        }

        return $presentBlocks;
    }

    public static function get_widget_block_list() {
        $blockList = array();
        $widget_elements = get_option('widget_block');
        foreach ((array)$widget_elements as $widget_element) {
            if (!empty($widget_element['content'])) {

                $widget_blocks = self::getPresentBlocks($widget_element['content']);

                foreach ($widget_blocks as $block) {
                    $blockList[] = $block;
                }
            }
        }
        return $blockList;
    }

}
