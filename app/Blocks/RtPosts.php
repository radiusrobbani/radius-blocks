<?php

namespace RT\RadiusBlocks\Blocks;

use RT\RadiusBlocks\Abstracts\Block;

class RtPosts extends Block
{

    protected $name = 'rtrb/posts';

    protected $attributes = [

    ];

    public function __construct() {
        add_action('init', [$this, 'register_block_type']);
        add_filter('rtrb_attribute_css_' . $this->name, [__CLASS__, 'attribute_css', 10, 2]);
    }

    public function register_block_type() {
        register_block_type($this->getName(), [
            'render_callback' => [$this, 'render_callback'],
        ]);
    }

    public static function attribute_css($style, $block) {

        return $style . ' body{color:red}';
    }

    public function render_callback($attrs) {
        $limit = !empty($attrs['limit']) ? absint($attrs['limit']) : 10;

        $post_ids = get_posts([
            'post_per_page' => $limit,
            'fields'        => 'ids'
        ]);

        ob_start();
        ?>
        <h2>Radius post from php</h2>
        <?php
        if (!empty($post_ids)) {
            ?>
            <ul><?php
            foreach ($post_ids as $id) {
                ?>
                <li><?php echo get_the_title($id) ?></li>
                <?php
            }
            ?></ul><?php

        }
        return ob_get_clean();
    }
}