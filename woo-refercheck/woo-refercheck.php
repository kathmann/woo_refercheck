<?php
/**
 * Plugin Name: Woo ReferCheck
 * Plugin URI: https://github.com/kathmann/woo_refercheck
 * Description: A plugin for WooCommerce to enable filtering product display based on the referrer.
 * Version: 1.0.0
 * Author: Mark Kathmann
 * Author URI: http://stackedbits.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Requires at least: 3.8
 * Tested up to: 4.2.3
 *
 * Text Domain: woo-refercheck
 * Domain Path: /languages
 *
 * @package  Woo_ReferCheck
 * @category Plugin
 * @author   Mark Kathmann
 */
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// exit if this file is accessed directly
if ( !defined('ABSPATH') ) {
    exit;
}

// read the plugin paths
define( 'WCRC_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCRC_URL', plugin_dir_url( __FILE__ ) );

// check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :

    class Woo_ReferCheck {
        /**
         * Construct the plugin
         */
        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'init' ) );
        }

        /**
         * Initialise the plugin
         */
        public function init() {
            // load the language file(s)
            load_plugin_textdomain( 'woo-refercheck', false, basename( dirname( __FILE__ ) ) . '/languages/' );

            // include the functions file
            include_once( WCRC_PATH . 'includes/functions.php' );

            // display the custom WCRC fields on every product edit page
            add_action( 'woo_product_options_general_product_data', 'woo_refercheck_add_custom_general_fields' );

            // save the custom WCRC fields with the product
            add_action( 'woo_process_product_meta', 'woo_refercheck_add_custom_general_fields_save' );

            // hook the main check to the template redirection
            add_action( 'template_redirect', 'woo_refercheck_product_filter' );
        }
    }

    // instantiate the plugin object
    $Woo_ReferCheck = new Woo_ReferCheck( __FILE__ );

endif;
