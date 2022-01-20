<?php

/**
 * @package radius-blocks
 * Plugin Name: Radius Blocks
 * Plugin URI:
 * Description:
 * Author: RadiusTheme
 * Author URI: https://radiustheme.com
 * Version: 0.0.1
 * License: GPL3+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: radius-blocks
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

defined('ABSPATH') || exit('Keep Silent');

// Define WP_BLOCKS_PLUGIN_FILE.
define('RTRB_VERSION', '0.0.1');
define('RTRB_FILE', __FILE__);

if (!class_exists('RadiusBlocks')) {
    require_once 'app/RadiusBlocks.php';
}
