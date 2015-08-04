<?php
/**
 * Woo ReferCheck - Uninstall
 *
 * @package  Woo_ReferCheck
 * @category Functions
 * @author   Mark Kathmann
 */

// exit if uninstall is not called from withing WordPress
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// remove the custom meta data on all products
delete_metadata( 'product', 1, '_woo_refercheck_checkbox', '', true );
delete_metadata( 'product', 1, '_woo_refercheck_referrers', '', true );
delete_metadata( 'product', 1, '_woo_refercheck_target', '', true );
