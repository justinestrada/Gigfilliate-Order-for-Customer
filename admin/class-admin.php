<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://partners.vitalibis.com/login
 * @since      0.0.1
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/admin
 * @author     Gigfilliate <justin@justinestrada.com>
 */
class Gigfilliate_Order_For_Customer_Admin {

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
    add_action('admin_menu', [$this,'admin_menu'],20);
    add_action('vitalibis_settings_dashboard_row', [$this,'vitalibis_settings_dashboard_row'], 10, 2 );
    add_action("on_save_vitalibis_settings_dashboard_row",[$this,"on_save_vitalibis_settings_dashboard_row"],10,2);
  }

  public function admin_menu()
	{
    add_submenu_page('vitalibis', 'Order For Customer', 'Order For Customer', 'manage_options', '/admin.php?page=vitalibis&tab=settings&status_view=dashboard');
	}

  public function vitalibis_settings_dashboard_row($settings){
    ?>
      <tr>
        <th><label for="excluded_product_ids_from_order_for_customer">Excluded Products From Order For Customer</label></th>
        <td>
          <input type="text" id="excluded_product_ids_from_order_for_customer" name="excluded_product_ids_from_order_for_customer" class="large-text" value="<?php echo (isset($_POST['excluded_product_ids_from_order_for_customer'])) ? $_POST['excluded_product_ids_from_order_for_customer'] : (isset($settings->dashboard->excluded_product_ids_from_order_for_customer) ?$settings->dashboard->excluded_product_ids_from_order_for_customer:''); ?>"/>
          <p class="description">You can add ids of products here seprated by (,) . If you dont want them to be visible in orders for customer section.</p>
        </td>
      </tr>
    <?php
  }

  public function on_save_vitalibis_settings_dashboard_row($settings){
    $settings->dashboard->excluded_product_ids_from_order_for_customer = isset($_POST['excluded_product_ids_from_order_for_customer']) ? $_POST['excluded_product_ids_from_order_for_customer'] : '';
  }
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
	}

}
