<?php

namespace RT\RadiusBlocks\Helpers;
defined('ABSPATH') || exit;

class Installer
{

    public static function activate() {

        // Check if we are not already running this routine.
        if ('yes' === get_transient('rtrb_installing')) {
            return;
        }

        // If we made it till here nothing is running yet, lets set the transient now.
        set_transient('rtrb_installing', 'yes', MINUTE_IN_SECONDS * 10);


        if (!get_option('rtrb_blocks', false)) {
            update_option('rtrb_blocks', Fns::blocks());
        }

        self::update_version();

        delete_transient('rtrb_installing');
    }

    public static function update_version() {
        update_option('rtrb_version', RTRB_VERSION);
    }

    public static function deactivate() {

    }

}