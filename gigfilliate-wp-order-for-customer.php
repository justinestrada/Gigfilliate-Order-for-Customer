<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://partners.vitalibis.com/login
 * @since             1.0.0
 * @package           Gigfilliate_Order_For_Customer
 *
 * @wordpress-plugin
 * Plugin Name:       Gigfilliate Order For Customer
 * Plugin URI:        https://partners.vitalibis.com/login
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Gigfilliate
 * Author URI:        https://partners.vitalibis.com/login
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gigfilliate-order-for-customer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GIGFILLIATE_ORDER_FOR_CUSTOMER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gigfilliate-order-for-customer-activator.php
 */
function activate_gigfilliate_order_for_customer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gigfilliate-order-for-customer-activator.php';
	Gigfilliate_Order_For_Customer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gigfilliate-order-for-customer-deactivator.php
 */
function deactivate_gigfilliate_order_for_customer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gigfilliate-order-for-customer-deactivator.php';
	Gigfilliate_Order_For_Customer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gigfilliate_order_for_customer' );
register_deactivation_hook( __FILE__, 'deactivate_gigfilliate_order_for_customer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gigfilliate-order-for-customer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_gigfilliate_order_for_customer() {

	$plugin = new Gigfilliate_Order_For_Customer();
	$plugin->run();

}
run_gigfilliate_order_for_customer();
