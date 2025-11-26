<?php
/**
 * Fired during plugin deactivation
 *
 * @package    P5FAQ
 * @subpackage P5FAQ/includes
 */

class P5FAQ_Deactivator {

    /**
     * Plugin deactivation logic
     *
     * Flushes rewrite rules to clean up.
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
